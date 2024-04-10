jQuery( document ).ready(function($) {
    $.noConflict();

    /** @since 1.0.0 change event create_workspace  */
    $('#flexSwitchCheckDefault_createWorkspace').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val('Yes')
            $('#createWorkspace-info').show(500)
            $('#validationWorkspace-name').prop('required', true);

            $('#choose_list').prop('checked', false).val('No');
            $('#choose-workspace').hide(500)
        } else {
            $(this).val('No')
            $('#createWorkspace-info').hide(500)
            $('#validationWorkspace-name').removeAttr('required')
        }
    });

    /** @since 1.1.0 change event for setting page checkbox notify new task  */
    $('#flexCheckNewTask').on('change', function() {
        $(this).is(':checked') ? $(this).val('true') : $(this).val('false');
    });

    /** @since 1.1.0 change event for setting page checkbox notify new comment  */
    $('#flexCheckNewComment').on('change', function() {
        $(this).is(':checked') ? $(this).val('true') : $(this).val('false');
    });

    /** @since 1.0.0 change event choose_workspaces  */
    $('#choose_list').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val('Yes')
            $('#choose-workspace').show(500)
            $('#select-workspace').prop('required', true);

            $('#flexSwitchCheckDefault_createWorkspace').prop('checked', false).val('No');
            $('#createWorkspace-info').hide(500)
            $('#validationWorkspace-name').removeAttr('required')
        } else {
            $(this).val('No')
            $('#choose-workspace').hide(500)
            $('.path_settings').hide(500)
            $('#select-workspace').removeAttr('required')
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: translate_obj.changes_are_saved,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                // timer: 1500
            })
            Swal.showLoading()
            $('#submit').click()
        }
    });

    /** @since 1.0.0 change event select existing workspace  */
    $(document).on('change', '#select-workspace', function () {
        var optionSelected = $(this).find("option:selected");
        $('[name="chosen_workspace"]').val(`${optionSelected.val()},${optionSelected.text()}`)
        $.ajax({
            method: 'POST',
            url: ajaxurl,
            dataType: 'html',
            data: {
                action: 'select_workspace',
                selected_workspace_id: optionSelected.val(),
            },
            beforeSend: function () {
                $('.loader-overlay').show()
            },
            complete: function () {
                $('.loader-overlay').hide()
            }
        }).success(function (response) {
            $('#only-list').empty()
            $('#folders-list').empty().html(response)
        });
    });

    /** @since 1.0.0 change event select existing folder in workspace  */
    $(document).on('change', '#select-folder', function () {
        var optionSelected = $(this).find("option:selected");
        $('[name="chosen_folder"]').val(`${optionSelected.val()},${optionSelected.text()}`)
        $.ajax({
            method: 'POST',
            url: ajaxurl,
            dataType: 'html',
            data: {
                action: 'select_folder',
                selected_folder_id: optionSelected.val(),
            },
            beforeSend: function () {
                $('.loader-overlay').show()
            },
            complete: function () {
                $('.loader-overlay').hide()
            }
        }).success(function (response) {
            $('#only-list').empty().html(response)
        });
    });

    /** @since 1.0.0 change event select list  */
    $(document).on('change', '#select-list', function () {
        var optionSelected = $(this).find("option:selected");
        $('[name="chosen_list"]').val(`${optionSelected.val()},${optionSelected.text()}`)
    });

    /** @since 1.2.0 add custom class for style dropdown select2 when open select  */
    $('#dvt-cf-dropdown').on('select2:open', function() {
        $('#select2-dvt-cf-dropdown-results').closest('.select2-dropdown').addClass('dvt-cf-custom-style-select2')
    });

    /** @since 1.2.0 add custom class for style dropdown select2 when open select  */
    $('#dvt-cf-multi-dropdown').on('select2:open', function() {
        $('#select2-dvt-cf-multi-dropdown-results').closest('.select2-dropdown').addClass('dvt-cf-custom-style-select2')
    });

    /** @since 1.2.0 custom field ratting hover function  */
    $('.dvt-cf-star').hover(
        function(e) {
            $(this).removeClass('unselected');
            $(this).prevAll().removeClass('unselected');
        },
        function() {
            $(this).addClass('unselected');
            $(this).prevAll().addClass('unselected');
        }
    );

    /** @since 1.2.0 custom field function onchange for fields  */
    $('#dvt_custom_field_accordion .dvt-cf-field-value input[type="radio"]').change(function() {
        var wrapperID = $(this).closest('.dvt-ratting-field-wrapper').attr('id')

        if ($(this).is(':checked') ) {
            $(this).next(`label.dvt-cf-star`).addClass('dvt-checked');
            $(this).next(`label.dvt-cf-star`).prevAll(`label.dvt-cf-star`).addClass('dvt-checked');
            $(this).next(`label.dvt-cf-star`).removeClass('unselected');
            $(this).next(`label.dvt-cf-star`).prevAll(`label.dvt-cf-star`).removeClass('unselected');
            $(`#${wrapperID} .dvt-cf-star`).off('mouseenter mouseleave');
            $(`#${wrapperID} i`).remove()
            $(`#${wrapperID}`).append(`<i onclick="resetRatting('${wrapperID}')"  style="float: right;position: relative;top: 10px; color: red;" class="fas fa-times-circle"></i>`)

            // $(`#${wrapperID} input[type="radio"]`).prop('disabled', true);
        } else {
            $(this).next(`label.dvt-cf-star`).removeClass('dvt-checked');
            $(this).next(`label.dvt-cf-star`).prevAll(`label.dvt-cf-star`).removeClass('dvt-checked');
        }
    });


    // Check plugin page position for load scripts
    if (translate_obj.current_url_plugin === 'toplevel_page_dev-tasks-admin-page' && translate_obj.settings_valid !== 'invalid' && (translate_obj.choose_list === 'Yes' || translate_obj.flexSwitchCheckDefault_createWorkspace === 'Yes')) {

        /** @since 1.2.5 Ajax get list all data  */
        $.ajax({
            method: 'GET',
            url: ajaxurl,
            dataType: 'json',
            cache: false,
            data: {
                action: 'get_list_data',
            },
            beforeSend: function () {
                $('.loader-overlay').show()
            },
            complete: function () {
                $('.loader-overlay').hide()
                startGetAllTasks();
            }
        }).success(function (response) {
            for (const status of response.statuses) {
                $('.row.flex-nowrap').append(`
                  <div class="col-md-4 status__${status.status.replace(/ /g, "_")}" id="${status.id}">
                     <div class="card card__task" style="border-top-color: ${status.color}">
                        ${status.status}
                     </div>
                  </div>
            `)
             if (status.orderindex === 0) {
                 $('#status-to-create-task').val(status.status)
             }
            }
        });


    /** @since 1.2.5 Ajax get all tasks  */
    function startGetAllTasks() {
        $.ajax({
            method: 'GET',
            url: ajaxurl,
            dataType: 'json',
            cache: false,
            data: {
                action: 'get_all_tasks',
            },
            beforeSend: function () {
                $('.loader-overlay').show()
            },
            complete: function () {
                $('.loader-overlay').hide()
            }
        }).success(function (response) {
            var initials
            var profilePicture
            var color
            var showBG
            var username
            var time_spent
            var flagIcon

            for (var task of response.tasks) {

                // console.log(task);

                if (task.assignees[0]) {
                    initials = task.assignees[0].initials
                    profilePicture = task.assignees[0].profilePicture
                    color = task.assignees[0].color
                    username = task.assignees[0].username

                    if (!color) {
                        color = '#5897fb'
                    }

                    if (profilePicture) {
                        showBG = `background: url(${profilePicture}) center center / cover;`
                        initials = ''
                    } else {
                        showBG = `background: ${color};`
                    }

                } else {
                    initials = ''
                    profilePicture = ''
                    showBG = ''
                    color = ''
                    username = ''
                }

                if (task.priority) {
                    flagIcon = `<i class="fa fa-flag" style="font-size: 12px; color: ${task.priority.color}" title="${task.priority.priority}"></i>`
                } else {
                    flagIcon = `<i class="fa fa-flag" style="visibility: hidden; font-size: 12px;"></i>`
                }


                if (task.time_spent) {
                    var milliseconds = task.time_spent;
                    var duration = moment.duration(milliseconds);
                    var hours = Math.floor(duration.asHours());
                    var minutes = (Math.floor(duration.asMinutes() % 60)).toString().padStart(2, "0");
                    var seconds = (Math.floor(duration.asSeconds() % 60)).toString().padStart(2, "0");
                    time_spent = `<div style="font-size: 12px;">${hours}:${minutes}:${seconds}</div>`
                } else {
                    time_spent = '<div style="visibility: hidden; font-size: 12px;">none</div>'
                }

                $(`.status__${task.status.status.replace(/ /g, "_")}`)
                    .append(`
         
                   <div class="card task_wrapper" id="${task.id}" type="button" data-bs-toggle="modal" data-bs-target="#taskModal_${task.id}">
                        <div class="task_info_main">
                            <div class="task_title">${task.name}</div>
                            <div class="task_assignee" style="${showBG}" title="${username}" >${initials}</div>
                        </div>
                        <br>
                         <div class="task_other_info">${flagIcon} ${time_spent}</div>
                    </div>           
                      
                    <!-- Modal -->
                    <div class="modal fade custom-modal" id="taskModal_${task.id}" tabindex="-1" aria-labelledby="taskModalLabel_${task.id}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="taskModalLabel_${task.id}">${task.name}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                      <div class="task_other_info_all">
                                            <div class="task_assignee" style="${showBG} ${(showBG) ? '' : 'display:none;'}" title="${username}" >${initials}</div>
                                            ${flagIcon} ${time_spent}
                                      </div>
                                       <div class="row"> 
                                            <div class="col-md-6">
                                                <div class="card task-info">
                                                    <h5>${task.name}</h5>
                                                    <div style="${(task.description) ? '' : 'display:none;'}">${task.description}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card comments" id="scroll_${task.id}">
                                                       <!--Loader-->
                                                        <div class="loader-overlay-chat">
                                                            <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                                                        </div>
                                                        <!--END Loader-->
                                                        <div class="comment-wrapper"></div>
                                                </div>
                                            </div>   
                                       </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="text" name="add_comment" id="add_comment">
                                    <button type="button" id="submit_btn" class="btn btn-primary">${translate_obj.comment}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `)

            }
        }).error(function (response) {
            console.log('Err')
            console.log(response)
        });
    }

        /** @since 1.0.0 Ajax post task id on click  */
        $(document).on('click', '.card.task_wrapper', function () {
            $('#task_id_transfer').val('')
            $('#task_id_transfer').val($(this).attr('id'))
            $('#add_comment').val('')

            getAllComment($(this))

        })

        /** @since 1.0.0 Ajax post comment this task  */
        $(document).on('click', '#submit_btn', function () {

            if ($('#add_comment').val() != '') {

                $.ajax({
                    method: 'POST',
                    url: ajaxurl,
                    dataType: 'json',
                    cache: false,
                    data: {
                        action: 'add_task_comments',
                        task_id: $('#task_id_transfer').val(),
                        comment: `CLIENT: ${$('#add_comment').val()}`,
                    },
                    // beforeSend: function () {
                    //     $('.loader-overlay-chat').show()
                    // },
                    // complete: function () {
                    //     $('.loader-overlay-chat').hide()
                    // }
                }).success(function (response) {
                    $('#add_comment').val('')
                    getAllComment($(`#${$('#task_id_transfer').val()}`))

                }).error(function (response) {
                    console.log('Err')
                    console.log(response)
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: translate_obj.err_empty_comment,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    buttonsStyling: false
                })
            }

        })

        /** @since 1.0.0 This is function get all comments with ajax  */
        function getAllComment(elm) {

            $.ajax({
                method: 'POST',
                url: ajaxurl,
                dataType: 'json',
                cache: false,
                data: {
                    action: 'get_task_comments',
                    task_id: elm.attr('id'),
                },
                beforeSend: function () {
                    $('.loader-overlay-chat').show()
                },
                complete: function () {
                    $('.loader-overlay-chat').hide()
                }
            }).success(function (response) {
                $('.card.comments .comment-wrapper').empty()

                if (response.comments.length !== 0) {

                    for (var comment of response.comments.reverse()) {
                        var initials
                        var profilePicture
                        var color
                        var showBG
                        var username

                        // console.log(comment);

                        initials = comment.user.initials
                        profilePicture = comment.user.profilePicture
                        color = comment.user.color
                        username = comment.user.username

                        if (~comment.comment_text.indexOf("CLIENT: ")) {
                            comment.comment_text = comment.comment_text.replace('CLIENT: ', '')
                            var createIntials = translate_obj.client_name_show_chat.split(' ')
                            if (createIntials[0] && createIntials[1]) {
                                initials = createIntials[0].substring(0, 1)+createIntials[1].substring(0, 1)
                            } else {
                                initials = 'CL'
                            }

                            if (translate_obj.client_name_show_chat) {
                                username = translate_obj.client_name_show_chat
                            } else {
                                username = 'Client'
                            }

                            color = '#555555'
                            profilePicture = false
                        }

                        if (!color) {
                            color = '#5897fb'
                        }

                        if (profilePicture) {
                            showBG = `background: url(${profilePicture}) center center / cover;`
                            initials = ''
                        } else {
                            showBG = `background: ${color};`
                        }

                        var date = moment(parseInt(comment.date)); // milliseconds
                        var data_for_show = date.format("MMM Do YYYY, h:mm:ss a")

                        $('.card.comments .comment-wrapper').append(`
                   <div class="chat-wrapper">
                        <div class="avatar" style="${showBG}">${initials}</div>
                        <div class="comment-box">
                            <div class="user-info">
                                <div class="user-name" style="color:${color}">${username}</div>
                                <div class="user-post-date">${data_for_show}</div>
                            </div>
                            ${comment.comment_text}
                        </div>        
                   </div>
                `)

                    }
                }

                $(`#scroll_${$('#task_id_transfer').val()}`).stop().animate({scrollTop: $(`#scroll_${$('#task_id_transfer').val()}`)[0].scrollHeight}, 500)

            }).error(function (response) {
                console.log('Err')
                console.log(response)
            });
        }

    } //END Check plugin page position for load scripts


});


