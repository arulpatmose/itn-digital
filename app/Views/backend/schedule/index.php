<?php

/**
 * view_schedule
 *
 * Author: Arul Patmose
 *
 * Load Default Global Template and Extend
 *
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        List of Schedule Items for
                        <span class="mx-1 text-success"><?php echo $schedule['usched_id']; ?></span>
                        <span> - </span>
                        <span class="mx-1 text-success"><?php echo $commercial['name']; ?></span>
                        <span class="text-gray-dark">(<?php echo $commercial['duration']; ?> Secs)</span>
                        <span> - </span>
                        <span><?php echo $program['name']; ?></span>
                    </h3>
                    <div class="block-options">
                        <a role="button" href="<?php echo base_url('schedules'); ?>" class="btn btn-sm btn-danger">
                            Cancel
                        </a>
                        <a href="<?php echo base_url('schedule/add/') . $schedule['sched_id']; ?>" role="button" class="btn btn-sm btn-primary">Add
                            New</a>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-schedule">
                            <thead>
                                <tr>
                                    <th class="publishStatus text-center"></th>
                                    <th class="dataIndex text-center">#</th>
                                    <th class="scheduleDate">Scheduled Date</th>
                                    <th class="commercial noOrder">Commercial</th>
                                    <th class="platform noOrder">Platform</th>
                                    <th class="spot noOrder">Spot</th>
                                    <th class="addedBy noOrder">Added By</th>
                                    <th class="createAt noOrder">Created At</th>
                                    <th class="updatedBy noOrder">Updated By</th>
                                    <th class="comments hiddenCols noOrder">Comments</th>
                                    <th class="views">Views</th>
                                    <th class="tableAction all noOrder text-center">Actions</th>
                                    <th class="status hiddenCols noOrder">Status</th>
                                    <th class="link hiddenCols noOrder">Ref Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedule_items as $item) { ?>
                                    <tr>
                                        <td><?= $item['published']; ?>
                                        </td>
                                        <td><?= $item['scd_id']; ?>
                                        </td>
                                        <td><?= $item['sched_date']; ?>
                                        </td>
                                        <td><?= $commercial['name'] ?>
                                            <span class="mx-1 text-gray-dark">(<?= $commercial['duration'] ?> Secs)</span>
                                        </td>
                                        <td data-platform-name=<?= strtolower($platform['name']) ?>><?= $platform['name'] . ' - ' . $platform['channel']; ?>
                                        </td>
                                        <td><?= $item['spot_name']; ?>
                                        </td>
                                        <td><?= $item['a_first_name']; ?>
                                        </td>
                                        <td><?= $item['created_at']; ?>
                                        </td>
                                        <td><?= $item['u_first_name']; ?>
                                        </td>
                                        <td><?= $item['remarks']; ?>
                                        </td>
                                        <td data-link=<?= $item['link']; ?>>N/A</td>
                                        <td>
                                            <div class="btn-group">
                                                <a role="button"
                                                    class="btn btn-sm btn-danger"
                                                    id="delete-schedule-item-button"
                                                    data-schedule="<= $schedule['usched_id']; ?>"
                                                    data-id="<?= $item['scd_id']; ?>"
                                                    href="#"
                                                    data-url="<?= site_url('schedule/delete'); ?>"
                                                    data-bs-toggle="tooltip"
                                                    aria-label="Remove Schedule"
                                                    data-bs-title="Remove Schedule"
                                                    data-bs-placement="left">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </a>

                                                <a role="button"
                                                    class="btn btn-sm btn-warning position-relative"
                                                    id="view-comments-button"
                                                    data-schedule-id="<?= esc($item['sched_id']) ?>"
                                                    data-schedule-item-id="<?= esc($item['scd_id']) ?>"
                                                    href="#"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewCommentsModal">

                                                    <i class="fa fa-fw fa-comments"></i>

                                                    <?php if (!empty($item['remarks'])): ?>
                                                        <span class="ripple-dot"></span>
                                                    <?php endif ?>
                                                </a>

                                                <a role="button"
                                                    class="btn btn-sm btn-primary"
                                                    id="reference-link-button"
                                                    href="#"
                                                    data-url="<?= $item['link']; ?>"
                                                    data-bs-toggle="tooltip"
                                                    aria-label="View Program"
                                                    data-bs-title="View Program"
                                                    data-bs-placement="right">
                                                    <i class="fa fa-fw fa-link"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            if ($item['published'] === "0") {
                                                echo "Unpublished";
                                            } elseif ($item['published'] === "1") {
                                                echo "Published";
                                            } else {
                                                echo "Invalid status value"; // Handle any other value not equal to 0 or 1
                                            } ?>
                                        <td><?php echo $item['link']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Comments Modal -->
<div class="modal fade" id="viewCommentsModal" tabindex="-1" aria-labelledby="viewCommentsModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCommentsModalLabel">Remarks & Comments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Schedule Item Comments -->
                <ul class="list-group text-start" id="schedule-item-comments-list"></ul>
                <div class="text-muted text-center py-2 mb-4 d-none" id="no-item-comments-msg">
                    You haven’t added any item-specific remarks yet.
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END View Comments Modal -->
<?= $this->endSection() ?>

<?= $this->section('other-styles') ?>
<style>
    .dark .dt-scroll-body {
        border-color: #1a1f28 !important;
    }

    .ripple-dot {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 10px;
        height: 10px;
        background-color: #d61f47;
        border-radius: 50%;
        z-index: 10;
        box-shadow: 0 0 0 rgba(214, 31, 71, 0.7);
    }

    .ripple-dot::before {
        content: "";
        top: 0;
        right: 0;
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: #d61f47;
        border-radius: 50%;
        z-index: -1;
        animation: rippleEffect 1.5s ease-out infinite;
    }

    @keyframes rippleEffect {
        0% {
            transform: scale(0.5);
            opacity: 0.8;
        }

        70% {
            transform: scale(2.5);
            opacity: 0;
        }

        100% {
            transform: scale(3);
            opacity: 0;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script src="<?= base_url('assets/js/plugins/datatables-buttons-jszip/jszip.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins/datatables-buttons/buttons.print.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins/datatables-buttons/buttons.html5.min.js'); ?>"></script>

<script>
    // Datatable for Schedule Item
    jQuery(document).ready(function() {
        const viewCache = {}; // { videoId: views }

        if (jQuery('#table-schedule').length) {
            jQuery('#table-schedule').DataTable({
                pagingType: "full_numbers",
                pageLength: 10,
                lengthMenu: [
                    [5, 10, 15, 20, 50],
                    [5, 10, 15, 20, 50]
                ],
                autoWidth: true,
                scrollX: true,
                responsive: false,
                stateSave: false,
                info: true,
                searching: false,
                columnDefs: [{
                        targets: ['noOrder'],
                        orderable: false,
                    },
                    {
                        targets: '_all',
                        className: 'text-nowrap'
                    },
                    {
                        targets: ['hiddenCols'],
                        visible: false,
                        searchable: false
                    },
                    {
                        targets: ['publishStatus'],
                        render: function(data, type, row, meta) {
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
                        targets: ['dataIndex'],
                        width: "5%",
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        targets: ['platform'],
                        render: function(data, type, row, meta) {
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
                        targets: ['views'],
                        orderable: false,
                        render: function(data, type, row, meta) {
                            const platform = row[4]?.toLowerCase() || '';
                            const link = row[13];
                            let videoId = getYouTubeVideoId(link);

                            if (platform.includes('youtube') && videoId) {
                                // For export or hidden rendering
                                if (type !== 'display') {
                                    return viewCache[videoId] ? formatNumberWithCommas(viewCache[videoId]) : 'Pending';
                                }

                                const placeholderId = `yt-views-${meta.row}`;

                                if (viewCache[videoId] !== undefined) {
                                    return `<span id="${placeholderId}">${formatNumberWithCommas(viewCache[videoId])}</span>`;
                                } else {
                                    setTimeout(() => {
                                        fetchYouTubeViews(videoId, function(views) {
                                            viewCache[videoId] = views;
                                            $(`#${placeholderId}`).text(formatNumberWithCommas(views));
                                        });
                                    }, 0);

                                    return `<span id="${placeholderId}">Loading...</span>`;
                                }
                            }

                            return type === 'display' ? 'N/A' : '';
                        }
                    }
                ],
                dom: "<'row'<'col-sm-12'<'text-center bg-body-light py-2 mb-2'B>>>" +
                    "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [{
                        extend: 'copy',
                        text: 'Copy',
                        title: 'ITN Digital Schedule',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13]
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        title: 'ITN Digital Schedule',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13]
                        },
                        filename: function() {
                            return getExportFileName()
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'ITN Digital Schedule',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13]
                        },
                        filename: function() {
                            return getExportFileName()
                        }
                    },
                    {
                        extend: 'csv',
                        text: 'CSV',
                        title: 'ITN Digital Schedule',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13]
                        },
                        filename: function() {
                            return getExportFileName()
                        }
                    }
                ],
            });
        }
    });

    $(document).on('click', '#view-comments-button', function(e) {
        e.preventDefault();

        const button = $(this);
        const scheduleId = button.data('schedule-id');
        const scheduleItemId = button.data('schedule-item-id');

        // Elements
        const itemCommentsList = $('#schedule-item-comments-list').empty();

        $('#no-item-comments-msg').addClass('d-none');

        $.ajax({
            url: '<?= base_url('daily-schedule/fetch-comments') ?>',
            method: 'POST',
            data: {
                schedule_id: scheduleId,
                item_id: scheduleItemId
            },
            dataType: 'json',
            success: function(res) {
                // Render item comments
                if (res.item_comments) {
                    itemCommentsList.append(
                        `<div class="text-muted text-center py-2">${res.item_comments}</div>`
                    );
                } else {
                    $('#no-item-comments-msg').removeClass('d-none');
                }

                $('#viewCommentsModal').modal('show');
            },
            error: function(xhr) {
                toast.fire({
                    title: 'Failed',
                    icon: 'error',
                    html: xhr.responseJSON?.message || 'Something went wrong.',
                    showCancelButton: false,
                    showConfirmButton: true
                }).then(() => window.location.reload());
            }
        });
    });

    // Delete Schedules Function
    jQuery(document).on('click', '#delete-schedule-item-button', function(event) {
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
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json', // Ensures the response is parsed automatically
                    success: function(response) {
                        if (response.status === 'success') {
                            toast.fire({
                                title: "Success",
                                icon: 'success',
                                html: response.message
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            toast.fire({
                                title: "Error",
                                icon: 'error',
                                html: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        toast.fire({
                            title: "Error",
                            icon: 'error',
                            html: "Something went wrong. Please try again."
                        });
                        console.error("AJAX error:", status, error);
                    }
                });
            }
        });
    });

    function getYouTubeVideoId(url) {
        if (!url) return null;

        const regExp = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
        const match = url.match(regExp);

        return match && match[1] ? match[1] : null;
    }

    function fetchYouTubeViews(videoId, callback) {
        const apiKey = '<?= $system_settings['youtubeDataGoogleApi'] ?>';
        const apiUrl = `https://www.googleapis.com/youtube/v3/videos?part=statistics&id=${videoId}&key=${apiKey}`;

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                const views = data.items[0]?.statistics?.viewCount || 'N/A';
                callback(views);
            })
            .catch(() => callback('N/A'));
    }

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>
<?= $this->endSection() ?>