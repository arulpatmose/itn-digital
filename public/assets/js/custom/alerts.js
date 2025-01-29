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

// Delete Program Function

jQuery(document).on('click', '#delete-program-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');
    toast.fire({
        title: "Are you sure?",
        html: "You won't be able to revert this!",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: "Yes, Delete it",
        allowOutsideClick: false
    }).then(function (result) {
        if (result.value) {
            $.post(url, { id: id }, function (data) {
                response = jQuery.parseJSON(data);
                if (response.code == 1) {
                    toast.fire({
                        title: "Success",
                        icon: 'success',
                        html: response.message
                    }).then(function () {
                        jQuery('#table-programs').DataTable().ajax.reload(null, false);
                    });
                } else {
                    toast.fire({
                        title: "Error",
                        icon: 'error',
                        html: response.message
                    });
                }
            });
        }
    });
});

// Delete Spot Function

jQuery(document).on('click', '#delete-spot-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');
    toast.fire({
        title: "Are you sure?",
        html: "You won't be able to revert this!",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: "Yes, delete it",
        allowOutsideClick: false
    }).then(function (result) {
        if (result.value) {
            $.post(url, { id: id }, function (data) {
                response = jQuery.parseJSON(data);
                if (response.code == 1) {
                    toast.fire({
                        title: "Success",
                        icon: 'success',
                        html: response.message
                    }).then(function () {
                        jQuery('#table-spots').DataTable().ajax.reload(null, false);
                    });
                } else {
                    toast.fire({
                        title: "Error",
                        icon: 'error',
                        html: response.message
                    });
                }
            });
        }
    });
});

// Delete Format Function

jQuery(document).on('click', '#delete-format-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');
    toast.fire({
        title: "Are you sure?",
        html: "You won't be able to revert this!",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: "Yes, delete it",
        allowOutsideClick: false
    }).then(function (result) {
        if (result.value) {
            $.post(url, { id: id }, function (data) {
                response = jQuery.parseJSON(data);
                if (response.code == 1) {
                    toast.fire({
                        title: "Success",
                        icon: 'success',
                        html: response.message
                    }).then(function () {
                        jQuery('#table-formats').DataTable().ajax.reload(null, false);
                    });
                } else {
                    toast.fire({
                        title: "Error",
                        icon: 'error',
                        html: response.message
                    });
                }
            });
        }
    });
});

// Delete Client Function

jQuery(document).on('click', '#delete-client-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');
    toast.fire({
        title: "Are you sure?",
        html: "You won't be able to revert this!",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: "Yes, delete it",
        allowOutsideClick: false
    }).then(function (result) {
        if (result.value) {
            $.post(url, { id: id }, function (data) {
                response = jQuery.parseJSON(data);
                if (response.code == 1) {
                    toast.fire({
                        title: "Success",
                        icon: 'success',
                        html: response.message
                    }).then(function () {
                        jQuery('#table-clients').DataTable().ajax.reload(null, false);
                    });
                } else {
                    toast.fire({
                        title: "Error",
                        icon: 'error',
                        html: response.message
                    });
                }
            });
        }
    });
});

// Delete Platform Function

jQuery(document).on('click', '#delete-platform-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');
    toast.fire({
        title: "Are you sure?",
        html: "You won't be able to revert this!",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: "Yes, delete it",
        allowOutsideClick: false
    }).then(function (result) {
        if (result.value) {
            $.post(url, { id: id }, function (data) {
                response = jQuery.parseJSON(data);
                if (response.code == 1) {
                    toast.fire({
                        title: "Success",
                        icon: 'success',
                        html: response.message
                    }).then(function () {
                        jQuery('#table-platforms').DataTable().ajax.reload(null, false);
                    });
                } else {
                    toast.fire({
                        title: "Error",
                        icon: 'error',
                        html: response.message
                    });
                }
            });
        }
    });
});

// Delete Commercial Function

jQuery(document).on('click', '#delete-commercial-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');
    toast.fire({
        title: "Are you sure?",
        html: "You won't be able to revert this!",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: "Yes, delete it",
        allowOutsideClick: false
    }).then(function (result) {
        if (result.value) {
            $.post(url, { id: id }, function (data) {
                response = jQuery.parseJSON(data);
                if (response.code == 1) {
                    toast.fire({
                        title: "Success",
                        icon: 'success',
                        html: response.message
                    }).then(function () {
                        jQuery('#table-commercials').DataTable().ajax.reload(null, false);
                    });
                } else {
                    toast.fire({
                        title: "Error",
                        icon: 'error',
                        html: response.message
                    });
                }
            });
        }
    });
});

// Download Commercial Link Function

jQuery(document).on('click', '#commercial-link-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');

    if (IsURL(url) !== true || url === null || url === undefined || url === '') {
        toast.fire({
            title: "Oops.. Sorry!",
            html: "No valid download links found for this commercial!",
            showCloseButton: true,
            allowOutsideClick: false
        })
    } else {
        toast.fire({
            title: "Download Commercial",
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonText: "Download",
            allowOutsideClick: false
        }).then(function (result) {
            if (result.value) {
                window.open(url, '_blank');
            }
        });
    }
});

// Delete Schedules Function

