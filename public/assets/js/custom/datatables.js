// Override a few default classes
jQuery.extend(jQuery.fn.DataTable.ext.classes, {
    sWrapper: "dataTables_wrapper dt-bootstrap5",
    sFilterInput: "form-control form-control-sm",
    sLengthSelect: "form-select form-select-sm"
});

// Override a few defaults
jQuery.extend(true, jQuery.fn.DataTable.defaults, {
    language: {
        lengthMenu: "_MENU_",
        search: "_INPUT_",
        searchPlaceholder: "Search..",
        info: "Page <strong>_PAGE_</strong> of <strong>_PAGES_</strong>",
        infoFiltered: " - filtered from <strong>_MAX_</strong> total records",
        paginate: {
            first: '<i class="fa fa-angle-double-left"></i>',
            previous: '<i class="fa fa-angle-left"></i>',
            next: '<i class="fa fa-angle-right"></i>',
            last: '<i class="fa fa-angle-double-right"></i>'
        }
    },
    fnDrawCallback: function (oSettings) {
        if (typeof bsTooltip === "function" || typeof bsTooltip !== "undefined") {
            bsTooltip();
        }
    }
});

// Override buttons default classes
jQuery.extend(true, jQuery.fn.DataTable.Buttons.defaults, {
    dom: {
        button: {
            className: 'btn btn-sm btn-primary'
        }
    }
});

// Datatable for Programs
jQuery(document).ready(function () {
    if (jQuery('#table-programs').length) {
        jQuery('#table-programs').DataTable({
            ajax: {
                url: '/api/get-all-programs',
                data: function (data) {
                    return {
                        data: data
                    }
                },
                dataSrc: function (data) {
                    return data.aaData;
                },
                method: 'post'
            },
            serverSide: true,
            processing: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: true,
            info: true,
            columns: [
                {
                    data: 'prog_id'
                },
                {
                    data: 'thumbnail'
                },
                {
                    data: 'name'
                },
                {
                    data: 'type'
                },
                {
                    data: 'prog_id'
                }
            ],
            columnDefs: [
                {
                    targets: [0],
                    width: "5%",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: [1],
                    width: "10%",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var thumbnail;
                        var uri = URI().origin();

                        if (data == null || data == 'NULL') {
                            thumbnail = 'No-Image-Placeholder.svg';
                        } else {
                            thumbnail = data;
                        }

                        return '<img class="w-50 mx-auto d-block rounded" src="' + uri + '/uploads/thumbnails/' + thumbnail + '">';
                    },
                },
                {
                    targets: [3],
                    render: function (data, type, row, meta) {
                        var type;
                        switch (data) {
                            case "0":
                                type = "Teledrama";
                                break;
                            case "1":
                                type = "TV Show";
                                break;
                            case "2":
                                type = "Other";
                                break;
                            default:
                                type = "N/A";
                        }
                        return type;
                    }
                },
                {
                    targets: [4],
                    width: "10%",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<div class="btn-group">' +
                            '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/programs/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Program" data-bs-title="Edit Program" data-bs-placement="left">' +
                            '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-danger" id="delete-program-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/programs/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Program" data-bs-title="Remove Program" data-bs-placement="right">' +
                            '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                    }
                }
            ]
        });
    }
});

// Datatable for Spots
jQuery(document).ready(function () {
    if (jQuery('#table-spots').length) {
        jQuery('#table-spots').DataTable({
            ajax: {
                url: '/api/get-all-spots',
                data: function (data) {
                    return {
                        data: data
                    }
                },
                dataSrc: function (data) {
                    return data.aaData;
                },
                method: 'post'
            },
            serverSide: true,
            processing: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: true,
            info: true,
            order: [[2, 'asc']],
            columns: [
                {
                    data: 'spot_id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'priority'
                },
                {
                    data: 'spot_id'
                }
            ],
            columnDefs: [
                {
                    targets: [0],
                    width: "5%",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: [3],
                    width: "10%",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<div class="btn-group">' +
                            '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/spots/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Spot" data-bs-title="Edit Spot" data-bs-placement="left">' +
                            '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-danger" id="delete-spot-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/spots/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Spot" data-bs-title="Remove Spot" data-bs-placement="right">' +
                            '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                    }
                }
            ]
        });
    }
});

