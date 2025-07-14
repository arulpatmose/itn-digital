// Set default properties for SweetAlert2

let toast = Swal.mixin({
    buttonsStyling: false,
    target: '#page-container',
    customClass: {
        confirmButton: 'btn btn-success m-1',
        cancelButton: 'btn btn-danger m-1',
        input: 'form-control'
    }
});

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