jQuery(document).on('click', '#delete-schedule-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');
    var schedule = $(this).data('schedule');
    toast.fire({
        title: "Are you sure?",
        html: "<span class=\"my-3 d-block\">You're about to delete <mark>" + schedule + "</mark></span><small>Deleting a single schedule item will result in the removal of all associated scheduled items for this commercial. This action is irreversible.</small>",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: "Yes, Delete All",
        allowOutsideClick: false
    }).then(function (result) {
        if (result.value) {
            $.post(url, { id: id }, function (data) {
                response = jQuery.parseJSON(data);
                if (response.code == 1) {
                    toast.fire({
                        title: "Success",
                        icon: 'success',
                        html: response.message
                    }).then(function () {
                        jQuery('#table-schedules').DataTable().ajax.reload(null, false);
                    });
                } else {
                    toast.fire({
                        title: "Error",
                        icon: 'error',
                        html: response.message
                    });
                }
            });
        }
    });
});

// Delete Schedules Function

jQuery(document).on('click', '#delete-schedule-item-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');
    var schedule = $(this).data('schedule');
    toast.fire({
        title: "Are you sure?",
        html: "You won't be able to revert this!",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: "Yes, Delete",
        allowOutsideClick: false
    }).then(function (result) {
        if (result.value) {
            $.post(url, { id: id }, function (data) {
                response = jQuery.parseJSON(data);
                if (response.code == 1) {
                    toast.fire({
                        title: "Success",
                        icon: 'success',
                        html: response.message
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    toast.fire({
                        title: "Error",
                        icon: 'error',
                        html: response.message
                    });
                }
            });
        }
    });
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

// Delete User Function

jQuery(document).on('click', '#delete-user-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).data('url');
    toast.fire({
        title: "Are you sure?",
        html: "You won't be able to revert this!",
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: "Yes, delete it",
        allowOutsideClick: false
    }).then(function (result) {
        if (result.value) {
            $.post(url, { user_id: id }, function (data) {
                response = jQuery.parseJSON(data);
                if (response.code == 1) {
                    toast.fire({
                        title: "Success",
                        icon: 'success',
                        html: response.message
                    }).then(function () {
                        jQuery('#table-users').DataTable().ajax.reload(null, false);
                    });
                } else {
                    toast.fire({
                        title: "Error",
                        icon: 'error',
                        html: response.message
                    });
                }
            });
        }
    });
});

// Update modal on Daily Schedule Page

jQuery(document).on('click', '#schedule-update-button', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var update_url = $(this).data('url');
    var edit_url = '/daily-schedule/edit/' + id;

    $.ajax({
        type: "POST",
        url: edit_url,
        cache: false,
        success: function (res) {
            if (res !== undefined || res === '') {
                $('#schedule-link').val(res.link);
                $('#schedule-remarks').val(res.remarks);
            }
        }
    });

    jQuery('#schedule-update-modal').modal('show');

    // Attach a click event handler to the "Submit" button within the modal
    jQuery('#schedule-update-form-button').on('click', function () {
        // Get the form data using jQuery
        var formData = new FormData($('#schedule-update-form')[0]);

        formData.append('sched_id', id);

        postData = new URLSearchParams(formData).toString();

        $.ajax({
            type: "POST",
            url: update_url,
            cache: false,
            data: postData,
            success: function (response) {
                if (response.status === 'success') {
                    // Close the modal
                    $('#schedule-update-form').modal('hide');
                    toast.fire({
                        title: 'The schedule was updated successfully',
                        showCancelButton: false,
                        showConfirmButton: true
                    }).then(function () {
                        window.location.reload();
                    })
                } else {
                    toast.fire({
                        title: 'Failed to update schedule',
                        text: 'Please try again.'
                    }).then(function () {
                        window.location.reload();
                    })
                }
            },
            error: function (error) {
                toast.fire({
                    title: 'Failed to update schedule!',
                    text: 'Something went wrong'
                }).then(function () {
                    window.location.reload();
                })
            }
        });
    });
});

// Bulk Update Links on Daily Schedule

$(document).ready(function () {
    $('.bulk-update-button').click(function (event) {
        event.preventDefault();
        var update_url = '/daily-schedule/update-bulk';

        let tableId = $(this).data('table-id');
        let selectedIds = [];
        $(`#data-table-${tableId} .select-row:checked`).each(function () {
            selectedIds.push($(this).data('id'));
        });

        if (selectedIds.length > 0) {
            // Show the modal
            $('#schedule-update-modal').modal('show');

            // Attach a click event handler to the "Submit" button within the modal
            $('#schedule-update-form-button').off('click').on('click', function () {
                // Get the form data using jQuery
                var formData = new FormData($('#schedule-update-form')[0]);

                // Append selected IDs
                selectedIds.forEach(function (id) {
                    formData.append('ids[]', id);
                });

                // Convert FormData to URL-encoded string
                var postData = new URLSearchParams(formData).toString();

                $.ajax({
                    type: "POST",
                    url: update_url, // Change this URL to your bulk update endpoint
                    cache: false,
                    data: postData,
                    success: function (response) {
                        if (response.status === 'success') {
                            // Close the modal
                            $('#schedule-update-modal').modal('hide');
                            toast.fire({
                                title: 'The schedule was updated successfully',
                                showCancelButton: false,
                                showConfirmButton: true
                            }).then(function () {
                                window.location.reload();
                            });
                        } else {
                            toast.fire({
                                title: 'Failed to update schedule',
                                text: 'Please try again.'
                            }).then(function () {
                                window.location.reload();
                            });
                        }
                    },
                    error: function (error) {
                        toast.fire({
                            title: 'Failed to update schedule!',
                            text: 'Something went wrong'
                        }).then(function () {
                            window.location.reload();
                        });
                    }
                });
            });
        } else {
            toast.fire({
                title: 'Oops!',
                text: 'Please select at least one schedule item.'
            });
        }
    });
});