// Datatable for Formats
jQuery(document).ready(function () {
    if (jQuery('#table-formats').length) {
        jQuery('#table-formats').DataTable({
            ajax: {
                url: '/api/get-all-formats',
                data: function (data) {
                    return {
                        data: data
                    }
                },
                dataSrc: function (data) {
                    return data.aaData;
                },
                method: 'post'
            },
            serverSide: true,
            processing: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: true,
            info: true,
            columns: [
                {
                    data: 'format_id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'code'
                },
                {
                    data: 'format_id'
                }
            ],
            columnDefs: [
                {
                    targets: [0],
                    width: "5%",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: [3],
                    width: "10%",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<div class="btn-group">' +
                            '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/formats/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Format" data-bs-title="Edit Format" data-bs-placement="left">' +
                            '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-danger" id="delete-format-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/formats/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Format" data-bs-title="Remove Format" data-bs-placement="right">' +
                            '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                    }
                }
            ]
        });
    }
});

// Datatable for Clients
jQuery(document).ready(function () {
    if (jQuery('#table-clients').length) {
        jQuery('#table-clients').DataTable({
            ajax: {
                url: '/api/get-all-clients',
                data: function (data) {
                    return {
                        data: data
                    }
                },
                dataSrc: function (data) {
                    return data.aaData;
                },
                method: 'post'
            },
            serverSide: true,
            processing: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: true,
            info: true,
            columns: [
                {
                    data: 'client_id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'address'
                },
                {
                    data: 'client_id'
                }
            ],
            columnDefs: [
                {
                    targets: [0],
                    width: "5%",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: [3],
                    width: "10%",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<div class="btn-group">' +
                            '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/clients/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Client" data-bs-title="Edit Client" data-bs-placement="left">' +
                            '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-danger" id="delete-client-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/clients/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Client" data-bs-title="Remove Client" data-bs-placement="right">' +
                            '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                    }
                }
            ]
        });
    }
});

// Datatable for Platforms
jQuery(document).ready(function () {
    if (jQuery('#table-platforms').length) {
        jQuery('#table-platforms').DataTable({
            ajax: {
                url: '/api/get-all-platforms',
                data: function (data) {
                    return {
                        data: data
                    }
                },
                dataSrc: function (data) {
                    return data.aaData;
                },
                method: 'post'
            },
            serverSide: true,
            processing: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: true,
            info: true,
            columns: [
                {
                    data: 'pfm_id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'channel'
                },
                {
                    data: 'pfm_id'
                }
            ],
            columnDefs: [
                {
                    targets: [0],
                    width: "5%",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: [3],
                    width: "10%",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<div class="btn-group">' +
                            '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/platforms/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Platform" data-bs-title="Edit Platform" data-bs-placement="left">' +
                            '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-danger" id="delete-platform-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/platforms/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Platform" data-bs-title="Remove Platform" data-bs-placement="right">' +
                            '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                    }
                }
            ]
        });
    }
});

