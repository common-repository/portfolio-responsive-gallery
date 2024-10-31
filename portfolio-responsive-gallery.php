<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ays-pro.com/
 * @since             1.0.0
 * @package           Portfolio_Responsive_Gallery
 *
 * @wordpress-plugin
 * Plugin Name:       Portfolio Responsive Gallery
 * Plugin URI:        https://ays-pro.com/index.php/wordpress/portfolio-responsive-gallery/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Portfolio Team
 * Author URI:        https://ays-pro.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       portfolio-responsive-gallery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if( ! defined( 'AYS_PRG_BASE_URL' ) ) {
    define( 'AYS_PRG_BASE_URL', plugin_dir_url(__FILE__ ) );
}

if( ! defined( 'AYS_PRG_ADMIN_URL' ) ) {
    define( 'AYS_PRG_ADMIN_URL', plugin_dir_url(__FILE__ ) . 'admin/' );
}


if( ! defined( 'AYS_PRG_PUBLIC_URL' ) ) {
    define( 'AYS_PRG_PUBLIC_URL', plugin_dir_url(__FILE__ ) . 'public/' );
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PRG_NAME_VERSION', '1.0.0' );
define( 'PRG_NAME', 'portfolio-responsive-gallery' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-portfolio-responsive-gallery-activator.php
 */
function activate_portfolio_responsive_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-portfolio-responsive-gallery-activator.php';
	Portfolio_Responsive_Gallery_Activator::ays_prg_db_check();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-portfolio-responsive-gallery-deactivator.php
 */
function deactivate_portfolio_responsive_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-portfolio-responsive-gallery-deactivator.php';
	Portfolio_Responsive_Gallery_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_portfolio_responsive_gallery' );
register_deactivation_hook( __FILE__, 'deactivate_portfolio_responsive_gallery' );

add_action( 'plugins_loaded', 'activate_portfolio_responsive_gallery' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-portfolio-responsive-gallery.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_portfolio_responsive_gallery() {

    add_action( 'admin_notices', 'general_prg_admin_notice' );
	$plugin = new Portfolio_Responsive_Gallery();
	$plugin->run();

}


function general_prg_admin_notice(){
    if ( isset($_GET['page']) && strpos($_GET['page'], PRG_NAME) !== false ) {
        ?>
             <div class="ays-notice-banner">
                <div class="navigation-bar">
                    <div id="navigation-container">
                        <a class="logo-container" href="https://ays-pro.com/" target="_blank">
                            <img class="logo" src="<?php echo AYS_PRG_ADMIN_URL . 'images/ays_pro.png'; ?>" alt="AYS Pro logo" title="AYS Pro"/>
                        </a>
                        <ul id="menu">
                            <li><a class="ays-btn" href="https://freedemo.ays-pro.com/portfolio-responsive-gallery/" target="_blank">Demo</a></li>
                            <li><a class="ays-btn" href="https://ays-pro.com/index.php/wordpress/portfolio-responsive-gallery/" target="_blank">PRO</a></li>
                            <li><a class="ays-btn" href="https://wordpress.org/support/plugin/portfolio-responsive-gallery/" target="_blank">Support Chat</a></li>
                            <li><a class="ays-btn" href="https://ays-pro.com/index.php/contact/" target="_blank">Contact us</a></li>
                        </ul>
                    </div>
                </div>
             </div>
        <?php
    }
}


run_portfolio_responsive_gallery();
