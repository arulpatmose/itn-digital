var uri = new URI();
var _Protocol = uri.protocol();
var _AppUri = uri.origin();
var _HostName = uri.hostname();
var _SearchQuery = uri.query();
var _PathName = uri.pathname();

// Function to check if the URL is Valid
function IsURL(url) {
    const urlPattern = /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/;
    return urlPattern.test(url);
}

// Set Default file name for Datatable Exports
function getExportFileName() {
    var d = new Date();
    var n = d.getTime();
    return 'ITN Digital Schedule - ' + n;
};

/*
 * Ion Range Slider, for more examples you can check out https://github.com/IonDen/ion.rangeSlider
 *
 * Helpers.run('jq-rangeslider');
 *
 * Example usage:
 *
 * <input type="text" class="js-rangeslider form-control" value="50">
 *
 */
jQuery('.js-rangeslider:not(.js-rangeslider-enabled)').each((index, element) => {
    let el = jQuery(element);

    // Add .js-rangeslider-enabled class to tag it as activated and init it
    jQuery(element).addClass('js-rangeslider-enabled').ionRangeSlider({
        input_values_separator: ';',
        skin: el.data('skin') || 'round'
    });
});

/**
 * Handles dynamic logo switching based on the current theme (dark or light).
 * 
 * This script:
 * - Selects the HTML root element and logo elements (dark and light variants).
 * - Defines a function to toggle the visibility of the logos depending on whether
 *   the page is in dark mode (by checking for the "dark" class on <html>).
 * - Executes the logo toggle function on page load.
 * - Observes changes to the <html> element's class attribute to dynamically
 *   switch logos when the theme is toggled via JavaScript.
 * 
 * Dependencies:
 * - jQuery
 * 
 * Usage:
 * Ensure the page has `.dark-logo` and `.light-logo` elements,
 * and that theme switching applies/removes the `dark` class on the `<html>` tag.
 */

$(document).ready(function () {
    const $html = $("html"); // Select the <html> element
    const $darkLogo = $(".dark-logo"); // Select the dark logo
    const $lightLogo = $(".light-logo"); // Select the light logo

    // Function to toggle logo visibility
    const updateLogoVisibility = function () {
        if ($html.hasClass("dark")) {
            $darkLogo.show(); // Show dark logo
            $lightLogo.hide(); // Hide light logo
        } else {
            $darkLogo.hide(); // Hide dark logo
            $lightLogo.show(); // Show light logo
        }
    };

    // Run on page load
    updateLogoVisibility();

    // Optional: Monitor class changes dynamically if theme switching is done via JavaScript
    const observer = new MutationObserver(function () {
        updateLogoVisibility();
    });

    observer.observe($html[0], { attributes: true, attributeFilter: ["class"] });
});

/**
 * Clears the saved DataTable state from localStorage and reloads the table via AJAX,
 * resetting pagination to the first page.
 *
 * @param {string} tableId - The ID of the DataTable to clear state and reload.
 *
 * @example
 * // Clear and reload a table with ID 'example-table', resetting to first page
 * clearStateAndReload('example-table');
 */
function clearStateAndReload(tableId) {
    if (jQuery('#' + tableId).length) {
        const stateKey = 'DataTables_' + window.location.pathname + '_' + tableId;
        localStorage.removeItem(stateKey);
        // Reload and reset pagination (true)
        jQuery('#' + tableId).DataTable().ajax.reload(null, true);
    }
}

/*
 * Bootstrap Tooltip, for more examples you can check out https://getbootstrap.com/docs/5.0/components/tooltips/
 *
 * Helpers.run('bs-tooltip');
 *
 * Example usage:
 *
 * <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" title="Tooltip Text">Example</button> or
 * <button type="button" class="btn btn-primary js-bs-tooltip" title="Tooltip Text">Example</button>
 *
 */
function bsTooltip() {
    let elements = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]:not(.js-bs-tooltip-enabled), .js-bs-tooltip:not(.js-bs-tooltip-enabled)'));

    window.helperBsTooltips = elements.map(el => {
        // Add .js-bs-tooltip-enabled class to tag it as activated
        el.classList.add('js-bs-tooltip-enabled');

        // Init Bootstrap Tooltip
        return new bootstrap.Tooltip(el, {
            container: el.dataset.bsContainer || '#page-container',
            animation: el.dataset.bsAnimation && el.dataset.bsAnimation.toLowerCase() == 'true' ? true : false,
        })
    });
}

/*
 * Select2, for more examples you can check out https://github.com/select2/select2
 *
 * Helpers.run('jq-select2');
 *
 * Example usage:
 *
 * <select class="js-select2 form-control" style="width: 100%;" data-placeholder="Choose one..">
 *   <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
 *   <option value="1">HTML</option>
 *   <option value="2">CSS</option>
 *   <option value="3">Javascript</option>
 * </select>
 *
 */