// Datatable for Commercials
jQuery(document).ready(function () {
    if (jQuery('#table-commercials').length) {
        jQuery('#table-commercials').DataTable({
            ajax: {
                url: '/api/get-all-commercials',
                data: function (data) {
                    return {
                        data: data
                    }
                },
                dataSrc: function (data) {
                    return data.aaData;
                },
                method: 'post'
            },
            serverSide: true,
            processing: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: true,
            columns: [
                {
                    data: 'com_id'
                },
                {
                    data: 'ucom_id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'duration'
                },
                {
                    data: 'format'
                },
                {
                    data: 'category'
                },
                {
                    data: 'sub_category'
                },
                {
                    data: 'client'
                },
                {
                    data: 'added_by'
                },
                {
                    data: 'remarks'
                },
                {
                    data: 'com_id'
                },

            ],
            columnDefs: [
                {
                    targets: [0],
                    width: "5%",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: [1],
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<a class="link-fx text-success" href="' + uri + '/commercials/edit/' + row.com_id + '">' + data + '</a>';
                    }
                },
                {
                    targets: [3],
                    render: function (data, type, row, meta) {
                        return data + ' Sec';
                    }
                },
                {
                    targets: [7],
                    render: function (data, type, row, meta) {
                        if (data !== 0.00 || data !== null) {
                            return data.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        } else {
                            return data;
                        }
                    }
                },
                {
                    targets: [10],
                    width: "10%",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<div class="btn-group">' +
                            '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/commercials/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Platform" data-bs-title="Edit Commercial" data-bs-placement="left">' +
                            '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-danger" id="delete-commercial-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/commercials/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Commercial" data-bs-title="Remove Commercial" data-bs-placement="top">' +
                            '<i class="fa fa-fw fa-times"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-primary" id="commercial-link-button" data-id="' + data + '" href="#" data-url="' + row.link + '" data-bs-toggle="tooltip" aria-label="Download Commercial" data-bs-title="Download Commercial" data-bs-placement="right">' +
                            '<i class="fa fa-fw fa-link"></i>' + '</a>' + ' </div>';
                    }
                }
            ]
        });
    }
});

// Datatable for Schedules
jQuery(document).ready(function () {
    if (jQuery('#table-schedules').length) {
        var tableSchedules = jQuery('#table-schedules').DataTable({
            ajax: {
                url: '/api/get-all-schedules',
                data: function (data) {
                    // Custom filter values
                    var dateRange = $('#schedule-date-range').val();
                    var program = $('#schedule-filters-program').val();
                    var commercial = $('#schedule-filters-commercial').val();
                    var platform = $('#schedule-filters-platform').val();
                    var client = $('#schedule-filters-client').val();
                    var format = $('#schedule-filters-format').val();
                    var schedule = $('#schedule-filters-schedule').val();

                    // Append values to Data
                    data.dateRange = dateRange;
                    data.program = program;
                    data.commercial = commercial
                    data.platform = platform;
                    data.client = client;
                    data.format = format;
                    data.schedule = schedule;

                    return {
                        data: data
                    }
                },
                dataSrc: function (data) {
                    return data.aaData;
                },
                method: 'post'
            },
            serverSide: true,
            processing: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: true,
            scrollX: true,
            scrollResize: true,
            scrollCollapse: true,
            // responsive: true,
            stateSave: true,
            searching: true,
            order: [[3, 'desc']],
            columns: [
                {
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: ''
                },
                {
                    data: 'published'
                },
                {
                    data: 'sched_id'
                },
                {
                    data: 'usched_id'
                },
                {
                    data: 'commercial'
                },
                {
                    data: 'format'
                },
                {
                    data: 'client_name'
                },
                {
                    data: 'program'
                },
                {
                    data: 'platform'
                },
                {
                    data: 'sched_id'
                }

            ],
            columnDefs: [
                {
                    targets: [1],
                    render: function (data, type, row, meta) {
                        var published;
                        switch (row.published) {
                            case '0':
                                published = '<i class="fa fa-times-circle text-danger"></i>';
                                break;
                            case '1':
                                published = '<i class="fa fa-circle-check text-success"></i>';
                                break;
                            default:
                                published = "N/A";
                        }
                        return published;
                    },
                    orderable: false,
                    width: 50
                },
                {
                    targets: [2],
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                    width: 50
                },
                {
                    targets: [3],
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<a class="link-fx text-success" href="' + uri + '/schedule/' + row.sched_id + '">' + data + '</a>';
                    }
                },
                {
                    targets: [4],
                    width: 120
                },
                {
                    targets: [8],
                    render: function (data, type, row, meta) {
                        var platform;
                        switch (row.platform.toLowerCase()) {
                            case 'facebook':
                                platform = '<i class="fab fa-facebook"></i> ' + data;
                                break;
                            case 'youtube':
                                platform = '<i class="fab fa-youtube"></i> ' + data;
                                break;
                            case 'instagram':
                                platform = '<i class="fab fa-instagram"></i> ' + data;
                                break;
                            case 'tiktok':
                                platform = '<i class="fab fa-tiktok"></i> ' + data;
                                break;
                            default:
                                platform = "N/A";
                        }
                        return platform + ' (' + row.channel + ')';
                    }
                },
                {
                    targets: [9],
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<div class="btn-group">' +
                            '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/schedules/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Schedule" data-bs-title="Edit Schedule" data-bs-placement="left">' +
                            '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-danger" id="delete-schedule-button" data-schedule="' + row.usched_id + '"data-id="' + data + '" href="#" data-url="' + _AppUri + '/schedules/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Schedule" data-bs-title="Remove Schedule" data-bs-placement="top">' +
                            '<i class="fa fa-fw fa-times"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-primary" data-id="' + data + '" href="' + uri + '/schedule/' + data + '" data-bs-toggle="tooltip" aria-label="View Items" data-bs-title="View Items" data-bs-placement="right">' +
                            '<i class="fa fa-fw fa-eye"></i>' + '</a>' + ' </div>';
                    }
                }
            ],
            language: {
                searchPlaceholder: "Enter Schedule ID"
            }
        });

        // Add event listener for opening and closing details
        tableSchedules.on('click', 'td.dt-control', function (e) {
            let tr = e.target.closest('tr');
            let row = tableSchedules.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
            }
            else {
                // Open this row
                row.child(formatScheduleExtra(row.data())).show();
            }
        });
    }
});

