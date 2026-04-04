jQuery(document).ready(function ($) {
	/**
	 * Search form logic
	 */
	$('#irbis-search-form .js-search-by').on('change', function () {
		var $currentRadio = $('input[name=irbis_search_by]:checked', '#irbis-search-form');
		var $currentRadioPlaceholder = $currentRadio.data('placeholderForSearchString');
		var $searchStringInput = $('#search-string');
		$searchStringInput.attr('placeholder', $currentRadioPlaceholder);
		$searchStringInput.focus();
	})
})
