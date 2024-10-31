<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Portfolio_Responsive_Gallery
 * @subpackage Portfolio_Responsive_Gallery/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php
        echo esc_html(get_admin_page_title());
        echo sprintf( '<a href="?page=%s&action=%s" class="portfolio_add_new_button page-title-action">%s</a>', esc_attr( $_REQUEST['page'] ), 'add', __("Add New", $this->plugin_name));
        ?>
    </h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <?php
                        $this->portfolio_obj->prepare_items();
                        $this->portfolio_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>