// Datatable for Schedule Accounts Data
jQuery(document).ready(function () {
    if (jQuery('#table-schedule-budget').length) {
        var tableScheduleBudget = jQuery('#table-schedule-budget').DataTable({
            ajax: {
                url: '/api/get-schedules-budget',
                data: function (data) {
                    // Custom filter values
                    var dateRange = $('#schedule-date-range').val();
                    var program = $('#schedule-filters-program').val();
                    var commercial = $('#schedule-filters-commercial').val();
                    var platform = $('#schedule-filters-platform').val();
                    var client = $('#schedule-filters-client').val();
                    var format = $('#schedule-filters-format').val();
                    var schedule = $('#schedule-filters-schedule').val();

                    // Append values to Data
                    data.dateRange = dateRange;
                    data.program = program;
                    data.commercial = commercial
                    data.platform = platform;
                    data.client = client;
                    data.format = format;
                    data.schedule = schedule;

                    return {
                        data: data
                    }
                },
                dataSrc: function (data) {
                    return data.aaData;
                },
                method: 'post'
            },
            serverSide: true,
            processing: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: true,
            scrollX: true,
            scrollResize: true,
            scrollCollapse: true,
            // responsive: true,
            stateSave: true,
            searching: true,
            columns: [
                {
                    data: 'sched_id'
                },
                {
                    data: 'usched_id'
                },
                {
                    data: 'commercial'
                },
                {
                    data: 'client_name'
                },
                {
                    data: 'program'
                },
                {
                    data: 'format'
                },
                {
                    data: 'platform'
                },
                {
                    data: 'marketing_ex'
                },
                {
                    data: 'num_schedules'
                },
                {
                    data: 'daily_budget'
                },
                {
                    data: 'total_budget'
                }
            ],
            columnDefs: [
                {
                    targets: [0],
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: [6],
                    render: function (data, type, row, meta) {
                        var platform;
                        switch (row.platform.toLowerCase()) {
                            case 'facebook':
                                platform = '<i class="fab fa-facebook"></i> ' + data;
                                break;
                            case 'youtube':
                                platform = '<i class="fab fa-youtube"></i> ' + data;
                                break;
                            case 'instagram':
                                platform = '<i class="fab fa-instagram"></i> ' + data;
                                break;
                            case 'tiktok':
                                platform = '<i class="fab fa-tiktok"></i> ' + data;
                                break;
                            default:
                                platform = "N/A";
                        }
                        return platform + ' (' + row.channel + ')';
                    }
                },
                {
                    targets: [9],
                    render: function (data, type, row, meta) {
                        if (data !== 0.00 || data !== null) {
                            return data.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        } else {
                            return data;
                        }
                    },
                    className: 'dt-body-right'
                },
                {
                    targets: [10],
                    render: function (data, type, row, meta) {
                        if (data !== 0.00 || data !== null) {
                            return data.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        } else {
                            return data;
                        }
                    },
                    className: 'dt-body-right'
                }
            ],
            dom: "<'row'<'col-sm-12'<'text-center bg-body-light py-2 mb-2'B>>>" +
                "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                searchPlaceholder: "Enter Schedule ID"
            }
        });
    }
});