/** @since 1.0.0 style swetalert2 buttons with design bootstrap  */
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
})

/** @since 1.0.0 confirm disconnect btn and disconnect  */
function disconnect_btn() {
    swalWithBootstrapButtons.fire({
        title: translate_obj.are_you_sure,
        text: translate_obj.stop_the_connection,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: translate_obj.Yes_disconnected,
        cancelButtonText: translate_obj.cancel_text,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: translate_obj.changes_are_saved,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                // timer: 1500
            })
            Swal.showLoading()
            jQuery('#API_token').val('')
            jQuery('#client_name').val('')
            jQuery('#submit').click()
        }
    })
}

/** @since 1.0.0 remove settings for Workspace  */
function removeSettings() {
    swalWithBootstrapButtons.fire({
        title: translate_obj.are_you_sure,
        text: translate_obj.change_this_setting,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: translate_obj.yes,
        cancelButtonText: translate_obj.cancel_text,
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: translate_obj.changes_are_saved,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                // timer: 1500
            })
            Swal.showLoading()
            jQuery('#choose_list').click()
            jQuery('#submit').click()
        }
    })
}

/** @since 1.2.0 function for update label with name from attached file  */
function dvtUpdateLabel(input) {
    var fileName = input.files[0].name;
    var label = input.nextElementSibling;
    label.innerHTML = fileName;
}

/** @since 1.2.0 function for reset ratting fields  */
function resetRatting(id) {
    jQuery(`#${id} input[type="radio"]`).each(function() {
        jQuery(this).prop('checked', false);
        jQuery(this).next(`label.dvt-cf-star`).addClass('unselected');
        jQuery(this).next(`label.dvt-cf-star`).removeClass('dvt-checked');

        jQuery(`#${id} .dvt-cf-star`).hover(
            function(e) {
                jQuery(this).removeClass('unselected');
                jQuery(this).prevAll().removeClass('unselected');
            },
            function() {
                jQuery(this).addClass('unselected');
                jQuery(this).prevAll().addClass('unselected');
            }
        );

        // jQuery(`#${id} input[type="radio"]`).prop('disabled', false);

        jQuery(`#${id} i`).remove()
    })
}