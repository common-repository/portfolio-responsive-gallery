<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Portfolio_Responsive_Gallery
 * @subpackage Portfolio_Responsive_Gallery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Portfolio_Responsive_Gallery
 * @subpackage Portfolio_Responsive_Gallery/admin
 * @author     Portfolio Team <info@ays-pro.com>
 */
class Portfolio_Responsive_Gallery_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    private $portfolio_obj;
    private $portfolio_attributes_obj;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_filter('set-screen-option', array(__CLASS__, 'set_screen'), 10, 3);
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook_suffix) {

        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');

        if (false === strpos($hook_suffix, $this->plugin_name)) {
            return;
        }

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Portfolio_Responsive_Gallery_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Portfolio_Responsive_Gallery_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('ays_prg_animate.css', plugin_dir_url(__FILE__) . 'css/animate.min.css', array(), '3.7.2', 'all');
        wp_enqueue_style('ays_prg_font_awesome', plugin_dir_url(__FILE__) . 'css/font_awesome_all.min.css', array(), '5.9.0', 'all');
        wp_enqueue_style('ays_prg_fa_v4_shims', plugin_dir_url(__FILE__) . 'css/font_awesome_v4-shims.min.css', array(), '5.9.0', 'all');
        wp_enqueue_style('ays_prg_bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), '4.3.1', 'all');
        wp_enqueue_style('ays-prg-select2', plugin_dir_url(__FILE__) . 'css/select2.min.css', array(), '4.0.7', 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/portfolio-responsive-gallery-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook_suffix) {

        if (false === strpos($hook_suffix, $this->plugin_name)) {
            return;
        }

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Portfolio_Responsive_Gallery_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Portfolio_Responsive_Gallery_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script('jquery-effects-core');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_media();
        wp_enqueue_editor();
        wp_enqueue_script('ays_prg_popper', plugin_dir_url(__FILE__) . 'js/popper.min.js', array('jquery'), '1.14.7', true);
        wp_enqueue_script('ays_prg_bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), '4.3.1', true);
        wp_enqueue_script('ays_prg_select2', plugin_dir_url(__FILE__) . 'js/select2.min.js', array('jquery'), '4.0.7', true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/portfolio-responsive-gallery-admin.js', array('jquery'), $this->version, true);
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */

    public function add_plugin_admin_menu() {

        add_options_page(__('Portfolio Responsive Gallery', $this->plugin_name), __('Portfolio Responsive Gallery', $this->plugin_name), 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));

        $hook_gallery = add_menu_page(__('Portfolio Responsive Gallery', $this->plugin_name), __('Portfolio Responsive Gallery', $this->plugin_name), 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'), AYS_PRG_ADMIN_URL . 'images/portfolio_icon.png', 6);

        add_action("load-$hook_gallery", [$this, 'screen_option_portfolio']);

        $hook_portfolio = add_submenu_page(
            $this->plugin_name,
            __('Portfolios', $this->plugin_name),
            __('Portfolios', $this->plugin_name),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page')
        );

        add_action("load-$hook_portfolio", [$this, 'screen_option_portfolio']);

        $hook_portfolio_attributes = add_submenu_page(
            $this->plugin_name,
            __('Attributes', $this->plugin_name),
            __('Attributes', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-portfolio-attributes',
            array($this, 'display_plugin_portfolio_attributes_page')
        );

        add_action("load-$hook_portfolio_attributes", [$this, 'screen_option_portfolio_attributes']);
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */

    public function add_action_links($links) {
        /*
         *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
         */
        $settings_link = array(
            '<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge($settings_link, $links);

    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */

    public function display_plugin_setup_page() {
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
        case 'add':
            include_once 'partials/actions/portfolio-responsive-gallery-admin-actions.php';
            break;
        case 'edit':
            include_once 'partials/actions/portfolio-responsive-gallery-admin-actions.php';
            break;
        default:
            include_once 'partials/portfolio-responsive-gallery-admin-display.php';
        }
    }

    public function display_plugin_portfolio_attributes_page() {
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
        case 'add':
            include_once 'partials/attributes/actions/portfolio-responsive-gallery-attributes-actions.php';
            break;
        case 'edit':
            include_once 'partials/attributes/actions/portfolio-responsive-gallery-attributes-actions.php';
            break;
        default:
            include_once 'partials/attributes/portfolio-responsive-gallery-attributes-display.php';
        }
    }

    public static function set_screen($status, $option, $value) {
        return $value;
    }

    public function screen_option_portfolio() {
        $option = 'per_page';
        $args = [
            'label' => __('Portfolios', $this->plugin_name),
            'default' => 5,
            'option' => 'portfolios_per_page',
        ];

        add_screen_option($option, $args);
        $this->portfolio_obj = new Portfolio_Responsive_Gallery_List_Table($this->plugin_name);
    }

    public function screen_option_portfolio_attributes() {
        $option = 'per_page';
        $args = [
            'label' => __('Attributes', $this->plugin_name),
            'default' => 5,
            'option' => 'attributes_per_page',
        ];

        add_screen_option($option, $args);
        $this->portfolio_attributes_obj = new Portfolio_Responsive_Gallery_Attributes_List_Table($this->plugin_name);
    }

    public function ays_get_attr_for_project() {
        if (isset($_REQUEST["action"]) && $_REQUEST["action"] == 'ays_get_attr_for_project') {
            global $wpdb;
            $sql = "SELECT * FROM {$wpdb->prefix}ays_portfolio_attributes WHERE `published`='1'";
            $results = $wpdb->get_results($sql, 'ARRAY_A');
            echo json_encode($results);
            wp_die();
        }
    }

    public static function ays_get_rpg_options() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ays_portfolio';
        $res = $wpdb->get_results("SELECT id, name, width, height FROM " . $table_name . "");
        $aysGlobal_array = array();

        foreach ($res as $ays_res_options) {
            $aysStatic_array = array();
            $aysStatic_array[] = $ays_res_options->id;
            $aysStatic_array[] = $ays_res_options->title;
            $aysStatic_array[] = $ays_res_options->width;
            $aysStatic_array[] = $ays_res_options->height;
            $aysGlobal_array[] = $aysStatic_array;
        }
        return $aysGlobal_array;
    }

    function ays_rpg_register_tinymce_plugin($plugin_array) {
        $plugin_array['ays_rpg_button_mce'] = AYS_rpg_BASE_URL . '/ays_rpg_shortcode.js';
        return $plugin_array;
    }

    function ays_rpg_add_tinymce_button($buttons) {
        $buttons[] = "ays_rpg_button_mce";
        return $buttons;
    }

    function gen_ays_rpg_shortcode_callback() {
        $shortcode_data = $this->ays_get_rpg_options();

        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title><?php echo __('Gallery Photo Gallery', $this->plugin_name); ?></title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
                <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
                <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>

                <?php
wp_print_scripts('jquery');
        ?>
                <base target="_self">
            </head>
            <body id="link" onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" dir="ltr" class="forceColors">
                <div class="select-sb">

              <table align="center">
                  <tr>
                    <td><label for="ays_rpg">Gallery</label></td>
                    <td>
                      <span>
                                <select id="ays_rpg" style="padding: 2px; height: 25px; font-size: 16px;width:100%;">
                            <option>--Select Gallery--</option>
                                <?php foreach ($shortcode_data as $index => $data) {
            echo '<option id="' . $data[0] . '" value="' . $data[0] . '" mw="' . $data[2] . '" mh="' . $data[3] . '" class="ays_rpg_options">' . $data[1] . '</option>';
        }

        ?>
                                </select>
                            </span>
                    </td>
                  </tr>
                  <tr>
                    <td><label for="ays_rpg_nk_width_get">Gallery width</label></td>
                    <td><input type="number" name="ays_sl_nk_width_get" id="ays_rpg_nk_width_get" style="padding: 2px; height: 25px; font-size: 16px;"></td>
                  </tr>
                  <tr>
                    <td><label for="ays_rpg_nk_height_get">Gallery height</label></td>
                    <td><input type="number" name="ays_rpg_nk_height_get" id="ays_rpg_nk_height_get" style="padding: 2px; height: 25px; font-size: 16px;"></td>
                  </tr>
              </table>
                </div>
                <div class="mceActionPanel">
                    <input type="submit" id="insert" name="insert" value="Insert" onClick="rpg_insert_shortcode();"/>
                </div>
            <script>
                jQuery("#ays_rpg").change(function(event){
                        var ays_rpg_mw = jQuery( "#ays_rpg option:selected").attr("mw");
                        var ays_rpg_mh = jQuery( "#ays_rpg option:selected").attr("mh");
                        jQuery("#ays_rpg_nk_width_get").val(ays_rpg_mw);
                        jQuery("#ays_rpg_nk_height_get").val(ays_rpg_mh);
                    });
            </script>
            <script type="text/javascript">
                function rpg_insert_shortcode() {
                    var mw = document.getElementById('ays_rpg_nk_width_get').value;
                    var mh = document.getElementById('ays_rpg_nk_height_get').value;
                    var tagtext = '[gallery_p_gallery id="' + document.getElementById('ays_rpg')[document.getElementById('ays_rpg').selectedIndex].id + '" w="'+ mw +'" h="'+ mh +'"]';
                    window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
                    tinyMCEPopup.close();
                }
              </script>

            </body>
          </html>
          <?php
die();
    }

    public function ays_get_all_image_sizes() {
        $image_sizes = array();
        global $_wp_additional_image_sizes;
        $default_image_sizes = array('thumbnail', 'medium', 'medium_large', 'large');

        foreach ($default_image_sizes as $size) {
            $image_sizes[$size]['width'] = intval(get_option("{$size}_size_w"));
            $image_sizes[$size]['height'] = intval(get_option("{$size}_size_h"));
            $image_sizes[$size]['crop'] = get_option("{$size}_crop") ? get_option("{$size}_crop") : false;
        }

        if (isset($_wp_additional_image_sizes) && count($_wp_additional_image_sizes)) {
            $image_sizes = array_merge($image_sizes, $_wp_additional_image_sizes);
        }

        return $image_sizes;
    }

}
