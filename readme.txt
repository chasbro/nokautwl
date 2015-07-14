=== NokautWL ===
Contributors: Nokaut.pl Sp z o.o.
Tags: nokaut
Requires at least: 3.0.1
Tested up to: 4.2.2
Stable tag: 1.0.2
License: MIT
License URI: http://opensource.org/licenses/MIT

Integration with nokaut.pl search api, easy configuration, customizable templates.

== Description ==

Plugin simply integrate nokaut.pl search api with wordpress blog.

Features:

* main products comparison page
* wordpress category products comparison page
* product page
* short tags for posts (product text link, product box, products boxes)

This plugin uses Nokaut.pl Search API KIT (https://github.com/nokaut/api-kit).
Nokaut.pl Search API KIT code is licensed under [MIT](https://github.com/nokaut/api-kit/blob/master/LICENSE) license.

This plugin provides a simple way for you to use the [Twig templating engine](http://twig.sensiolabs.org/) with plugin templates.
Read [Twig documentation](http://twig.sensiolabs.org/documentation) for more technical details.
Read [Twig license](http://twig.sensiolabs.org/license) to clarify the legal aspects.

This plugin integrates [Bootstrap](http://getbootstrap.com/) framework.
Bootstrap code is licensed under [MIT](https://github.com/twbs/bootstrap/blob/master/LICENSE) license.
You can simply turn off Bootstrap integration with template changes.

== Installation ==

1. Extract files from achive. Upload `nokaut-wl/` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure plugin ('/wp-admin/options-general.php?page=nokautwl-config')
4. Turn on premalinks (http://codex.wordpress.org/Using_Permalinks)
5. Place `<?php echo \NokautWL\View\Products\ShortCode\ProductsBox::render(null,4,4) ?>` in your category templates (just after `<div id="content" class="site-content" role="main">`)
6. Place short tags in you posts.

== Frequently Asked Questions ==

= Where can I find more documentation? =

After plugin instalation, go to plugin configuration page '/wp-admin/options-general.php?page=nokautwl-config' and read help.

== Screenshots ==

1. Plugin configuration.

== Changelog ==

= 1.1.0 =
* Update/fix admin documentation
* Fix product url cleaning from WP url
* Fix list product click url

= 1.0.2 =
* Click url change to nokaut.click
* Update tested WordPress version

= 1.0.1 =
* Readme changes

= 1.0.0 =
* Initial version

== Upgrade Notice ==

= 1.0.0 =
* Initial version, no upgrade needed.