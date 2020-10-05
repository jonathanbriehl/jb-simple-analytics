<?php
/**
 * Plugin Name: Real Simple Analytics
 * Plugin URI: http://www.jonathanbriehl.com
 * Description: Inserts analytics code in the header of your WordPress site
 * Version: 1.0.1
 * Author: Jonathan Briehl
 * Author URI: https://www.jonathanbriehl.com
 * License: GPL2
 */

/**  Copyright 2020  Jonathan Briehl
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/* Adds Simple Analytics to Settings menu as a sub-menu item  */
if (!function_exists('jb_simple_analytics_menu')) {
    function jb_simple_analytics_menu()
    {
        add_options_page('Real Simple Analytics', 'Real Simple Analytics', 'manage_options',
            'jb-simple-analytics', 'jb_simple_analytics_settings_page');
    }

    add_action('admin_menu', 'jb_simple_analytics_menu');
}

/* Displays the page content for the Real Simple Analytics options */
if (!function_exists('jb_simple_analytics_settings_page')) {
    function jb_simple_analytics_settings_page()
    {
        // Check that the user has the required capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Variables for the field and option names
        $opt_name = 'jb_simple_analytics';
        $hidden_field_name = 'jb_submit_hidden';
        $data_field_name = 'jb_simple_analytics';

        // Read in existing option value from database
        $opt_val = get_option($opt_name);

        // See if the user has posted some information
        // If they did, this hidden field will be set to 'Y'
        if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
            // Read their posted value
            $opt_val = htmlspecialchars(stripslashes($_POST[$data_field_name]));

            // Save the posted value in the database
            update_option($opt_name, $opt_val);

            // Put an settings updated message on the screen
            ?>
            <div class="updated"><p><strong><?php _e('Saved.', 'jb-simple-analytics'); ?></strong></p></div>
            <?php

        }

        // Now display the settings editing screen
        echo '<div class="wrap">';

        // header
        echo "<h2>" . __('Real Simple Analytics', 'jb-simple-analytics') . "</h2>";

        // settings form
        ?>
        <form name="form1" method="post" action="">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

            <h3><?php _e("Analytics", 'jb-simple-analytics'); ?></h3>
            <p>
                <label style="font-weight: bold;" for="<?php echo $data_field_name; ?>">Analytics Code</label>
                <textarea style="width: 100%; height: 250px;" id="<?php echo $data_field_name; ?>"
                          name="<?php echo $data_field_name; ?>"><?php echo $opt_val; ?></textarea>
            </p>
            <p>
                Enter your analytics code (example: Google Analytics).
            </p>
            <hr/>

            <p class="submit">
                <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>"/>
            </p>

        </form>
        <?php
        echo '</div>';
    }
}

/* Grabs the analytics code and inserts it into the header */
if (!function_exists('jb_simple_analytics_header')) {
    function jb_simple_analytics_header()
    {
        $analytics_code = get_option('jb_simple_analytics');
        if (strlen($analytics_code) > 0) {
            echo "\n";
            echo "<!-- Real Simple Analytics -->";
            echo htmlspecialchars_decode(stripslashes($analytics_code));
            echo "\n";
        }
    }

    add_action('wp_head', 'jb_simple_analytics_header', 5);
}

/* Add "Settings" link to plugin listing on the WordPress site plugin page */
if (!function_exists('jb_simple_analytics_plugin_settings_link')) {
    function jb_simple_analytics_plugin_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=jb-simple-analytics">' . __("Settings", "jb-simple-analytics") . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_$plugin", 'jb_simple_analytics_plugin_settings_link');
}

/* Check For Plugin Updates - host hashed for privacy */
require plugin_dir_path(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://code.jonathanbriehl.com/wordpress-plugins/jb-simple-analytics/update.php?domain=' . md5($_SERVER['HTTP_HOST']),
    __FILE__, //Full path to the main plugin file or functions.php.
    'jb-simple-analytics'
);