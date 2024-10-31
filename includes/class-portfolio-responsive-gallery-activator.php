<?php
global $ays_prg_db_version;
$ays_prg_db_version = '1.0.0';
/**
 * Fired during plugin activation
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Portfolio_Responsive_Gallery
 * @subpackage Portfolio_Responsive_Gallery/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Portfolio_Responsive_Gallery
 * @subpackage Portfolio_Responsive_Gallery/includes
 * @author     Portfolio Team <info@ays-pro.com>
 */
class Portfolio_Responsive_Gallery_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;
        global $ays_prg_db_version;
        $installed_ver = get_option("ays_prg_db_version");

        $table_portfolio = $wpdb->prefix . 'ays_portfolio';
        $table_portfolio_items = $wpdb->prefix . 'ays_portfolio_items';
        $table_attributes = $wpdb->prefix . 'ays_portfolio_attributes';
        $charset_collate = $wpdb->get_charset_collate();
        if ($installed_ver != $ays_prg_db_version) {

            $sql = "CREATE TABLE $table_portfolio (
                      	id INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                      	name VARCHAR(255) NOT NULL,
                      	description TEXT NOT NULL,
                      	options TEXT NOT NULL,
                      	PRIMARY KEY (id)
                    )$charset_collate;
                    CREATE TABLE $table_portfolio_items (
                      	id INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                      	portfolio_id INT(16) NOT NULL,
                      	name VARCHAR(255) NOT NULL,
                      	description TEXT NOT NULL,
                      	project_url TEXT NOT NULL,
                      	main_image TEXT NOT NULL,
                      	images TEXT NOT NULL,
                      	options TEXT NOT NULL,
                      	attributes TEXT NOT NULL,
                      	PRIMARY KEY (id)
                    )$charset_collate;
                    CREATE TABLE $table_attributes (
                        id INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                        name VARCHAR(255) NOT NULL,
                        slug VARCHAR(255) NOT NULL,
                        type VARCHAR(255) NOT NULL,
                        published TINYINT UNSIGNED NOT NULL,
                        PRIMARY KEY (`id`)
                    )$charset_collate;";
            dbDelta($sql);

            update_option('ays_prg_db_version', $ays_prg_db_version);
        }
    }
    private static function insert_default_values() {
        global $wpdb;
        $table_portfolio = $wpdb->prefix . 'ays_portfolio';
        $table_portfolio_items = $wpdb->prefix . 'ays_portfolio_items';
        $table_attributes = $wpdb->prefix . 'ays_portfolio_attributes';

        $ports_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_portfolio}");

        if ($ports_count == 0) {

            // DEFAULT CUSTOM ATTRIBUTES
            //
            $wpdb->insert($table_attributes, array(
                'name' => 'Author',
                'slug' => 'prg_attr_1',
                'type' => 'text',
                'published' => 1
            ));

            // DEFAULT PORTFOLIO
            //
            $wpdb->insert($table_portfolio, array(
                'name' => 'Default portfolio',
                'description' => '<p><a href=\"https://ays-pro.com\">Ays-pro plugins</a></p>',
                'options' => '{"accordion_number":1,"prg_width":0,"columns_count":3,"prg_view_type":"grid","show_prg_title":"on","show_prg_desc":"on","image_sizes":"full_size","show_project_title":"on","show_project_title_on":"image_hover","prg_images_loading":"all_loaded", "images_fit":"contain"}'
            ));

            // DEFAULT PROJECTS
            //
            $last_insert = $wpdb->insert_id;
            $wpdb->insert($table_portfolio_items, array(
                'portfolio_id' => $last_insert,
                'name' => 'Quiz Maker',
                'description' => 'This plugin allows you make unlimited number of quizzes . Each QUIZ can includes unlimited questions. Questions can be single choice, multiple choice and dropdown . Plugin is very user friendly and easy to use. With Quiz Maker you can also categorize the questions and quizzes.',
                'project_url' => 'https://wordpress.org/plugins/quiz-maker/',
                'main_image' => 'https://ps.w.org/quiz-maker/assets/screenshot-8.jpg',
                'images' => 'https://ps.w.org/quiz-maker/assets/screenshot-5.jpg***https://ps.w.org/quiz-maker/assets/screenshot-7.jpg',
                'options' => '{"url_open":"on"}',
                'attributes' => '{"prg_attr_1":"Quiz Maker Team"}'
            ));
            $wpdb->insert($table_portfolio_items, array(
                'portfolio_id' => $last_insert,
                'name' => 'Poll Maker',
                'description' => 'Poll maker is a reciprocal plugin which is very easy to use and is a perfect time-saving plugin. The advantage of the poll is that the users do not need to go to other websites for voting it is an instant poll.',
                'project_url' => 'https://wordpress.org/plugins/poll-maker/',
                'main_image' => 'https://ps.w.org/poll-maker/assets/screenshot-3.jpg',
                'images' => 'https://ps.w.org/poll-maker/assets/screenshot-8.jpg***https://ps.w.org/poll-maker/assets/screenshot-7.jpg',
                'options' => '{"url_open":"on"}',
                'attributes' => '{"prg_attr_1":"Poll Maker Team"}'
            ));
            $wpdb->insert($table_portfolio_items, array(
                'portfolio_id' => $last_insert,
                'name' => 'Secure Copy Content Protection',
                'description' => 'Secure Copy Content Protection is a plugin aimed at protecting web content from being plagiarized. As soon as Copy Protection plugin is activated it disables the right click, copy paste, content selection and copy shortcut keys on your website thus preventing content theft as well as web scraping, which are very popular nowadays.',
                'project_url' => 'https://wordpress.org/plugins/secure-copy-content-protection/',
                'main_image' => 'https://raw.githubusercontent.com/arm092/ays-pro-arm/master/images/cyber-security-12-e1547067689763.jpg',
                'images' => 'https://raw.githubusercontent.com/arm092/ays-pro-arm/master/images/sccp.png***https://raw.githubusercontent.com/arm092/ays-pro-arm/master/images/sccp2.png',
                'options' => '{"url_open":"on"}',
                'attributes' => '{"prg_attr_1":"Copy Content Protection Team"}'
            ));
            $wpdb->insert($table_portfolio_items, array(
                'portfolio_id' => $last_insert,
                'name' => 'Gallery — Photo Gallery',
                'description' => 'Gallery — Photo Gallery is a cool responsive image gallery plugin with awesome views. Which allows you to add unlimited galleries and unlimited images in your prefered format.',
                'project_url' => 'https://wordpress.org/plugins/gallery-photo-gallery/',
                'main_image' => 'https://ps.w.org/gallery-photo-gallery/assets/screenshot-4.jpg',
                'images' => 'https://ps.w.org/gallery-photo-gallery/assets/screenshot-3.jpg***https://ps.w.org/gallery-photo-gallery/assets/screenshot-5.jpg',
                'options' => '{"url_open":"on"}',
                'attributes' => '{"prg_attr_1":"Photo Gallery Team"}'
            ));
            $wpdb->insert($table_portfolio_items, array(
                'portfolio_id' => $last_insert,
                'name' => 'Ays Slider',
                'description' => 'Ays image slider is a progressive slider plugin, which is a great way to grab your audience’s attention with amazing and entertaining slideshows. Many customization options and a lot of cool effects makes this image slider stand out. The plugin allows you to add unlimited number of slides and customize the settings using different professional slider options.',
                'project_url' => 'https://wordpress.org/plugins/ays-slider/',
                'main_image' => 'https://raw.githubusercontent.com/arm092/ays-pro-arm/master/images/sld1.png',
                'images' => 'https://raw.githubusercontent.com/arm092/ays-pro-arm/master/images/sld2.png***https://raw.githubusercontent.com/arm092/ays-pro-arm/master/images/sld3.png',
                'options' => '{"url_open":"on"}',
                'attributes' => '{"prg_attr_1":"Image Slider Team"}'
            ));
            

        }
    }
    public static function ays_prg_db_check() {
        global $ays_prg_db_version;
        if (get_site_option('ays_prg_db_version') != $ays_prg_db_version) {
            self::activate();
            self::insert_default_values();
        }
    }
}
