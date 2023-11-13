<?php
if (!defined('ABSPATH')) exit;

/**
 * Plugin Name: DevtasksUp
 * Plugin URI:
 * Description: The plugin integrates ClickUp into the admin for streamlined task management. Simply add an API key for full access to create tasks, leave comments, and view task priority. Ideal for developers to set up for clients for seamless task delegation.
 * Author: Martin Valchev
 * Author URI: https://martinvalchev.com/
 * Version: 1.2.1
 * Text Domain: dev-tasks-up
 * Domain Path: /languages
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Define constants
 *
 * @since 1.1.1
 */
if ( ! defined( 'DVT_VERSION_NUM' ) ) 		    define( 'DVT_VERSION_NUM'		, '1.2.1' ); // Plugin version constant
if ( ! defined( 'DVT_STARTER_PLUGIN' ) )		define( 'DVT_STARTER_PLUGIN'		, trim( dirname( plugin_basename( __FILE__ ) ), '/' ) ); // Name of the plugin folder eg - 'dev-tasks-up'
if ( ! defined( 'DVT_STARTER_PLUGIN_DIR' ) )	define( 'DVT_STARTER_PLUGIN_DIR'	, plugin_dir_path( __FILE__ ) ); // Plugin directory absolute path with the trailing slash. Useful for using with includes eg - /var/www/html/wp-content/plugins/dev-tasks-up/
if ( ! defined( 'DVT_STARTER_PLUGIN_URL' ) )	define( 'DVT_STARTER_PLUGIN_URL'	, plugin_dir_url( __FILE__ ) ); // URL to the plugin folder with the trailing slash. Useful for referencing src eg - http://localhost/wp/wp-content/plugins/dev-tasks-up/
if ( ! defined( 'DVT_PLUGIN_NAME' ) )	        define( 'DVT_PLUGIN_NAME'	        , get_file_data(__FILE__, ['Plugin Name'], false)[0] ); // Name plugin - 'DevtasksUp'

// Load basic setup. Plugin list links, text domain, footer links etc.
require_once( DVT_STARTER_PLUGIN_DIR . 'basic-setup.php' );

// Load functions for Task Center
require_once( DVT_STARTER_PLUGIN_DIR . 'task-center.php' );


class DevTasksIntegration
{
    /**
     * Construct
     *
     * @since 1.2.0
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'adminMenu'));
        // add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('admin_enqueue_scripts', array($this, 'dev_tasks_admin_styles'));
        add_action('admin_post', array($this, 'save'));
        add_action('devt_createWorkspace', array($this, 'createWorkspace'));
        add_action('dev_task_up_inNewWorkspaceCreateFolder', array($this, 'inNewWorkspaceCreateFolder'));
        add_action('dev_task_up_inNewWorkspaceFolderCreateList', array($this, 'inNewWorkspaceFolderCreateList'));
        add_action('dev_task_up_getWorkspaces', array($this, 'getWorkspaces'));
        add_action('admin_notices', array($this, 'errorNotices'));
        add_action( 'init',  array($this, 'dev_task_up_start_session' ));
        add_action( 'admin_footer-plugins.php',  array($this, 'dvt_feedback_dialog' ));
        add_action( 'init',  array($this, 'dvt_capability' ));

        add_action('wp_ajax_select_workspace', array($this, 'selectWorkspace'));
        add_action('wp_ajax_nopriv_select_workspace',  array($this, 'selectWorkspace'));
        add_action('wp_ajax_select_folder', array($this, 'selectFolder'));
        add_action('wp_ajax_nopriv_select_folder',  array($this, 'selectFolder'));
        add_action('wp_ajax_dvt_send_deactivation_feedback_email', array($this, 'dvt_send_deactivation_email'));
        add_action('wp_ajax_nopriv_dvt_send_deactivation_feedback_email',  array($this, 'dvt_send_deactivation_email'));

        // add_shortcode('dev-tasks-plugin', array($this, 'shortcodeAction'));
    }

    /**
     * Create capability for administrator
     *
     * @since 1.2.0
     */
    public function dvt_capability() {
        $role = get_role('administrator');
        $role->add_cap('dev_tasks_up_admin_capability');
    }


