(function ($) {
	"use strict";
	$(document).ready(function () {
		$('input[id^="nokautwl_categories"]').select2({
			placeholder: "Select a category",
			allowClear: true,
			minimumInputLength: 2,
			tags: [],
			ajax: {
				url: ajax_object.ajax_url,
				dataType: 'json',
				type: "POST",
				quietMillis: 50,
				data: function (term) {
					return { action: 'category_search', term: term };
				},
				results: function (data) {
					return { results: $.map(data, function (item) {
						return { text: item.title, id: item.id }
					}) };
				}
			},
			initSelection: function (element, callback) {
				var data = [];
				var categories = element.val().split(",");
				var postData = {
					action: 'categories_get_by_ids',
					categories: categories
				};
				jQuery.post(ajax_object.ajax_url, postData, function (response) {
					$.each(response, function (index, value) {
						data.push({id: value.id, text: value.title});
						callback(data);
					});
				}, 'json');
			}
		});
	});
}(jQuery));