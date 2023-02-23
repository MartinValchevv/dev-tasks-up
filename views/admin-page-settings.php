<?php
if (!defined('ABSPATH')) exit;
global $DevTasksIntegration;
$workspaces = unserialize($DevTasksIntegration->getOption('all_workspaces'));
?>

<div class="wrapper">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>


    <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="needs-validation" novalidate>
        <input type="hidden" name="connect-admin-form" value="1" />
        <input type="hidden" name="chosen_workspace" value="" />
        <input type="hidden" name="chosen_folder" value="" />
        <input type="hidden" name="chosen_list" value="" />

        <div class="card" style="min-width: 100%; padding: 0;">
            <div class="card-header">
                <?php esc_html_e( 'Connecting with ClickUp account', 'dev-tasks-up' ); ?>
                <?php if ($DevTasksIntegration->getOption('API_token')): ?>
                    <?php if ($DevTasksIntegration->getOption('API_token_validation') == 'invalid'): ?>
                        <span class="badge text-bg-danger"><?php esc_html_e( 'Disconnected', 'dev-tasks-up' ); ?></span>
                    <?php else: ?>
                        <a class="button_off"><span class="badge text-bg-success"><?php esc_html_e( 'Connected', 'dev-tasks-up' ); ?></span></a>
                        <button class="disconnect_btn btn btn-outline-danger" type="button" onclick="return disconnect_btn();" style="float: right">
                            <i class="fas fa-power-off"></i>
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="card-body">
                    <div class="form-floating">
                        <input type="password" name="API_token" id="API_token" value="<?php echo esc_attr(base64_decode($DevTasksIntegration->getOption('API_token'))) ?>" class="form-control" placeholder="<?php esc_html_e('Personal API Token', 'dev-tasks-up'); ?>" >
                        <label for="API_token"><?php esc_html_e('Personal API Token', 'dev-tasks-up'); ?></label>
                    </div>
                    <div class="form-floating" style="margin-top: 20px;">
                        <input type="text" name="client_name" id="client_name" value="<?php echo esc_attr($DevTasksIntegration->getOption('client_name')); ?>" class="form-control" placeholder="<?php esc_html_e('Client name', 'dev-tasks-up'); ?>">
                        <label for="client_name"><?php esc_html_e('Client name', 'dev-tasks-up'); ?></label>
                    </div>
                    <div class="form-check" style="margin: 10px 0;">
                        <input class="form-check-input" type="checkbox" value="<?php echo esc_attr($DevTasksIntegration->getOption('new_task_notify')); ?>" name="flexCheckNewTask" id="flexCheckNewTask" <?php echo ($DevTasksIntegration->getOption('new_task_notify') == 'true')? 'checked' : ''; ?>>
                        <label class="form-check-label" for="flexCheckNewTask">
                            <?php esc_html_e('Receive email notifications from ClickUp when a new task is created', 'dev-tasks-up'); ?>
                        </label>
                    </div>
                    <div class="form-check" style="margin: 10px 0;">
                        <input class="form-check-input" type="checkbox" value="<?php echo esc_attr($DevTasksIntegration->getOption('new_comment_notify')); ?>" name="flexCheckNewComment" id="flexCheckNewComment" <?php echo ($DevTasksIntegration->getOption('new_comment_notify') == 'true')? 'checked' : ''; ?>>
                        <label class="form-check-label" for="flexCheckNewComment">
                            <?php esc_html_e('Receive email notifications from ClickUp when a new comment is posted', 'dev-tasks-up'); ?>
                        </label>
                    </div>
                    <?php if (0): ?>
                        <div class="form-floating">
                            <input type="text" name="client_ID" id="client_ID" value="<?php echo esc_attr($DevTasksIntegration->getOption('client_ID')); ?>" class="form-control" placeholder="<?php esc_html_e('Client ID', 'dev-tasks-up'); ?>">
                            <label for="client_ID"><?php esc_html_e('Client ID', 'dev-tasks-up'); ?></label>
                        </div>
                        <div class="form-floating">
                            <input type="text" name="client_secret" id="client_secret" value="<?php echo esc_attr($DevTasksIntegration->getOption('client_secret')); ?>" class="form-control" placeholder="<?php esc_html_e('Client Secret', 'dev-tasks-up'); ?>">
                            <label for="client_secret"><?php esc_html_e('Client Secret', 'dev-tasks-up'); ?></label>
                        </div>
                        <div class="form-floating">
                            <input type="text" name="redirect_URL" id="redirect_URL" value="<?php echo esc_attr($DevTasksIntegration->getOption('redirect_URL')); ?>" class="form-control" placeholder="<?php esc_html_e('Redirect URL(s)', 'dev-tasks-up'); ?>">
                            <label for="redirect_URL"><?php esc_html_e('Redirect URL(s)', 'dev-tasks-up'); ?></label>
                        </div>
                    <?php endif; ?>
                    <?php if ($DevTasksIntegration->getOption('API_token_validation') == 'valid'): ?>
                        <div style="clear: both; height: 30px;"></div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" value="<?php echo esc_attr($DevTasksIntegration->getOption('flexSwitchCheckDefault_createWorkspace')) ?>" name="flexSwitchCheckDefault_createWorkspace" id="flexSwitchCheckDefault_createWorkspace" <?php echo ($DevTasksIntegration->getOption('flexSwitchCheckDefault_createWorkspace') == 'Yes')? 'checked' : ''; ?> <?php echo ($DevTasksIntegration->getOption('workspace_created'))? 'disabled' : ''; ?>>
                            <label class="form-check-label" for="flexSwitchCheckDefault_createWorkspace"><?php esc_html_e('Create a new working environment (automatically creates a folder with the client\'s domain in the new working environment, in the folder it creates a new list with the title Clients)', 'dev-tasks-up'); ?></label>
                            <div class="card" id="createWorkspace-info" style="min-width: 100%; display: none;">
                                <div class="col-md-12">
                                    <label for="validationWorkspace-name" class="form-label"><?php esc_html_e('Choose a Workspace name', 'dev-tasks-up'); ?></label>
                                    <input type="text" class="form-control" name="validationWorkspace-name" id="validationWorkspace-name">
                                    <div class="invalid-feedback">
                                        <?php esc_html_e('This field is required', 'dev-tasks-up'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($DevTasksIntegration->getOption('flexSwitchCheckDefault_createWorkspace') == 'Yes'): ?>
                            <div class="card border-primary mb-3 path_settings" style="max-width: auto; padding: 0; margin-left: 2.5rem;">
                                <div class="card-header"><?php esc_html_e('An application has been set up to run in the following path', 'dev-tasks-up'); ?></div>
                                <div class="card-body text-primary">
                                    <p class="card-text" style="font-size: inherit;">
                                        <span class="badge rounded-pill bg-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php esc_html_e('Workspace', 'dev-tasks-up'); ?>"><?php echo esc_html($DevTasksIntegration->getOption('show_workspace_name')) ?></span> /
                                        <?php if ($DevTasksIntegration->getOption('show_folder_name')): ?>
                                            <span class="badge rounded-pill bg-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php esc_html_e('Folder', 'dev-tasks-up'); ?>"><?php echo esc_html($DevTasksIntegration->getOption('show_folder_name')) ?></span> /
                                        <?php endif; ?>
                                        <span class="badge rounded-pill bg-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php esc_html_e('List', 'dev-tasks-up'); ?>"><?php echo esc_html($DevTasksIntegration->getOption('show_list_name')) ?></span>
                                        <button style="float: right" type="button" class="btn btn-outline-danger" onclick="return removeSettings();"><i class="far fa-trash-alt"></i></button>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div style="clear: both; height: 30px;"></div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" value="<?php echo esc_attr($DevTasksIntegration->getOption('choose_list')) ?>" name="choose_list" id="choose_list" <?php echo ($DevTasksIntegration->getOption('choose_list') == 'Yes')? 'checked' : ''; ?> <?php echo ($DevTasksIntegration->getOption('workspace_created'))? 'disabled' : ''; ?>>
                            <label class="form-check-label" for="choose_list"><?php esc_html_e('Choose an existing Workspace (choose a sheet in which the client will be able to create tasks and see the progress of the tasks)', 'dev-tasks-up'); ?></label>
                            <div class="card" id="choose-workspace" style="min-width: 100%; display: none;">
                                <!--Loader-->
                                <div class="loader-overlay">
                                    <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                                </div>
                                <!--END Loader-->
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-check-label" for="select-workspace"><?php esc_html_e('Select a workspace to attach tasks to', 'dev-tasks-up'); ?></label>
                                            <select id="select-workspace" name="select-workspace" class="form-select" size="6" aria-label="size 6 select example">
                                                <option disabled value=""><?php esc_html_e('Choose a Workspace', 'dev-tasks-up'); ?></option>
                                                <?php if (!empty($workspaces)) : ?>
                                                    <?php foreach ($workspaces as $ws ) : ?>
                                                        <option value="<?php echo esc_attr($ws['id']) ?>"><?php echo esc_html($ws['name']) ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                <?php esc_html_e('This field is required', 'dev-tasks-up'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="folders-list"></div>
                                        <div class="col-md-4" id="only-list"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($DevTasksIntegration->getOption('choose_list') == 'Yes'): ?>
                            <div class="card border-primary mb-3 path_settings" style="max-width: auto; padding: 0; margin-left: 2.5rem;">
                                <div class="card-header"><?php esc_html_e('An application has been set up to run in the following path', 'dev-tasks-up'); ?></div>
                                <div class="card-body text-primary">
                                    <p class="card-text" style="font-size: inherit;">
                                        <span class="badge rounded-pill bg-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php esc_html_e('Workspace', 'dev-tasks-up'); ?>"><?php echo esc_html($DevTasksIntegration->getOption('show_workspace_name')) ?></span> /
                                        <?php if ($DevTasksIntegration->getOption('show_folder_name')): ?>
                                        <span class="badge rounded-pill bg-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php esc_html_e('Folder', 'dev-tasks-up'); ?>"><?php echo esc_html($DevTasksIntegration->getOption('show_folder_name')) ?></span> /
                                        <?php endif; ?>
                                        <span class="badge rounded-pill bg-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php esc_html_e('List', 'dev-tasks-up'); ?>"><?php echo esc_html($DevTasksIntegration->getOption('show_list_name')) ?></span>
                                        <button style="float: right" type="button" class="btn btn-outline-danger" onclick="return removeSettings();"><i class="far fa-trash-alt"></i></button>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php
                wp_nonce_field('connect-save', 'connect-message');
                submit_button( __( 'Save Changes', 'dev-tasks-up' ), 'btn btn-primary' );
                ?>
            </div>
        </div>
    </form>
</div>
<div style="text-align: center; margin: 20px 0;">
    <a href="https://clickup.com?fp_ref=hma1f" target="_blank" style="outline:none;border:none;"><img class="img-fluid" src="https://d2gdx5nv84sdx2.cloudfront.net/uploads/s73xa6xt/marketing_asset/banner/4158/leaderboard_v2.png" alt="clickup" border="0"/></a>
</div>

<script>
    /** @since 1.0.0 Form Validation */
    // JavaScript for disabling form submissions if there are invalid fields
    (function () {
        'use strict'

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>