// Datatable for Users
jQuery(document).ready(function () {
    if (jQuery('#table-users').length) {
        jQuery('#table-users').DataTable({
            ajax: {
                url: '/api/get-all-users',
                data: function (data) {
                    return {
                        data: data
                    }
                },
                dataSrc: function (data) {
                    return data.aaData;
                },
                method: 'post'
            },
            serverSide: true,
            processing: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: true,
            info: true,
            columns: [
                {
                    data: 'id'
                },
                {
                    data: 'first_name'
                },
                {
                    data: 'last_name'
                },
                {
                    data: 'groups'
                },
                {
                    data: 'last_active'
                },
                {
                    data: 'id'
                }
            ],
            columnDefs: [
                {
                    targets: [0],
                    width: "5%",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: [3],
                    render: function (data, type, row, meta) {
                        if (data.trim() !== '') {
                            var _groups = data.split(',');
                            // Loop through the array and create chip elements
                            // Create a list of spans and join them into a single string
                            var groupList = $.map(_groups, function (value) {
                                return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success me-1">' + value + '</span>';
                            }).join('');

                            return groupList;
                        } else {
                            return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">N/A</span>';
                        }
                    }
                },
                {
                    targets: [5],
                    width: "10%",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var uri = URI().origin();

                        return '<div class="btn-group">' +
                            '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/users/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit User" data-bs-title="Edit User" data-bs-placement="left">' +
                            '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                            '<a role="button" class="btn btn-sm btn-danger" id="delete-user-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/users/delete' + '" data-bs-toggle="tooltip" aria-label="Remove User" data-bs-title="Remove User" data-bs-placement="right">' +
                            '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                    }
                }
            ]
        });
    }
});

// Datatable for Schedule Item
jQuery(document).ready(function () {
    if (jQuery('#table-schedule').length) {
        jQuery('#table-schedule').DataTable({
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: false,
            info: true,
            searching: false,
            columnDefs: [
                {
                    targets: [0],
                    render: function (data, type, row, meta) {
                        var published;
                        switch (row[0]) {
                            case '0':
                                published = '<i class="fa fa-times-circle text-danger"></i>';
                                break;
                            case '1':
                                published = '<i class="fa fa-circle-check text-success"></i>';
                                break;
                            default:
                                published = "N/A";
                        }
                        return published;
                    },
                    orderable: false,
                    width: 50
                },
                {
                    targets: [1],
                    width: "5%",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: [4],
                    render: function (data, type, row, meta) {
                        // Split the text based on the hyphen
                        var parts = data.split(' - ');

                        var platform;
                        switch (parts[0].toLowerCase()) {
                            case 'facebook':
                                platform = '<i class="fab fa-facebook"></i> ' + data;
                                break;
                            case 'youtube':
                                platform = '<i class="fab fa-youtube"></i> ' + data;
                                break;
                            case 'instagram':
                                platform = '<i class="fab fa-instagram"></i> ' + data;
                                break;
                            case 'tiktok':
                                platform = '<i class="fab fa-tiktok"></i> ' + data;
                                break;
                            default:
                                platform = "N/A";
                        }
                        return platform;
                    }
                },
                {
                    targets: [11],
                    visible: false
                },
                {
                    targets: [12],
                    visible: false
                }
            ],
            dom: "<'row'<'col-sm-12'<'text-center bg-body-light py-2 mb-2'B>>>" +
                "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copy',
                    title: 'ITN Digital Schedule',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'ITN Digital Schedule',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12]
                    },
                    filename: function () { return getExportFileName() }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'ITN Digital Schedule',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12]
                    },
                    filename: function () { return getExportFileName() }
                },
                {
                    extend: 'csv',
                    text: 'CSV',
                    title: 'ITN Digital Schedule',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12]
                    },
                    filename: function () { return getExportFileName() }
                }
            ],
        });
    }
});