// Select2 for Commercials
jQuery(document).ready(function () {
    if (jQuery('#schedule-commercial').length) {
        let el = jQuery('#schedule-commercial');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
            ajax: {
                url: '/api/get-select-options',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        optionsType: 'commercial',
                        searchTerm: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    }
});

// Select2 for Programs

jQuery(document).ready(function () {
    if (jQuery('#schedule-program').length) {
        let el = jQuery('#schedule-program');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
            ajax: {
                url: '/api/get-select-options',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        optionsType: 'program',
                        searchTerm: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    }
});

// Select2 for Spots on Schedules
jQuery(document).ready(function () {
    if (jQuery('#schedule-spot').length) {
        let el = jQuery('#schedule-spot');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
            ajax: {
                url: '/api/get-select-options',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        optionsType: 'spots',
                        searchTerm: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    }
});

// Select2 for Spots on Platform
jQuery(document).ready(function () {
    if (jQuery('#schedule-platform').length) {
        let el = jQuery('#schedule-platform');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
        });
    }
});

// Select2 for Commercials on Schedules
jQuery(document).ready(function () {
    if (jQuery('#schedule-filters-commercial').length) {
        let el = jQuery('#schedule-filters-commercial');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
            ajax: {
                url: '/api/get-select-options',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        optionsType: 'commercial',
                        searchTerm: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true,
                allowClear: true
            }
        }).on('change', function (e) {
            clearStateAndReload('table-schedules');
        });
    }
});

// Select2 for Programs on Schedules
jQuery(document).ready(function () {
    if (jQuery('#schedule-filters-program').length) {
        let el = jQuery('#schedule-filters-program');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
            ajax: {
                url: '/api/get-select-options',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        optionsType: 'program',
                        searchTerm: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true,
                allowClear: true
            }
        }).on('change', function (e) {
            clearStateAndReload('table-schedules');
        });
    }
});

// Select2 for Clients on Schedules
jQuery(document).ready(function () {
    if (jQuery('#schedule-filters-client').length) {
        let el = jQuery('#schedule-filters-client');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
            ajax: {
                url: '/api/get-select-options',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        optionsType: 'client',
                        searchTerm: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true,
                allowClear: true
            }
        }).on('change', function (e) {
            clearStateAndReload('table-schedules');
        });
    }
});

// Select2 for Platforms on Schedules
jQuery(document).ready(function () {
    if (jQuery('#schedule-filters-platform').length) {
        let el = jQuery('#schedule-filters-platform');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
            minimumResultsForSearch: -1
        }).on('change', function (e) {
            clearStateAndReload('table-schedules');

            if (jQuery('#daily-schedule-items-wrapper').length) {
                window.location.href = URI().setSearch('platform', el.find(":selected").val());
            }
        });
    }
});

// Select2 for Formats on Schedules
jQuery(document).ready(function () {
    if (jQuery('#schedule-filters-format').length) {
        let el = jQuery('#schedule-filters-format');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
            minimumResultsForSearch: -1
        }).on('change', function (e) {
            clearStateAndReload('table-schedules');
        });
    }
});

// Select2 for Spots on Platform
jQuery(document).ready(function () {
    if (jQuery('#commercial-client').length) {
        let el = jQuery('#commercial-client');

        el.addClass('js-select2-enabled').select2({
            placeholder: el.data('placeholder') || false,
            dropdownParent: document.querySelector(el.data('container') || '#page-container'),
        });
    }
});

/*
 * Flatpickr init, for more examples you can check out https://github.com/flatpickr/flatpickr
 *
 * Helpers.run('js-flatpickr');
 *
 * Example usage:
 *
 * <input type="text" class="js-flatpickr form-control">
 *
 */
jQuery(document).ready(function () {
    if (jQuery('#schedule-dates').length) {
        let el = jQuery('#schedule-dates');

        el.addClass('js-flatpickr-enabled');

        flatpickr(el, {
            mode: "multiple",
            minDate: "today",
            allowInput: true,
            dateFormat: "Y-m-d"
        });
    }
});

// Disabled Multiple Date Picker on Edit Schedule Method
jQuery(document).ready(function () {
    if (jQuery('#schedule-dates-edit').length) {
        let el = jQuery('#schedule-dates-edit');

        el.addClass('js-flatpickr-enabled');

        flatpickr(el, {
            minDate: "today",
            dateFormat: "Y-m-d"
        });
    }
});

// Date Range Picker on Scheduels Table
jQuery(document).ready(function () {
    if (jQuery('#schedule-date-range').length) {
        let el = jQuery('#schedule-date-range');

        el.addClass('js-flatpickr-enabled');

        flatpickr(el, {
            dateFormat: 'Y-m-d',
            mode: 'range',
        });

        const fp = document.querySelector("#schedule-date-range")._flatpickr;

        fp.config.onClose.push(function (data) {
            clearStateAndReload('table-schedules');
        });
    }
});

