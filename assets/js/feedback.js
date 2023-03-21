jQuery(document).ready(function ($) {
    var src = $('#the-list').find('[data-slug="devtasksup"] span.deactivate a').attr('href')

    $('#the-list').find('[data-slug="devtasksup"] span.deactivate a').attr('href', 'javascript:;')

    $('#the-list').find('[data-slug="devtasksup"] span.deactivate a').on('click', function (e) {
        e.preventDefault();
        $('#dvt-popup-container').addClass('show');
    });

    $('.dvt-skip').on('click', function (e) {
        e.preventDefault();
        location.href = src;
    });

    // Close popup when clicking outside of it
    $(document).on('click', function(event) {
        if ($(event.target).hasClass('show')) {
            $('#dvt-popup-container').removeClass('show');
        }
    });

    // Show/hide text field when selecting/deselecting 'Other' option
    $('input[type="radio"][name="reason"]').on('change', function() {
        $('#other-reason').addClass('hidden');
        $('#which-plugin').addClass('hidden');
        $('#other-reason-text').prop('required',false);

        if ($(this).val() === 'other') {
            $('#other-reason').removeClass('hidden');
            $('#other-reason-text').prop('required',true);
        } else if ($(this).val() === 'alternative') {
            $('#which-plugin').removeClass('hidden');
            $('#other-reason-text').prop('required',false);
            $('#other-reason').addClass('hidden');
        }
    });

    // Submit feedback form
    $('#dvt-feedback-form').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray();
        var serialized_data = {};

        $.each(formData, function(index, obj){
            serialized_data[obj.name] = obj.value;
        });

        $.ajax({
            method: 'POST',
            url: ajaxurl,
            data: {
                action: 'send_deactivation_feedback_email',
                form_data: serialized_data,
            },
            success: function(response) {
                if (response.success) {
                    $('#dvt-popup-container').removeClass('show');
                    location.href = src;
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error submitting feedback: ' + errorThrown);
            }
        });
    });
});