    /**
     * Admin menu added
     *
     * @since 1.2.0
     */
    public function adminMenu()
    {
        global $submenu;
        add_menu_page(
            __( 'DevtasksUp', 'dev-tasks-up' ),
            'DevtasksUp',
            'manage_options',
            'dev-tasks-admin-page',
            array($this, 'renderPage'),
            'dashicons-editor-code',
            10,
        );

        if( current_user_can('dev_tasks_up_admin_capability') ) {
            add_submenu_page("dev-tasks-admin-page",  __( 'Settings', 'dev-tasks-up' ), __( 'Settings', 'dev-tasks-up' ), 'manage_options', "dev-tasks-settings", array($this, 'renderPageSettings'));
            $submenu['dev-tasks-admin-page'][0][0] = __( 'Task Center', 'dev-tasks-up' );
        }

    }

    /**
     * Render page
     *
     * @since 1.0.0
     */
    public function renderPage()
    {
        include_once 'views/admin-page.php';
    }

    /**
     * Admin page settings render
     *
     * @since 1.0.0
     */
    public function renderPageSettings()
    {
        include_once 'views/admin-page-settings.php';
    }


    /**
     * Add Admin scripts and styles
     *
     * @since 1.1.2
     */
    public function dev_tasks_admin_styles($hook) {

        $screen = get_current_screen();
        
        if( $screen->base == 'toplevel_page_dev-tasks-admin-page' ||  $screen->base == 'devtasksup_page_dev-tasks-settings') {
           wp_enqueue_style( 'bootstrap_admin_styles', plugins_url( 'assets/bootstrap-5.2.1/css/bootstrap.min.css', __FILE__ ) );
           wp_enqueue_style( 'dev_tasks_admin_fontawesome_styles', plugins_url( 'assets/fontawesome5/css/all.min.css', __FILE__ ) );
           wp_enqueue_style( 'dev_tasks_admin_styles', plugins_url( 'assets/admin-style.css', __FILE__ ) );
           wp_enqueue_style( 'select2', plugins_url( 'assets/select2/select2.min.css', __FILE__ ) );

           wp_enqueue_script( 'bootstrap-admin-js', plugins_url( 'assets/bootstrap-5.2.1/js/bootstrap.bundle.min.js', __FILE__ ) );
           wp_enqueue_script( 'sweetalert2-js', plugins_url( 'assets/sweetalert2/sweetalert2.all.min.js', __FILE__ ) );
           wp_enqueue_script( 'momentjs', includes_url() . '/js/dist/vendor/moment.js', array(), '', true );
           wp_enqueue_script( 'dev-tasks-js', plugins_url( 'assets/js/main.js', __FILE__ ), '', DVT_VERSION_NUM, true );
           wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . 'assets/select2/select2.min.js', array( 'jquery' ), '', true );
           wp_enqueue_script( 'select2' );

        }

        if ($hook == "plugins.php") {
            wp_enqueue_script( 'dvt-feedback', plugins_url( 'assets/js/feedback.js', __FILE__ ), '', DVT_VERSION_NUM );
        }

