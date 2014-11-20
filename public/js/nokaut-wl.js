(function ($) {
	"use strict";
	$(document).ready(function () {
		// Search on products page
		$("#nokaut-wl-search-form").submit(function () {
			if (!$("#nokaut-wl-search-input").val().trim()) {
				return false;
			}
			if ($("#nokaut-wl-search-current-category:checked").val() == 1) {
				// porownywarka w kategorii
				var url = $("#nokaut-wl-search-form").attr('data-search-category-url-template').replace(/%s/g, encodeURIComponent($("#nokaut-wl-search-input").val().trim()));
			} else {
				// glowna porownywarka
				var url = $("#nokaut-wl-search-form").attr('data-search-global-url-template').replace(/%s/g, encodeURIComponent($("#nokaut-wl-search-input").val().trim()));
			}

			$("#nokaut-wl-search-form").attr('action', url);
		});

		$("#nokaut-wl-search-button").click(function () {
			$("#nokaut-wl-search-form").submit();
		});

		// Sorting on product page
		$("#nokaut-wl-offer-sort-default").click(function () {
			$('ul.list-group>li').tsort();
			$("#nokaut-wl-offer-sort-default").addClass('active');
			$("#nokaut-wl-offer-sort-price-asc").removeClass('active');
		});

		$("#nokaut-wl-offer-sort-price-asc").click(function () {
			$('ul.list-group>li').tsort('span.nokaut-wl-price', {data: 'sort-value'});
			$("#nokaut-wl-offer-sort-default").removeClass('active');
			$("#nokaut-wl-offer-sort-price-asc").addClass('active');
		});

		// ShortTag Product Tooltip
		$('span.nokautwl-tooltip>a[data-toggle="tooltip"]').tooltip({
			html: true,
			placement: 'auto',
			delay: { show: 500, hide: 50 }
		})
	})
}(jQuery));