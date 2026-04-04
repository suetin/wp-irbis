jQuery(function ($) {
    $('.wp-irbis-search-form input[name="irbis_search_by"]').on('change', function () {
        $('.wp-irbis-search-form input[name="irbis_search_string"]').trigger('focus');
    });
});
