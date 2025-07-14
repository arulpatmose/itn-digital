// Override a few default classes
// Check if jQuery and DataTables are loaded
if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
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
} else {
    console.warn('jQuery or DataTables is not loaded.');
}