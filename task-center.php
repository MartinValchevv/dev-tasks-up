<?php 
/**
 * Functions for the Task Center page
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class TaskCenter
{
    /**
     * Construct
     *
     * @since 1.0.0
     */
    public function __construct()
    {

        add_action('admin_post_post_task', array($this, 'saveFormCreateTask'));
        add_action('dev_task_up_get_members_for_current_list', array($this, 'GetMembersCurrentList'));

        add_action('wp_ajax_get_list_data', array($this, 'GetListData'));
        add_action('wp_ajax_nopriv_get_list_data',  array($this, 'GetListData'));
        add_action('wp_ajax_get_all_tasks', array($this, 'GetAllTasks'));
        add_action('wp_ajax_nopriv_get_all_tasks',  array($this, 'GetAllTasks'));
        add_action('wp_ajax_get_task_comments', array($this, 'getTaskComments'));
        add_action('wp_ajax_nopriv_get_task_comments',  array($this, 'getTaskComments'));
        add_action('wp_ajax_add_task_comments', array($this, 'addTaskComments'));
        add_action('wp_ajax_nopriv_add_task_comments',  array($this, 'addTaskComments'));

        $this->GetMembersCurrentList();

    }

    /**
     * Get all List data
     *
     * @since 1.0.0
     */
    public function GetListData()
    {

        $listId = $this->getOption('List_ID');

        $headers = array(
            'Authorization' => base64_decode($this->getOption('API_token')),
        );

        $response = wp_remote_get(
            "https://api.clickup.com/api/v2/list/" . $listId,
            array(
                'headers' => $headers,
            )
        );

        if ( is_wp_error( $response ) ) {
            echo $response->get_error_message();
        } else {
            echo wp_remote_retrieve_body( $response );
        }


        wp_die();
    }


    /**
     * Get all task comments
     *
     * @since 1.0.0
     */
    public function getTaskComments()
    {

        $task_id = sanitize_text_field($_POST['task_id']);

        $query = array(
            "custom_task_ids" => "true",
            "team_id" => $this->getOption('GeneralWorkspace_Id'),
        );

        $headers = array(
            'Authorization' => base64_decode($this->getOption('API_token')),
        );

        $response = wp_remote_get(
            "https://api.clickup.com/api/v2/task/" . $task_id . "/comment?" . http_build_query($query),
            array(
                'headers' => $headers,
            )
        );

        if ( is_wp_error( $response ) ) {
            echo $response->get_error_message();
        } else {
            echo wp_remote_retrieve_body( $response );
        }

        wp_die();
    }

    /**
     * Add comments in task
     *
     * @since 1.0.4
     */
    public function addTaskComments()
    {
        $task_id = sanitize_text_field($_POST['task_id']);

        $headers = array(
            'Authorization' => base64_decode($this->getOption('API_token')),
            'Content-Type' => 'application/json',
        );

        $payload = array(
            "comment_text" => sanitize_text_field($_POST['comment']),
            "assignee" => 0,
            "notify_all" => true
        );

        $query = array(
            "custom_task_ids" => "true",
            "team_id" => $this->getOption('GeneralWorkspace_Id'),
        );

        $response = wp_remote_post(
            "https://api.clickup.com/api/v2/task/" . $task_id . "/comment?" . http_build_query($query),
            array(
                'headers' => $headers,
                'body' => json_encode($payload),
            )
        );

        if ( is_wp_error( $response ) ) {
            echo $response->get_error_message();
        } else {
            echo wp_remote_retrieve_body( $response );
        }

        wp_die();
    }



    /**
     * Get all tasks
     *
     * @since 1.0.0
     */
    public function GetAllTasks()
    {

        $listId = $this->getOption('List_ID');

        $query = array(
            "archived" => "false",
            "include_closed" => "true",
//            "page" => "0",
//            "order_by" => "string",
//            "reverse" => "true",
//            "subtasks" => "true",
//            "statuses" => "string",
//            "assignees" => "string",
//            "tags" => "string",
//            "due_date_gt" => "0",
//            "due_date_lt" => "0",
//            "date_created_gt" => "0",
//            "date_created_lt" => "0",
//            "date_updated_gt" => "0",
//            "date_updated_lt" => "0",
//            "custom_fields" => "string"
        );

        $headers = array(
            'Authorization' => base64_decode($this->getOption('API_token')),
            'Content-Type' => 'application/json'
        );

        $response = wp_remote_get(
            "https://api.clickup.com/api/v2/list/" . $listId . "/task?" . http_build_query($query),
            array(
                'headers' => $headers,
            )
        );

        if ( is_wp_error( $response ) ) {
            echo $response->get_error_message();
        } else {
            echo wp_remote_retrieve_body( $response );
        }

        wp_die();
    }


    /**
     * Save admin Task Center page
     *
     * @since 1.0.4
     */
    public function saveFormCreateTask()
    {

        // First, validate the nonce and verify the user as permission to save.
        if (!($this->has_valid_nonce() && current_user_can('manage_options'))) {
            echo 'Not a valid nonce';
        }


        if (isset($_POST['create-task-form-save'])) {

            $listId = $this->getOption('List_ID');

            $query = array(
                "custom_task_ids" => "true",
                "team_id" => $this->getOption('GeneralWorkspace_Id')
            );

            $payload = array(
                "name" => sanitize_text_field($_POST['task-name']),
                "description" => sanitize_text_field($_POST['task-description']),
                "assignees" => array(
                    sanitize_text_field($_POST['assigneeSelect'])
                ),
                "tags" => array(),
                "status" => "TO DO",
                "priority" => sanitize_text_field($_POST['prioritySelect']),
                "due_date" => null,
                "due_date_time" => false,
                "time_estimate" => null,
                "start_date" => null,
                "start_date_time" => false,
                "notify_all" => true,
                "parent" => NULL,
                "links_to" => NULL,
                "check_required_custom_fields" => true,
                "custom_fields" => array()
            );

            $headers = array(
                'Authorization' => base64_decode($this->getOption('API_token')),
                'Content-Type' => 'application/json',
            );


            $response = wp_remote_post(
                "https://api.clickup.com/api/v2/list/" . $listId . "/task?" . http_build_query($query),
                array(
                    'headers' => $headers,
                    'body' => json_encode($payload),
                )
            );

            if (is_wp_error($response)) {
                echo $response->get_error_message();
            } else {
                echo wp_remote_retrieve_body($response);
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
        if (!isset($_POST['create-message'])) {
            return false;
        }

        $field  = sanitize_text_field(wp_unslash($_POST['create-message']));
        $action = 'create-save';

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
     * Get Members for current list
     *
     * @since 1.0.1
     */
    public function GetMembersCurrentList() {

        $data = $this->getMainOption('devt-connect-data');

        $url = "https://api.clickup.com/api/v2/list/".$this->getOption('List_ID')."/member";
        $headers = array(
            'Authorization' => base64_decode($this->getOption('API_token'))
        );
        $response = wp_remote_get( $url, array(
            'headers' => $headers
        ));
        $json_response = json_decode(wp_remote_retrieve_body($response), true);

        $arrMembers = array();
        if (isset($json_response['members'])) {
            foreach ($json_response['members'] as $res) {
                $arrMembers[$res['id']]['id'] = $res['id'];
                $arrMembers[$res['id']]['username'] = $res['username'];
                $arrMembers[$res['id']]['email'] = $res['email'];
                $arrMembers[$res['id']]['color'] = $res['color'];
                $arrMembers[$res['id']]['initials'] = $res['initials'];
                $arrMembers[$res['id']]['profilePicture'] = $res['profilePicture'];
                $arrMembers[$res['id']]['profileInfo'] = $res['profileInfo'];
            }
        }


        if (is_object($data)) {
            if (isset($json_response['err'])) {
                $data->List_members = '';
            } else {
                $data->List_members = maybe_serialize($arrMembers);
            }

            update_option('devt-connect-data', json_encode($data));
        } else {
            $data = new stdClass();
            if (isset($json_response['err'])) {
                $data->List_members = '';
            } else {
                $data->List_members = maybe_serialize($arrMembers);
            }

            update_option('devt-connect-data', json_encode($data));
        }

    }


}


$TaskCenter = new TaskCenter();