// Flatpickr on Daily Schedule Page
jQuery(document).ready(function () {
    if (jQuery('#daily-schedule-date').length) {
        let el = jQuery('#daily-schedule-date');
        let date = el.data('schedule-date');

        el.addClass('js-flatpickr-enabled');

        flatpickr(el, {
            dateFormat: "Y-m-d",
            defaultDate: [date]
        });

        const fp = document.querySelector("#daily-schedule-date")._flatpickr;

        fp.config.onChange.push(function (data) {
            var _date = fp.formatDate(data[0], 'Y-m-d');

            window.location.href = URI().pathname('/daily-schedule/' + _date);
        });
    }
});

// Flatpickr on Schedule Items Add Page
jQuery(document).ready(function () {
    if (jQuery('#schedule-item-dates').length) {
        let el = jQuery('#schedule-item-dates');

        var disabledDates = jQuery('#schedule-item-dates').data('disabled');

        console.log(disabledDates.trim().split(', '));

        el.addClass('js-flatpickr-enabled');

        flatpickr(el, {
            mode: "multiple",
            minDate: "today",
            dateFormat: "Y-m-d",
            allowInput: true,
            disable: disabledDates.trim().split(', ')
        });
    }
});

// Trigger Search on Schedule Search Fitler Input Key Up
jQuery(document).ready(function () {
    if (jQuery('#schedule-filters-schedule').length) {
        let keyupTimer;
        jQuery('#schedule-filters-schedule').keyup(function () {
            clearTimeout(keyupTimer);
            keyupTimer = setTimeout(function () {
                jQuery('#table-schedules').DataTable().ajax.reload(null, false);
            }, 800);
        });
    }
});

// Clear Input on Click
function clearInput(target) {
    document.getElementById(target).value = "";
    clearStateAndReload('table-schedules');
}

// Clear Selection on Click
function clearSelection(target) {
    var el = document.getElementById(target);
    jQuery(el).val(null).trigger("change");
    clearStateAndReload('table-schedules');
}

// Formatting Schedule Extra Data function for row details - modify as you need
function formatScheduleExtra(d) {
    // `d` is the original data object for the row

    if (d.total_budget !== 0.00 || d.total_budget !== null) {
        var _totalBudget = d.total_budget.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    return (
        '<dl>' +
        '<dt>Marketing Executive:</dt>' +
        '<dd>' +
        d.marketing_ex +
        '</dd>' +
        '<dt>Total Budget:</dt>' +
        '<dd>' +
        _totalBudget +
        '</dd>' +
        '<dt>Added by:</dt>' +
        '<dd>' +
        d.added_by +
        '</dd>' +
        '<dt>Remarks:</dt>' +
        '<dd>' +
        d.remarks +
        '</dd>' +
        '</dl>'
    );
}

// Auto Calculate Schedule Budget 
jQuery(document).ready(function () {
    if (jQuery('#schedule-dates').length) {
        const flp = document.querySelector("#schedule-dates")._flatpickr;
        $("#schedule-budget").bind("keyup change", function (e) {
            var totalBudget = parseFloat(jQuery("#schedule-budget").val()) || 0;
            var datesCount = flp.selectedDates.length ? flp.selectedDates.length : 0;
            setDailyBudget(datesCount, totalBudget);
        });

        flp.config.onClose.push(function (data) {
            var totalBudget = parseFloat(jQuery("#schedule-budget").val()) || 0;
            var datesCount = flp.selectedDates.length ? flp.selectedDates.length : 0;
            setDailyBudget(datesCount, totalBudget);
        });

        function setDailyBudget(count, budget) {
            var amount = budget / count || 0;

            if (isNaN(amount) || !isFinite(amount)) {
                amount = 0;
            }

            var output = 'Calculated Daily Budget: ' + amount + '.00';

            jQuery(".daily-budget-value").text(output).show();
        }
    }
});

// Handle Select Rows on Daily Schedule Table
jQuery(document).ready(function ($) {
    // Handle row click to toggle checkbox, excluding links and buttons
    $('table tbody tr').click(function (event) {
        // Check if the clicked element is an <a> tag
        if (!$(event.target).is('input:checkbox, a, button')) {
            let checkbox = $(this).find('.select-row');
            checkbox.prop('checked', !checkbox.prop('checked'));
        }
    });

    // Handle "Select All" checkbox
    $('.daily-schedule-items-table .select-all').click(function () {
        let isChecked = $(this).prop('checked');
        $(this).closest('table').find('tbody .select-row').prop('checked', isChecked);
    });

    // Handle individual row checkboxes to update "Select All" checkbox state
    $('.daily-schedule-items-table .select-row').change(function () {
        let allChecked = $(this).closest('table').find('tbody .select-row:checked').length === $(this).closest('table').find('tbody .select-row').length;
        $(this).closest('table').find('.select-all').prop('checked', allChecked);
    });
});


// Handle Go Back funtion for Table Go Back Button
function goBack() {
    window.history.back();
}