/**
 * Creates a preconfigured SweetAlert2 instance (`toast`) with default styling and target container.
 *
 * This mixin sets default button classes, input styling, and attaches the alert
 * to the `#page-container` element. Ideal for consistent styling across modals and alerts.
 *
 * @constant {SweetAlert2} toast - A SweetAlert2 instance with predefined styling and options.
 *
 * @example
 * toast.fire({
 *   title: 'Success!',
 *   text: 'Your data was saved successfully.',
 *   icon: 'success',
 *   showCancelButton: false,
 *   showConfirmButton: true
 * });
 */
let toast = Swal.mixin({
    buttonsStyling: false,
    target: '#page-container',
    customClass: {
        confirmButton: 'btn btn-success m-1',
        cancelButton: 'btn btn-danger m-1',
        input: 'form-control'
    }
});

/**
 * Displays a Bootstrap Notify-style notification using Codebase's One.helpers('jq-notify').
 *
 * @function showNotification
 * @param {string} [type='info'] - The type of the notification. Accepts 'info', 'success', 'warning', or 'danger'.
 * @param {string} [message='Your message!'] - The message to display in the notification.
 *
 * @example
 * showNotification('success', 'Data saved successfully!');
 * showNotification('danger', 'An error occurred while processing your request.');
 * showNotification('info', 'This is just an info notice.');
 * showNotification('warning', 'Be cautious about the next step.');
 */
function showNotification(type = 'info', message = 'Your message!') {
    const icons = {
        info: 'fa fa-info-circle me-1',
        success: 'fa fa-check me-1',
        warning: 'fa fa-exclamation me-1',
        danger: 'fa fa-times me-1'
    };

    const icon = icons[type] || icons.info;

    One.helpers('jq-notify', {
        type: type,
        icon: icon,
        message: message
    });
}

// Download Commercial Link Function
jQuery(document).on('click', '#reference-link-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');

    if (IsURL(url) !== true || url === null || url === undefined || url === '') {
        toast.fire({
            title: "Oops.. Sorry!",
            html: "No valid links found for this Program!",
            showCloseButton: true,
            allowOutsideClick: false
        })
    } else {
        toast.fire({
            title: "View Program",
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonText: "View",
            allowOutsideClick: false
        }).then(function (result) {
            if (result.value) {
                window.open(url, '_blank');
            }
        });
    }
});