<?php
if (!defined('ABSPATH')) exit;
global $DevTasksIntegration;
global $TaskCenter;
$members = unserialize($DevTasksIntegration->getOption('List_members'));
?>
<input id="task_id_transfer" type="hidden" value="">
<div class="wrapper">
     <h1><?php esc_html_e( 'Task Center', 'dev-tasks-up' ) ?></h1>

    <?php if ($DevTasksIntegration->getOption('API_token_validation') == 'valid' && ($DevTasksIntegration->getOption('choose_list') == 'Yes' || $DevTasksIntegration->getOption('flexSwitchCheckDefault_createWorkspace') == 'Yes')): ?>

        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="card create-task-form">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=post_task')); ?>"
                              class="needs-validation" novalidate>
                            <input type="hidden" name="create-task-form-save" value="post_task"/>
                            <fieldset>
                                <legend><?php esc_html_e('Create Task', 'dev-tasks-up') ?></legend>
                                <div class="mb-3">
                                    <label for="task-name" class="form-label"><?php esc_html_e('Summary', 'dev-tasks-up') ?><em>*</em></label>
                                    <input type="text" id="task-name" name="task-name" class="form-control" required>
                                    <div class="invalid-feedback">
                                        <?php esc_html_e('This field is required', 'dev-tasks-up'); ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="task-description"
                                           class="form-label"><?php esc_html_e('Description', 'dev-tasks-up') ?></label>
                                    <textarea class="form-control" id="task-description" name="task-description"
                                              rows="5"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="assigneeSelect"
                                           class="form-label"><?php esc_html_e('Assignee', 'dev-tasks-up') ?></label>
                                    <select id="assigneeSelect" name="assigneeSelect" class="form-select">
                                        <option><?php esc_html_e('Unassigned', 'dev-tasks-up') ?></option>
                                        <?php foreach ($members as $member) : ?>
                                            <option value="<?php echo esc_attr($member['id']) ?>"
                                                    data-color="<?php echo esc_attr($member['color']) ?>"
                                                    data-img_url="<?php echo esc_url($member['profilePicture']) ?>"
                                                    data-initials="<?php echo esc_attr($member['initials']) ?>"><?php echo esc_html($member['username']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="prioritySelect"
                                           class="form-label"><?php esc_html_e('Priority', 'dev-tasks-up') ?></label>
                                    <select id="prioritySelect" name="prioritySelect" class="form-select">
                                        <!-- <option value="0"><?php esc_html_e('None', 'dev-tasks-up') ?></option> -->
                                        <option value="4"><?php esc_html_e('Low', 'dev-tasks-up') ?></option>
                                        <option value="3"><?php esc_html_e('Normal', 'dev-tasks-up') ?></option>
                                        <option value="2"><?php esc_html_e('High', 'dev-tasks-up') ?></option>
                                        <option value="1"><?php esc_html_e('Urgent', 'dev-tasks-up') ?></option>
                                    </select>
                                </div>
                            </fieldset>
                            <?php
                            wp_nonce_field('create-save', 'create-message');
                            submit_button(__('Create', 'dev-tasks-up'), 'btn btn-primary');
                            ?>
                        </form>
                    </div>
                    <div class="banner-wrapper-taskpage">
                        <a href="https://clickup.com?fp_ref=hma1f" target="_blank" style="outline:none;border:none;"><img class="img-fluid" src="https://d2gdx5nv84sdx2.cloudfront.net/uploads/s73xa6xt/marketing_asset/banner/4153/medium_rectangle_v3-1.png" alt="clickup" border="0"/></a>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card" style="max-width: 100%; min-height: 593.7px; background-color: #eeeeee; overflow: auto; padding-left: 5px; padding-right: 5px;">

                        <!--Loader-->
                        <div class="loader-overlay">
                            <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                        </div>
                        <!--END Loader-->

                        <div class="container">
                            <div class="row flex-nowrap"></div>
                        </div>
                    </div>
                </div>

                <div class="banner-wrapper-taskpage-mobile">
                    <a href="https://clickup.com?fp_ref=hma1f" target="_blank" style="outline:none;border:none;"><img class="img-fluid" src="https://d2gdx5nv84sdx2.cloudfront.net/uploads/s73xa6xt/marketing_asset/banner/4153/medium_rectangle_v3-1.png" alt="clickup" border="0"/></a>
                </div>

            </div>
        </div>


    <?php else: ?>
        <div class="card no-config">
            <div class="card-body">
                <img src="<?php echo esc_url(DVT_STARTER_PLUGIN_URL . 'assets/images/choice.svg'); ?>" class="img-fluid" alt="choice"/>
                <h5 class="card-title"><?php esc_html_e( 'The plugin is not configured', 'dev-tasks-up' ) ?></h5>
                <p class="card-text"><?php echo sprintf(__( 'You must set up the link to your ClickUp account from the page. If you don\'t have an account, you can create one %s. Only a user with administrator rights can make the settings', 'dev-tasks-up' ), '<a href="https://clickup.com?fp_ref=hma1f" target="_blank">' . esc_html(__('here', 'dev-tasks-up')) . '</a>') ?></p>
                <a href="<?php echo esc_url(menu_page_url( 'dev-tasks-settings', false ))?>" class="btn btn-primary"><?php esc_html_e( 'Settings', 'dev-tasks-up' ) ?></a>
            </div>
        </div>
        <div style="text-align: center; margin: 20px 0;">
            <a href="https://clickup.com?fp_ref=hma1f" target="_blank" style="outline:none;border:none;"><img class="img-fluid" src="https://d2gdx5nv84sdx2.cloudfront.net/uploads/s73xa6xt/marketing_asset/banner/4158/leaderboard_v2.png" alt="clickup" border="0"/></a>
        </div>
    <?php endif; ?>

</div>

<script>
    /** @since 1.0.0 Form Validation create task */
    // JavaScript for disabling form submissions if there are invalid fields
    (function () {
        'use strict'

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

    /** @since 1.0.0 Select2 integration */
    jQuery( document ).ready(function($) {
        $("#assigneeSelect").select2({
            templateResult: formatState
        });

        $("#prioritySelect").select2({
            templateResult: formatStatePriority
        });
    });

    function formatState (state) {
        if (!state.id) {
            return state.text;
        }

        var initials = state.element.dataset.initials;
        var img_url = state.element.dataset.img_url;
        var color = state.element.dataset.color;

        if (!color) {
            color = '#5897fb';
        }

        if (img_url) {
            var $state = jQuery(
                `<span><img style="width: 40px; border-radius: 50%" src="${img_url}" class="img-flag" /></span><span style="margin-left: 10px;">${state.text}</span>`
            );
        } else {
            if (initials) {
                var $state = jQuery(
                    `<div style="display: flex; align-items: center;"><span style="background: ${color}; border-radius: 50%;width: 40px;height: 40px; display: flex;align-items: center;justify-content: center;font-size: 16px;font-weight: bold;color: #fff;">${initials}</span><span style="margin-left: 10px;">${state.text}</span></div>`
                );
            } else {
                var $state = jQuery(
                    `<span><img style="width: 40px; border-radius: 50%" src="<?php echo esc_url(DVT_STARTER_PLUGIN_URL . 'assets/images/48.png'); ?>" class="img-flag" /></span><span style="margin-left: 10px;">${state.text}</span>`
                );
            }
        }

        return $state;
    };

    function formatStatePriority (state) {
        if (!state.id) {
            return state.text;
        }

        var value = state.element.value;
        var iconColor = '';
        var icon = 'fa fa-flag';
        var size = '16';

        if (value === '1') {
            iconColor = '#f50000';
        } else if (value === '2') {
            iconColor = '#ffcc00';
        } else if (value === '3') {
            iconColor = '#6fddff';
        } else if (value === '4') {
            iconColor = '#d8d8d8';
        } else if (value === '0') {
            iconColor = '#222';
            icon = 'fas fa-times'
            size = '20';
        }

        var $state = jQuery(
            `<span><i class="${icon}" style="font-size: ${size}px; color: ${iconColor}"></i> ${state.text}</span>`
        );

        return $state;
    };
</script>