        $translation_array = array(
            'current_url_plugin' => $screen->base,
            'settings_valid' => $this->getOption('API_token_validation'),
            'choose_list' => $this->getOption('choose_list'),
            'flexSwitchCheckDefault_createWorkspace' => $this->getOption('flexSwitchCheckDefault_createWorkspace'),
            'client_name_show_chat' => $this->getOption('client_name'),
            'yes' => __( 'Yes, confirm', 'dev-tasks-up' ),
            'are_you_sure' => __( 'Are you sure ?', 'dev-tasks-up' ),
            'stop_the_connection' => __( 'Do you want to disconnect ?', 'dev-tasks-up' ),
            'Yes_disconnected' => __( 'Yes, disconnected !', 'dev-tasks-up' ),
            'cancel_text' => __( 'No, cancel', 'dev-tasks-up' ),
            'changes_are_saved' => __( 'Changes are saved', 'dev-tasks-up' ),
            'change_this_setting' => __( 'To change this setting', 'dev-tasks-up' ),
            'comment' => __( 'Comment', 'dev-tasks-up' ),
            'err_empty_comment' => __( 'To send a comment you must enter your comment in the field', 'dev-tasks-up' ),
            'deactivating' => __( 'Deactivating...', 'dev-tasks-up' ),
        );
        wp_localize_script( 'dev-tasks-js', 'translate_obj', $translation_array );
        wp_localize_script( 'dvt-feedback', 'translate_obj', $translation_array );

    }

    /**
     * Add shortcode
     *
     * @since 1.0.0
     */
    public function shortcodeAction()
    {
        ob_start();
        include_once 'views/frontend-page.php';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }


    /**
     * Save admin settings
     *
     * @since 1.1.0
     */
    public function save()
    {

        // First, validate the nonce and verify the user as permission to save.
        if (!($this->has_valid_nonce() && current_user_can('manage_options'))) {
            echo 'Not a valid nonce';
        }


        if (isset($_POST['connect-admin-form'])) {

            $data = array(
                'API_token' => base64_encode(sanitize_text_field($_POST['API_token'])),
                'API_token_validation' => 'invalid',
                'generalWorkspace_Id' => '',
                'flexSwitchCheckDefault_createWorkspace' => sanitize_text_field($_POST['flexSwitchCheckDefault_createWorkspace']),
                'choose_list' => sanitize_text_field($_POST['choose_list']),
                'validationWorkspace-name' => sanitize_text_field($_POST['validationWorkspace-name']),
                'client_name' => sanitize_text_field($_POST['client_name']),
                'new_task_notify' => sanitize_text_field($_POST['flexCheckNewTask']),
                'new_comment_notify' => sanitize_text_field($_POST['flexCheckNewComment']),

                // 'client_ID' => sanitize_text_field($_POST['client_ID']),
                // 'client_secret' => sanitize_text_field($_POST['client_secret']),
                // 'redirect_URL' => sanitize_text_field($_POST['redirect_URL']),
            );

            if (sanitize_text_field($_POST['choose_list']) === 'No') {
                $data['Workspace_ID'] = '';
                $data['show_workspace_name'] = '';
                $data['Folder_ID'] = '';
                $data['show_folder_name'] = '';
                $data['List_ID'] = '';
                $data['show_list_name'] = '';
                $data['choose_list'] = 'No';
            }

            if (!empty($_POST['chosen_workspace'])) {
                $selected_ws = explode (",", sanitize_text_field($_POST['chosen_workspace']));
                $selected_ws_workspace_id = $selected_ws[0];
                $selected_ws_workspace_name = $selected_ws[1];

                $data['Workspace_ID'] = $selected_ws_workspace_id;
                $data['show_workspace_name'] = $selected_ws_workspace_name;
            } else {
                if (sanitize_text_field($_POST['choose_list']) === 'Yes') {
                    $data['Workspace_ID'] = $this->getOption('Workspace_ID');
                    $data['show_workspace_name'] = $this->getOption('show_workspace_name');
                }
            }

            if (!empty($_POST['chosen_folder'])) {
                $selected_f = explode (",", sanitize_text_field($_POST['chosen_folder']));
                $selected_f_workspace_id = $selected_f[0];
                $selected_f_workspace_name = $selected_f[1];

                $data['Folder_ID'] = $selected_f_workspace_id;
                $data['show_folder_name'] = $selected_f_workspace_name;
            } else {
                if (sanitize_text_field($_POST['choose_list']) === 'Yes') {
                    $data['Folder_ID'] = $this->getOption('Folder_ID');
                    $data['show_folder_name'] = $this->getOption('show_folder_name');
                }
            }

            if (!empty($_POST['chosen_list'])) {
                $selected_list = explode (",", sanitize_text_field($_POST['chosen_list']));
                $selected_list_workspace_id = $selected_list[0];
                $selected_list_workspace_name = $selected_list[1];

                $data['List_ID'] = $selected_list_workspace_id;
                $data['show_list_name'] = $selected_list_workspace_name;
            } else {
                if (sanitize_text_field($_POST['choose_list']) === 'Yes') {
                    $data['List_ID'] = $this->getOption('List_ID');
                    $data['show_list_name'] = $this->getOption('show_list_name');
                }
            }

            update_option('devt-connect-data', json_encode($data));


            $url = "https://api.clickup.com/api/v2/team";
            $headers = array(
                'Authorization' => base64_decode($this->getOption('API_token'))
            );
            $response = wp_remote_get( $url, array(
                'headers' => $headers
            ));
            $json_response = json_decode(wp_remote_retrieve_body($response));

            $data['GeneralWorkspace_Id'] = $json_response->teams[0]->id;

            if (isset($json_response->err)) {
                $data['API_token_validation'] = 'invalid';
            } else {
                $data['API_token_validation'] = 'valid';
            }

            update_option('devt-connect-data', json_encode($data));

            $this->getWorkspaces($data); // Get all Workspaces

            if ($this->getOption('flexSwitchCheckDefault_createWorkspace') == 'Yes') {
                $this->createWorkspace($data); // Create new Workspace
            }


        }

        $this->redirect();
    }

    /**
     * Get Option from db
     *
     * @since 1.0.0
     */
    public function getOption($name)
    {
        $data = get_option('devt-connect-data');
        if (empty($data)) {
            return false;
        }

        $data = json_decode($data);
        if (isset($data->$name)) {
            return stripslashes($data->$name);
        }

        return false;
    }

    /**
     * Get main Option from db
     *
     * @since 1.0.0
     */
    private function getMainOption($option_name)
    {
        $data = get_option($option_name);
        if (empty($data)) {
            return false;
        }

        $data = json_decode($data);
        if (isset($data)) {
            return $data;
        }

        return false;
    }


    /**
     * Check valid nonce
     *
     * @since 1.0.0
     */
    private function has_valid_nonce()
    {
        // If the field isn't even in the $_POST, then it's invalid.
        if (!isset($_POST['connect-message'])) {
            return false;
        }

        $field  = sanitize_text_field(wp_unslash($_POST['connect-message']));
        $action = 'connect-save';

        return wp_verify_nonce($field, $action);
    }

    /**
     * Save redirect
     *
     * @since 1.0.0
     */
    private function redirect()
    {
        // To make the Coding Standards happy, we have to initialize this.
        if (!isset($_POST['_wp_http_referer'])) {
            $_POST['_wp_http_referer'] = wp_login_url();
        }

        // Sanitize the value of the $_POST collection for the Coding Standards.
        $url = sanitize_text_field(
            wp_unslash($_POST['_wp_http_referer'])
        );

        // Finally, redirect back to the admin page.
        wp_safe_redirect($url);
        exit;
    }

    /**
     * Create Workspace from settings
     *
     * @since 1.0.1
     */
    public function createWorkspace ($data) {

        session_start();

        $workspace_name = $this->getOption('validationWorkspace-name');

        $url = "https://api.clickup.com/api/v2/team/".$this->getOption('GeneralWorkspace_Id')."/space";

        $headers = array(
            'Authorization' => base64_decode($this->getOption('API_token')),
            'Content-Type' => 'application/json'
        );

        $body = array(
            'name' => $workspace_name,
            'multiple_assignees' => true,
            'features' => array(
                'due_dates' => array(
                    'enabled' => true,
                    'start_date' => false,
                    'remap_due_dates' => true,
                    'remap_closed_due_date' => false
                ),
                'time_tracking' => array(
                    'enabled' => true
                ),
                'tags' => array(
                    'enabled' => true
                ),
                'time_estimates' => array(
                    'enabled' => true
                ),
                'checklists' => array(
                    'enabled' => true
                ),
                'custom_fields' => array(
                    'enabled' => true
                ),
                'remap_dependencies' => array(
                    'enabled' => true
                ),
                'dependency_warning' => array(
                    'enabled' => true
                ),
                'portfolios' => array(
                    'enabled' => true
                )
            )
        );

        $args = array(
            'method' => 'POST',
            'headers' => $headers,
            'body' => json_encode($body)
        );

        $response = wp_remote_post( $url, $args );

        if ( is_wp_error( $response ) ) {
            echo $response->get_error_message();
        } else {
            $json_response = json_decode( $response['body'], true );

            if (isset($json_response['err'])) {
                // Store error in session
                $_SESSION['API_error'] = $json_response['err'];

                $data['workspace_created'] = false;
                $data['flexSwitchCheckDefault_createWorkspace'] = 'No';

                update_option('devt-connect-data', json_encode($data));

            } else {

                $data['Workspace_ID'] = $json_response['id']; // Get created Workspace ID
                $data['show_workspace_name'] = $json_response['name'];

                $data['workspace_created'] = true;

                update_option('devt-connect-data', json_encode($data));

                $this->inNewWorkspaceCreateFolder($data); // Create Folder in new Workspace
            }

        }

    }

    /**
     * Add new folder in created workspaces
     *
     * @since 1.0.0
     */
    public function inNewWorkspaceCreateFolder($data) {

        $list_name = preg_replace( '#^http(s)?:\/\/(www\.)?#', '', get_bloginfo('url'));
        $url = "https://api.clickup.com/api/v2/space/".$this->getOption('Workspace_ID')."/folder";

        $args = array(
            'method' => 'POST',
            'headers' => array(
                'Authorization' => base64_decode($this->getOption('API_token')),
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'name' => $list_name
            ))
        );

        $response = wp_remote_post($url, $args);
        $json_response = json_decode(wp_remote_retrieve_body($response));

        $data['Folder_ID'] = $json_response->id;
        $data['show_folder_name'] = $json_response->name;

        update_option('devt-connect-data', json_encode($data));

        $this->inNewWorkspaceFolderCreateList($data);

    }


    /**
     * Add new List in created Folder
     *
     * @since 1.0.0
     */
    public function inNewWorkspaceFolderCreateList($data) {

        // Create list in folder
        $args = array(
            'method' => 'POST',
            'headers' => array(
                'Authorization' => base64_decode($this->getOption('API_token')),
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'name' => 'Clients'
            )),
        );

        $response = wp_remote_post('https://api.clickup.com/api/v2/folder/'.$this->getOption('Folder_ID').'/list', $args);

        if ( is_wp_error( $response ) ) {
            echo $response->get_error_message();
        } else {
            $json_response = json_decode($response['body']);
            $data['List_ID'] = $json_response->id;
            $data['show_list_name'] = $json_response->name;
            update_option('devt-connect-data', json_encode($data));
        }


    }


    /**
     * Get all Workspaces
     *
     * @since 1.0.0
     */
    public function getWorkspaces($data) {

        $args = array(
            'method' => 'GET',
            'headers' => array(
                'Authorization' => base64_decode($this->getOption('API_token'))
            ),
        );

        $response = wp_remote_get('https://api.clickup.com/api/v2/team/'.$this->getOption('GeneralWorkspace_Id').'/space?archived=false', $args);

        if ( is_wp_error( $response ) ) {
            echo $response->get_error_message();
        } else {
            $json_response = json_decode($response['body'], true);
            $arr = array();
            foreach ($json_response['spaces'] as $key => $res) {
                $arr[$key]['id'] = $res['id'];
                $arr[$key]['name'] = $res['name'];
            }
            $data['all_workspaces'] = maybe_serialize($arr);
            update_option('devt-connect-data', json_encode($data));
        }


    }


    /**
     * AJAX select workspace and return folder for this workspace
     *
     * @since 1.0.0
     */
    public function selectWorkspace() {

        $selected_workspace_id = sanitize_text_field($_POST['selected_workspace_id']);

        $headers = array(
            'Authorization' => base64_decode($this->getOption('API_token'))
        );

        $folder_response = wp_remote_get( "https://api.clickup.com/api/v2/space/".$selected_workspace_id."/folder?archived=false", array(
            'headers' => $headers,
        ));

        $folder_json_response = json_decode( wp_remote_retrieve_body( $folder_response ), true );

        $folder_opt_html = array();
        foreach ($folder_json_response['folders'] as $folder) {
            $folder_opt_html[] .= '<option value="'.esc_attr($folder['id']).'">'.esc_html($folder['name']).'</option>';
        }

        if ($folder_opt_html) {
            $return_html = '<label class="form-check-label" for="select-folder">'. esc_html(__('Select a folder to attach tasks to', 'dev-tasks-up')).'</label>';
            $return_html .= '<select id="select-folder" name="select-folder" class="form-select" size="6" aria-label="size 6 select example" required>';
            $return_html .= '<option disabled value="">'.esc_html(__('Choose a folder', 'dev-tasks-up')).'</option>';
            foreach ($folder_opt_html as $opt) {
                $return_html .= $opt;
            }
            $return_html .= '</select>';
            $return_html .= '<div class="invalid-feedback">';
            $return_html .= esc_html(__('This field is required', 'dev-tasks-up'));
            $return_html .= '</div>';

            echo html_entity_decode(esc_html($return_html));
        } else {
            $list_response = wp_remote_get( "https://api.clickup.com/api/v2/space/".$selected_workspace_id."/list?archived=false", array(
                'headers' => $headers,
            ));

            $list_json_response = json_decode( wp_remote_retrieve_body( $list_response ), true );

            $list_opt_html = array();
            foreach ($list_json_response['lists'] as $list) {
                $list_opt_html[] .= '<option value="'.esc_attr($list['id']).'">'.esc_html($list['name']).'</option>';
            }

            $return_html_lists = '<label class="form-check-label" for="select-list">'. esc_html(__('Select a list to attach tasks to', 'dev-tasks-up')).'</label>';
            $return_html_lists .= '<select id="select-list" name="select-list" class="form-select" size="6" aria-label="size 6 select example" required>';
            $return_html_lists .= '<option disabled value="">'.esc_html(__('Choose a list', 'dev-tasks-up')).'</option>';
            foreach ($list_opt_html as $opt) {
                $return_html_lists .= $opt;
            }
            $return_html_lists .= '</select>';
            $return_html_lists .= '<div class="invalid-feedback">';
            $return_html_lists .= esc_html(__('This field is required', 'dev-tasks-up'));
            $return_html_lists .= '</div>';

            echo html_entity_decode(esc_html($return_html_lists));
        }

        wp_die();

    }

    /**
     * AJAX select folder and return list for this workspace
     *
     * @since 1.0.0
     */
    public function selectFolder() {

        $selected_folder_id = sanitize_text_field($_POST['selected_folder_id']);

        $headers = array(
            'Authorization' => base64_decode($this->getOption('API_token'))
        );

        $response = wp_remote_get( "https://api.clickup.com/api/v2/folder/".$selected_folder_id."/list?archived=false", array(
            'headers' => $headers,
        ));

        if ( is_wp_error( $response ) ) {
            echo 'Error: ' . esc_html($response->get_error_message());
        } else {
            $json_response = json_decode( wp_remote_retrieve_body( $response ), true );

            $list_opt_html = array();
            foreach ($json_response['lists'] as $list) {
                $list_opt_html[] .= '<option value="'.esc_attr($list['id']).'">'.esc_html($list['name']).'</option>';
            }

            if ($list_opt_html) {
                $return_html = '<label class="form-check-label" for="select-list">'. esc_html(__('Select a list to attach tasks to', 'dev-tasks-up')).'</label>';
                $return_html .= '<select id="select-list" name="select-list" class="form-select" size="6" aria-label="size 6 select example" required>';
                $return_html .= '<option disabled value="">'.esc_html(__('Choose a list', 'dev-tasks-up')).'</option>';
                foreach ($list_opt_html as $opt) {
                    $return_html .= $opt;
                }
                $return_html .= '</select>';
                $return_html .= '<div class="invalid-feedback">';
                $return_html .= esc_html(__('This field is required', 'dev-tasks-up'));
                $return_html .= '</div>';
                echo html_entity_decode(esc_html($return_html));
            }
        }
        wp_die();


    }

    /**
     * Start plugin session
     *
     * @since 1.1.1
     */
    public function dev_task_up_start_session() {
        if ( ! session_id() ) {
            session_start();
        }
    }

    /**
     * Add error alert return from ClickUp API
     *
     * @since 1.1.1
     */
    public function errorNotices() {

        // Check if there is an error stored in the session
        if (isset($_SESSION['API_error'])) {
            // Get the error text
            $error_text = $_SESSION['API_error'];

            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "'.$error_text.'",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    buttonsStyling: false
                })
            </script>';

            // Remove the error from the session
            unset($_SESSION['API_error']);
        }
    }


    /**
     * Feedback when deactivate plugin view
     *
     * @since 1.1.3
     */
    public function dvt_feedback_dialog() {
        ?>
        <div id="dvt-popup-container" class="popup-container">
            <div class="popup-content">
                <h3 style="margin-top: 0;"><?php echo esc_html__('Quick Feedback' , 'dev-tasks-up') ?></h3>
                <p style="font-weight: 600;"><?php echo esc_html__('If you have a moment, please share why you are deactivating DevtasksUp - ClickUp integration' , 'dev-tasks-up') ?></p>
                <hr style="margin-bottom: 20px;">
                <form id="dvt-feedback-form">
                    <div class="feedback-option">
                        <input type="radio" name="reason" value="needed" id="dvt-needed" required>
                        <label for="dvt-needed"><?php echo esc_html__('I no longer need the plugin' , 'dev-tasks-up') ?></label>
                    </div>
                    <div class="feedback-option">
                        <input type="radio" name="reason" value="alternative" id="dvt-alternative" required>
                        <label for="dvt-alternative"><?php echo esc_html__('I found a better plugin' , 'dev-tasks-up') ?></label>
                    </div>
                    <div class="feedback-option hidden" id="dvt-which-plugin">
                        <input type="text" placeholder="<?php echo esc_html__('Please share which plugin' , 'dev-tasks-up') ?>" name="which-plugin" id="dvt-which-plugin" style="width: 100%;"/>
                    </div>
                    <div class="feedback-option">
                        <input type="radio" name="reason" value="get_plugin_to_work" id="dvt-get_plugin_to_work" required>
                        <label for="dvt-get_plugin_to_work"><?php echo esc_html__('I couldn\'t get the plugin to work' , 'dev-tasks-up') ?></label>
                    </div>
                    <div class="feedback-option">
                        <input type="radio" name="reason" value="temporary" id="dvt-temporary" required>
                        <label for="dvt-temporary"><?php echo esc_html__('It\'s a temporary deactivation' , 'dev-tasks-up') ?></label>
                    </div>
                    <div class="feedback-option">
                        <input type="radio" name="reason" value="expectations" id="dvt-expectations" required>
                        <label for="dvt-expectations"><?php echo esc_html__('Plugin was not meeting expectations' , 'dev-tasks-up') ?></label>
                    </div>
                    <div class="feedback-option">
                        <input type="radio" name="reason" value="other" id="dvt-other" required>
                        <label for="dvt-other"><?php echo esc_html__('Other' , 'dev-tasks-up') ?></label>
                    </div>
                    <div class="feedback-option hidden" id="dvt-other-reason">
                        <textarea placeholder="<?php echo esc_html__('Please share the reason' , 'dev-tasks-up') ?>" name="other-reason-text" id="dvt-other-reason-text" rows="3" style="width: 100%;"></textarea>
                    </div>
                    <button type="submit"><?php echo esc_html__('Submit & Deactivate' , 'dev-tasks-up') ?></button>
                    <br>
                    <a class="dvt-skip" href="javascript:;"><?php echo esc_html__('Skip & Deactivate' , 'dev-tasks-up') ?></a>
                </form>
            </div>
        </div>

        <style>
            .popup-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.7);
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
                visibility: hidden;
                opacity: 0;
                transition: visibility 0s, opacity 0.3s ease;
            }

            .popup-container.show {
                visibility: visible;
                opacity: 1;
            }

            .popup-content {
                background-color: #fff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
                max-width: 500px;
                width: 100%;
                text-align: center;
            }

            .popup-content h2 {
                font-size: 24px;
                margin-bottom: 20px;
            }

            .feedback-option {
                display: flex;
                align-items: center;
                margin-bottom: 10px;
            }

            .feedback-option label {
                margin-left: 10px;
            }

            .hidden {
                display: none;
            }

            #dvt-feedback-form button {
                background-color: #222;
                border-radius: 3px;
                color: #fff;
                line-height: 1;
                padding: 12px 20px;
                font-size: 13px;
                min-width: 180px;
                height: 38px;
                border-width: 0;
                font-weight: 500;
                cursor: pointer;
                margin-bottom: 5px;
            }

            .dvt-skip {
                font-size: 12px;
                color:#a4afb7;
                text-decoration: none;
                font-weight: 600;
            }

            .dvt-skip:hover {
                color:#a4afb7;
                text-decoration: underline;
            }

        </style>
        <?php
    }


    /**
     * Feedback send email
     *
     * @since 1.1.2
     */
    function dvt_send_deactivation_email() {

        $deactivate_reasons = [
            'needed' => [
                'title' => esc_html__( 'I no longer need the plugin', 'dev-tasks-up' ),
            ],
            'alternative' => [
                'title' => esc_html__( 'I found a better plugin', 'dev-tasks-up' ),
            ],
            'get_plugin_to_work' => [
                'title' => esc_html__( 'I couldn\'t get the plugin to work', 'dev-tasks-up' ),
            ],
            'temporary' => [
                'title' => esc_html__( 'It\'s a temporary deactivation', 'dev-tasks-up' ),
            ],
            'expectations' => [
                'title' => esc_html__( 'Plugin was not meeting expectations', 'dev-tasks-up' ),
            ],
            'other' => [
                'title' => esc_html__( 'Other', 'dev-tasks-up' ),
            ],
        ];

        $form_data = array_map( 'sanitize_text_field', $_POST['form_data'] );

        $to = 'plugins@martinvalchev.com';
        $headers[] = 'From: '.get_bloginfo('name').'<'.get_option('admin_email').'>';
        $headers[] = 'Content-Type: text/html';
        $subject = DVT_PLUGIN_NAME.' deactivated';

        $reason_title = $deactivate_reasons[$form_data['reason']]['title'];

        ob_start();
        ?>
        <html>
        <body>
        <p>The plugin <?php echo esc_html(DVT_PLUGIN_NAME)?> has been deactivated with the following reason:</p>
        <p><strong><?php echo esc_html($reason_title )?></strong></p>
        <?php if (!empty($form_data['which-plugin'])) : ?>
            <p>Plugin replaced with:</p>
            <p><strong><?php echo esc_html($form_data['which-plugin'])?></strong></p>
        <?php endif; ?>
        <?php if (!empty($form_data['other-reason-text'])) : ?>
            <p>Additional details:</p>
            <p><strong><?php echo esc_html($form_data['other-reason-text'])?></strong></p>
        <?php endif; ?>
        </body>
        </html>
        <?php
        $html = ob_get_clean();

        wp_mail($to, $subject, $html, $headers);
        wp_send_json_success('Email sent successfully');

        wp_die();

    }



}


$DevTasksIntegration = new DevTasksIntegration();
