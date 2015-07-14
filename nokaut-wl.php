<?php
/*
Plugin Name: NokautWL
Plugin URI: http://nokaut.pl/
Description: Easy integration Nokaut.pl search to your wordpress website.
Version: 1.1.0
Author: Nokaut.pl Sp z o.o.
Author URI: http://nokaut.pl/wordpress-plugins/
License: MIT
*/

/*
Copyright (c) 2014 Nokaut.pl sp. z o.o.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

NokautWL uses some extra components:
1. Twig Template Engine:
- Source: http://twig.sensiolabs.org/
- License: new BSD License
- License URI: http://twig.sensiolabs.org/license
2. Bootstrap
- Source: http://getbootstrap.com/
- License: MIT License
- License URI: https://github.com/twbs/bootstrap/blob/master/LICENSE
3. Nokaut.pl Search API KIT
- Source: https://github.com/nokaut/api-kit
- License: MIT License
- License URI: https://github.com/nokaut/api-kit/blob/master/LICENSE
*/

error_reporting(E_ALL);

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('NOKAUTWL_VERSION', '1.0');
define('NOKAUTWL_MINIMUM_WP_VERSION', '3.0');
define('NOKAUTWL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('NOKAUTWL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NOKAUTWL_THEME_PUBLIC_DIR', get_template_directory() . '/nokaut-wl/public/');
define('NOKAUTWL_THEME_PUBLIC_URL', get_template_directory_uri() . '/nokaut-wl/public/');
define('NOKAUTWL_THEME_TEMPLATE_DIR', get_template_directory() . '/nokaut-wl/templates/');
define('NOKAUTWL_PLUGIN_TEMPLATE_DIR', plugin_dir_path(__FILE__) . 'templates/');

require_once NOKAUTWL_PLUGIN_DIR . "nokaut-wl-autoload.php";

register_activation_hook(__FILE__, array('NokautWL\\NokautWL', 'activate'));
register_deactivation_hook(__FILE__, array('NokautWL\\NokautWL', 'deactivate'));

/**
 * Plugin init
 */
\NokautWL\NokautWL::init();

if (is_admin()) {
    \NokautWL\Admin\Admin::init();
}
