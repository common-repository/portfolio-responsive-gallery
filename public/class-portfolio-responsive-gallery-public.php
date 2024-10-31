<?php

/** The public-facing functionality of the plugin.
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Portfolio_Responsive_Gallery
 * @subpackage Portfolio_Responsive_Gallery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Portfolio_Responsive_Gallery
 * @subpackage Portfolio_Responsive_Gallery/public
 * @author     Portfolio Team <info@ays-pro.com>
 */
class Portfolio_Responsive_Gallery_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_shortcode('portfolio_responsive_gallery', array($this, 'ays_portfolio_generate_shortcode'));

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Portfolio_Responsive_prg_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Portfolio_Responsive_prg_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style('ays_prg-animate', plugin_dir_url(__FILE__) . 'css/animate.min.css', array(), '3.7.2', 'all');
		wp_enqueue_style('prg.lightbox.css', plugin_dir_url(__FILE__) . 'css/lightbox.css', array(), '1.4.10', 'all');
		wp_enqueue_style('prg.owl.carousel.css', plugin_dir_url(__FILE__) . 'css/owl.carousel.min.css', array(), '2.3.4', 'all');
		wp_enqueue_style('prg.owl.carousel.theme.css', plugin_dir_url(__FILE__) . 'css/owl.theme.default.min.css', array(), '2.3.4', 'all');
		wp_enqueue_style('prg_lightbox_styles', plugin_dir_url(__FILE__) . 'css/prg_lightbox_styles.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/portfolio-responsive-gallery-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Portfolio_Responsive_prg_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Portfolio_Responsive_prg_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script('imagesloaded.min.js', 'https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js', array('jquery'), '4', true);
		wp_enqueue_script('prg.freewall.js', plugin_dir_url(__FILE__) . 'js/freewall.js', array('jquery'), '1.0.6', true);

		wp_enqueue_script('prg.lightbox.core.js', plugin_dir_url(__FILE__) . 'js/core.js', array('jquery'), '1.4.10', true);
		wp_enqueue_script('prg.lightbox.transition.js', plugin_dir_url(__FILE__) . 'js/transition.js', array('jquery'), '1.4.10', true);
		wp_enqueue_script('prg.lightbox.background.js', plugin_dir_url(__FILE__) . 'js/background.js', array('jquery'), '1.4.10', true);
		wp_enqueue_script('prg.touch.js', plugin_dir_url(__FILE__) . 'js/touch.js', array('jquery'), '1.4.10', true);
		wp_enqueue_script('prg.lightbox.js', plugin_dir_url(__FILE__) . 'js/lightbox.js', array('jquery'), '1.4.10', true);

		wp_enqueue_script('prg.owl.carousel.js', plugin_dir_url(__FILE__) . 'js/owl.carousel.min.js', array('jquery'), '2.3.4', true);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/portfolio-responsive-gallery-public.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . "-ajax-public", plugin_dir_url(__FILE__) . 'js/prg-lightbox-ajax.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name . '-ajax-public', 'prg_ajax_public', array('ajax_url' => admin_url('admin-ajax.php')));

	}

	public function ays_initialize_portfolio_shortcode() {
		// add_shortcode('portfolio_responsive_gallery', array($this, 'ays_generate_portfolio'));
	}

	public function ays_get_image_size( $img, $size ) {
		global $wpdb;
		$this_site_path = trim(get_site_url(), "https:");
		if (strpos(trim($img, "https:"), $this_site_path) !== false) {
			$query      = "SELECT * FROM {$wpdb->prefix}posts WHERE `post_type` = 'attachment' AND `guid` = '$img'";
			$result_img = $wpdb->get_results($query, "ARRAY_A");
			if (empty($result_img)) {
				return $img;
			}

			$url_img = wp_get_attachment_image_src($result_img[0]['ID'], $size);
			if ($url_img === false) {
				return $img;
			} else {
				return $url_img[0];
			}
		} else {
			return $img;
		}

	}

	public function ays_generate_portfolio( $attr ) {
		$id = (isset($attr['id'])) ? absint($attr['id']) : null;
		if (!$id) {
			return "";
		}

		$portfolio = $this->get_portfolio_by_id($id);
		if (empty($portfolio)) {
			return "";
		}
		$title       = $portfolio["name"];
		$description = $portfolio["description"];
		$prg_items   = $this->get_projects_by_portfolio_id($id);

		$prg_options = json_decode($portfolio['options'], true);
		$prg_width   = isset($prg_options['prg_width']) && $prg_options['prg_width'] != null ? $prg_options['prg_width'] . "px" : "100%";
		/*
		 * Portfolio settings
		 */
		$image_sizes           = ($prg_options['image_sizes'] == '' || $prg_options['image_sizes'] === false) ? 'full_size' : $prg_options['image_sizes'];
		$show_project_title    = ($prg_options['show_project_title'] == '' || $prg_options['show_project_title'] == false) ? '' : $prg_options['show_project_title'];
		$show_project_title_on = ($prg_options['show_project_title_on'] == '' || $prg_options['show_project_title_on'] == false) ? 'project_image' : $prg_options['show_project_title_on'];
		$prg_images_loading    = $prg_options['prg_images_loading'];
		$show_prg_title        = isset($prg_options['show_prg_title']) ? $prg_options['show_prg_title'] : 'off';
		$show_prg_desc         = isset($prg_options['show_prg_desc']) ? $prg_options['show_prg_desc'] : 'off';

		/*
		 * Projects image settings
		 */

		$view    = $prg_options['prg_view_type'];
		$columns = (!isset($prg_options['columns_count'])) ? 3 : $prg_options['columns_count'];

		switch ( $view ) {
			case 'grid':
				$prg_item_class        = ".ays_portfolio_container_$id div.prg_grid_column_$id";
				$prg_view_selector     = "#prg_grid_row_$id";
				$prg_lightbox_selector = "prg_grid_column_$id";
				break;
			case 'mosaic':
				$prg_item_class        = ".ays_portfolio_container_$id .prg-cell";
				$prg_view_selector     = "#ays_mosaic_$id";
				$prg_lightbox_selector = "prg-cell";
				break;
		}

		if ($columns == null || $columns == 0) {
			$columns = 3;
		}

		if ($prg_images_loading == 'all_loaded') {
			$ays_images_all_loaded = "<div class='prg-loader prg_loader_$id'>
                                        <div style='width:100%;height:100%' class='lds-disk'>
                                            <div>
                                            <div></div>
                                            <div></div>
                                            </div>
                                        </div>
                                    </div>";
		} else {
			$ays_images_all_loaded = '';
		}

		if ($prg_images_loading == 'current_loaded') {
			$ays_prg_lazy_load                  = ".progress( function( instance, image ) {
                                if(image.isLoaded){
                                    $(image.img).parent().find('.ays_image_loading_div').css({
                                        'opacity': '1',
                                        'animation-name': 'fadeOut',
                                        'animation-duration': '1.2s',
                                    });
                                    setTimeout(function(){
                                        $(image.img).parent().find('.ays_image_loading_div').css({
                                            'display': 'none'
                                        });
                                        $(image.img).parent().find('div.prg_hover_mask').css({
                                            'display': 'flex'
                                        });
                                        $(image.img).css({
                                            'opacity': '1',
                                            'display': 'block',
                                            'animation': 'fadeInUp .5s',
                                            'z-index': 4,
                                        });
                                    },400);

                                }
                            })";
			$ays_prg_container_display_none_js  = "";
			$ays_prg_container_display_block_js = "";
			$ays_prg_container_error_message_js = "";
			$ays_prg_container_css              = "display: block;";
			$ays_images_lazy_loader_css         = ".ays_portfolio_container_$id .ays_image_loading_div {
                                                display: flex;
                                            }";
			$ays_prg_lazy_load_mosaic           = "let wall = new Freewall('#ays_freewal_$id');
            wall.reset({
                selector: '.prg-cell',
                animate: true,
                cellW: 25,
                cellH: 250,
                gutterX: 7.5,
                gutterY: 7.5,
                onResize: function() {
                    wall.fitWidth();

                }
            });
            wall.fitWidth();

            // for scroll bar appear;

            $(window).trigger('resize');";
			$ays_prg_lazy_load_mosaic_css       = ".prg-cell a>img {
                                                opacity: 0;
                                             }
                                             .prg-cell a div.prg_hover_mask {
                                                display: none;
                                             }";

		} else {
			$ays_prg_lazy_load                  = '';
			$ays_prg_container_display_none_js  = "$('.ays_portfolio_container_$id').css({'display': 'none'});";
			$ays_prg_container_display_block_js = "$('.ays_portfolio_container_$id').css({'display': 'block', 'animation-name': 'fadeIn'});";
			$ays_prg_container_error_message_js = "$('.ays_portfolio_container_$id').html(errorImage);";
			$ays_prg_container_css              = "display: none;";
			$ays_images_lazy_loader_css         = ".ays_portfolio_container_$id .ays_image_loading_div {
                                                display: none;
                                            }";
			$ays_prg_lazy_load_mosaic           = "";
			$ays_prg_lazy_load_mosaic_css       = "";
		}

		$column_width = 100 / $columns;
		$prg_view     = $ays_images_all_loaded;
		$prg_view     .= "<style>

                            $ays_images_lazy_loader_css

                            .ays_portfolio_container_$id {
                                $ays_prg_container_css
                                width: $prg_width;
                                max-width: 100%;
                                margin: 0 auto !important;
                            }
                            .ays_portfolio_container_$id i:before {
                                font-family: 'Font Awesome 5 Free' !important;
                            }
                            div.prg_grid_row div.prg_grid_column_$id a>img,
                            .prg-cell a>img {
                                opacity: 1;
                            }
                            #ays_project_images_ul img {
                                object-fit: " . (isset($prg_options['images_fit']) ? $prg_options['images_fit'] : "contain") . ";
                            }
                            $ays_prg_lazy_load_mosaic_css

                        </style>";
		if ($show_prg_title == "on") {
			$show_prg_title = "<h2 class='ays_portfolio_title'>" . wp_unslash($title) . "</h2>";
		} else {
			$show_prg_title = "";
		}
		if ($show_prg_desc == "on") {
			$show_prg_desc = "<h4 class='ays_portfolio_description'>" . wp_unslash($description) . "</h4>";
		} else {
			$show_prg_desc = "";
		}
		$show_prg_header = "<div class='ays_portfolio_header'>
                                    $show_prg_title
                                    $show_prg_desc
                                </div>";

		$prg_view .= "<div class='prg-container ays_portfolio_container_$id' style='width: $prg_width'> $show_prg_header";

		switch ( $view ) {
			case "mosaic":

				$prg_image_sizes = '';
				$prg_view        .= "<div class='prg_mosaic_container'><div class='prg_mosaic_row free_wall_$id' id='ays_freewal_$id'>";

				foreach ( $prg_items as $key => $project ) {

					if ($show_project_title == 'on') {
						$ays_show_title = "<div class='ays_project_title'>
                                                        <span>" . wp_unslash($project["name"]) . "</span>
                                                     </div>";
					} else {
						$ays_show_title = '';
					}

					if ($show_project_title_on == 'project_image') {
						$show_title_in_hover = "<div class='prg_hover_mask animated'><i class='fas fa-search-plus'></i></div> $ays_show_title ";
					} elseif ($show_project_title_on == 'image_hover') {
						$show_title_in_hover = "<div class='prg_hover_mask animated'><i class='fas fa-search-plus'></i> $ays_show_title </div>";
					}

					$project_id    = $project['id'];
					$project_image = $image_sizes != 'full_size' ? $this->ays_get_image_size($project['main_image'], $image_sizes) : $project['main_image'];
					$w             = 200 + 200 * (number_format((float) rand() / (float) getrandmax(), 2, ".", ""));
					$prg_view      .= "<div class='prg-cell' style='width:{$w}px; height: 250px; background-image: url($project_image)'>
                    <a href='javascript:void(0);' class='open_project_lightbox' data-project='$project_id'>
                    <div class='ays_image_loading_div'>
                        <img src='" . AYS_PRG_PUBLIC_URL . "images/ays_flower_spinner_loader.svg'>
                    </div>
                    <img style='visibility:hidden;' src='" . $project_image . "'/>
                    </a>
                    $show_title_in_hover
                    </div>";
				}
				$prg_view .= "</div></div>
                                <script>
                                    (function ($) {
                                        $(document).ready(function(){
			                                let wall = new Freewall('#ays_freewal_$id');
			                                wall.reset({
			                                	selector: '.prg-cell',
			                                	animate: true,
			                                	cellW: 25,
                                                cellH: 250,
                                                gutterX: 7.5,
                                                gutterY: 7.5,
			                                	onResize: function() {
                                                    wall.fitWidth();

			                                	}
			                                });
                                            wall.fitWidth();
                                            $(window).trigger('resize');

                                            $('.prg_mosaic_row .prg-cell').hover(function(){
                                                $(this).find('.prg_hover_mask').css('animation-name', 'fadeIn');
                                                $(this).find('.prg_hover_mask').css('animation-duration', '.5s');
                                            },
                                            function(){
                                                $(this).find('.prg_hover_mask').css('animation-name', 'fadeOut');
                                                $(this).find('.prg_hover_mask').css('animation-duration', '.5s');
                                            });
                                        });
                                    })(jQuery)
                                </script>";
				break;
			case "grid":
				$prg_view .= "<div class='prg_grid_row' id='prg_grid_row_$id'>";
				foreach ( $prg_items as $key => $project ) {

					if ($show_project_title == 'on') {
						$ays_show_title = "<div class='ays_project_title'>
                                                        <span>" . wp_unslash($project['name']) . "</span>
                                                     </div>";
					} else {
						$ays_show_title = '';
					}

					if ($show_project_title_on == 'project_image') {
						$show_title_in_hover = "<div class='prg_hover_mask animated'><i class='fas fa-search-plus'></i></div> $ays_show_title ";
					} elseif ($show_project_title_on == 'image_hover') {
						$show_title_in_hover = "<div class='prg_hover_mask animated'><i class='fas fa-search-plus'></i> $ays_show_title </div>";
					}

					$project_id    = $project['id'];
					$project_image = $this->ays_get_image_size($project['main_image'], $image_sizes);
					$prg_view      .= "<div class='prg_grid_col prg_grid_column_$id' style='width: calc(" . ($column_width - 1) . "% - 15px);' data-src=' $project_image'>
                        <div class='ays_portfolio_item_image' style='background-image: url($project_image)'>
                            <a href='javascript:void(0);' class='open_project_lightbox' data-project='$project_id'>
                                <div class='ays_image_loading_div'>
                                    <img src='" . AYS_PRG_PUBLIC_URL . "images/ays_flower_spinner_loader.svg'>
                                </div>
                                <img style='visibility:hidden;' src='" . $project_image . "'/>
                            </a>
                            $show_title_in_hover
                        </div>
                    </div>";
				}
				$prg_view .= "</div>
                            <script>
                                (function ($) {
                                    $(document).ready(function(){
                                        $('.prg_grid_column_$id .ays_portfolio_item_image').hover(function(){
                                            $(this).find('.prg_hover_mask').css('animation-name', 'fadeIn');
                                            $(this).find('.prg_hover_mask').css('animation-duration', '.5s');
                                            $(this).find('.prg_hover_mask').css('opacity', '1');
                                        },
                                        function(){
                                            $(this).find('.prg_hover_mask').css('animation-name', 'fadeOut');
                                            $(this).find('.prg_hover_mask').css('animation-duration', '.5s');
                                        });
                                    });
                                })(jQuery)
                            </script>";
				break;
		}

		$prg_view .= "</div>
            <script>
                (function ($) {
                    $(document).ready(function(){
                        $ays_prg_container_display_none_js
                        $(document).find('.prg_loader_$id').css({'display': 'flex', 'animation-name': 'fadeIn'});

                        $(document).find('.ays_portfolio_container_$id').imagesLoaded().done( function( instance ) {
                            $(document).find('.prg_loader_$id').css({'display': 'none', 'animation-name': 'fadeOut'});
                            $('.ays_portfolio_container_$id').fadeIn();
                            let wall = new Freewall('#ays_freewal_$id');
			                wall.reset({
			                	selector: '.prg-cell',
			                	animate: true,
			                	cellW: 25,
                                cellH: 250,
                                gutterX: 7.5,
                                gutterY: 7.5,
			                	onResize: function() {
                                    wall.fitWidth();
			                	}
			                });
                            wall.fitWidth();
                            $(window).trigger('resize');
                        }).fail( function() {
                             let errorImage = '<div><p>Images doesn\'t loaded please reload page</p></div>';
                            $ays_prg_container_error_message_js
                            $ays_prg_container_display_block_js
                            $(document).find('.prg_loader_$id').css({'display': 'none', 'animation-name': 'fadeOut'});
                        })
                        $ays_prg_lazy_load
                        $ays_prg_lazy_load_mosaic
                    });
                })(jQuery);
            </script>";
		echo $prg_view;
	}

	public function ays_portfolio_generate_shortcode( $attr ) {
		ob_start();

		$this->ays_generate_portfolio($attr);

		return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());
	}

	public function ays_portfolio_load_project() {
		$project_id        = intval($_POST['project_id']);
		$project           = $this->get_project_by_id($project_id);
		$portfolio         = $this->get_portfolio_by_id($project['portfolio_id']);
		$portfolio_options = json_decode($portfolio['options'], true);
		$project_options   = json_decode($project['options'], true);

		$project_images     = !empty($project['images']) ? explode('***', $project['images']) : array();
		$project_main_image = $project['main_image'];

		$project_images_thumbs = array();
		foreach ( $project_images as $img ) {
			$project_images_thumbs[] = $this->ays_get_image_size($img, 'thumbnail');
		}
		$project_main_image_thumb = $this->ays_get_image_size($project_main_image, 'thumbnail');

		$project_url        = $project['project_url'];
		$project_name       = stripslashes($project['name']);
		$project_desc       = stripslashes($project['description']);
		$images_fit_class   = $portfolio_options['images_fit'] == 'cover' ? "ays-prg-project-img-cover" : "ays-prg-project-img-contain";
		$project_attributes = json_decode($project['proj_attributes'], true);
		?>
        <div class='ays_project' id="aysPrgProject<?= $project_id ?>">
            <div class='ays_project_images'>
                <div class='ays_project_images_item'>
                    <div class='ays_project_images_clearfix'>
                        <div id='ays_project_images_ul' data-count="<?= count($project_images) + 1; ?>"
                             class='ays_project_gallery owl-carousel owl-theme <?= $images_fit_class; ?>'>
                            <img src="<?= $project_main_image; ?>"/>
							<?php foreach ( $project_images as $index => $image ): ?>
                                <img src="<?= $image; ?>"/>
							<?php endforeach; ?>
                        </div>
                        <div class="carousel-custom-dots" style="width:<?= (count($project_images) + 1) * 100; ?>px">
                            <div id="carousel-custom-dots" class="owl-carousel owl-theme">
                                <div class='item'>
                                    <img src='<?= $project_main_image_thumb; ?>'>
                                </div>
								<?php foreach ( $project_images_thumbs as $thumb ): ?>
                                    <div class='item'>
                                        <img src='<?= $thumb; ?>'>
                                    </div>
								<?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class='ays_project_info'>
                <div class="prg-card">
                    <div class="prg-modal-control">
                        <svg id="prg-modal-ellipsis" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"/>
                            </g>
                            <g>
                                <path fill="#e63946" id="svg_1"
                                      d="m10,12a2,2 0 1 1 0,-4a2,2 0 0 1 0,4zm0,-6a2,2 0 1 1 0,-4a2,2 0 0 1 0,4zm0,12a2,        2 0 1 1 0,-4a2,2 0 0 1 0,4z"/>
                            </g>
                        </svg>
                        <svg id="prg-modal-close" baseProfile="tiny" height="20px" version="1.2"
                             viewBox="5 5 14 14" width="20px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink">
                            <path d="M17.414,6.586c-0.78-0.781-2.048-0.781-2.828,0L12,9.172L9.414,      6.586c-0.78-0.781-2.048-0.781-2.828,0  c-0.781,0.781-0.781,2.047,0,2.828L9.171,12l-2.585,   2.586c-0.781,0.781-0.781,2.047,0,2.828C6.976,17.805,7.488,18,8,18  s1.024-0.195,  1.414-0.586L12,14.828l2.586,2.586C14.976,17.805,15.488,18,16,18s1.024-0.195,1.414-0.586          c0.781-0.781,0.781-2.047,0-2.828L14.829,12l2.585-2.586C18.195,8.633,18.195,7.367,17.414,        6.586z"/>
                        </svg>
                    </div>
                    <div class="prg-card-body">
                        <h6 class="prg-card-title"><?= __("Name:", $this->plugin_name) . " " . $project_name; ?></h6>
                        <p class="prg-card-text">
                            <b><?= __("Description:", $this->plugin_name) ?></b> <?= $project_desc; ?>
                        </p>
						<?php if (!empty($project_url)): ?>
                            <a href="<?= $project_url; ?>" target='<?= isset($project_options['url_open']) && $project_options['url_open'] == 'on' ? "_blank" : "top"; ?>'
                               title="<?=__("Project page", $this->plugin_name)?>"
                               class="prg-card-link prg-btn"><?= __("See more", $this->plugin_name) ?></a>
						<?php endif; ?>
                    </div>
                    <ul class="prg-list-attr">
						<?php if (!empty($project_attributes)):
							foreach ( $project_attributes as $key => $value ):
								if (empty($value)) {
									continue;
								} ?>
                                <li class='prg-list-item'><b><?= $key ?>:</b> <?= $value ?></li>
							<?php endforeach;
						endif; ?>
                    </ul>
                </div>

            </div>
        </div>
		<?php
		wp_die();
	}

	public function get_portfolio_by_id( $id ) {
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}ays_portfolio WHERE id=" . absint(intval($id));

		$result = $wpdb->get_row($sql, "ARRAY_A");

		return $result;
	}

	public function get_portfolio_attributes_for_projects() {
		global $wpdb;
		$sql     = "SELECT * FROM {$wpdb->prefix}ays_portfolio_attributes WHERE `published`='1'";
		$results = $wpdb->get_results($sql, 'ARRAY_A');

		return $results;
	}

	public function get_projects_by_portfolio_id( $id ) {
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}ays_portfolio_items WHERE portfolio_id = '" . absint(intval($id)) . "'";

		$result = $wpdb->get_results($sql, "ARRAY_A");

		return $result;
	}

	public function get_project_by_id( $id ) {
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}ays_portfolio_items WHERE id = '" . absint(intval($id)) . "'";

		$result = $wpdb->get_row($sql, "ARRAY_A");

		return $result;
	}

	private function array_split( $array, $pieces ) {
		if ($pieces < 2) {
			return array($array);
		}

		$newCount = ceil(count($array) / $pieces);
		$a        = array_slice($array, 0, $newCount);
		$b        = $this->array_split(array_slice($array, $newCount), $pieces - 1);

		return array_merge(array($a), $b);
	}

	private function hex2rgba( $color, $opacity = false ) {

		$default = 'rgba(39, 174, 96, 0.5)';
		/**
		 * Return default if no color provided
		 */
		if (empty($color)) {
			return $default;
		}
		/**
		 * Sanitize $color if "#" is provided
		 */
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		/**
		 * Check if color has 6 or 3 characters and get values
		 */
		if (strlen($color) == 6) {
			$hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
		} elseif (strlen($color) == 3) {
			$hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
		} else {
			return $default;
		}

		/**
		 * [$rgb description]
		 * @var array
		 */
		$rgb = array_map('hexdec', $hex);
		/**
		 * Check if opacity is set(rgba or rgb)
		 */
		if ($opacity) {
			if (abs($opacity) > 1) {
				$opacity = 1.0;
			}

			$output = 'rgba( ' . implode(",", $rgb) . ',' . $opacity . ' )';
		} else {
			$output = 'rgb( ' . implode(",", $rgb) . ' )';
		}

		/**
		 * Return rgb(a) color string
		 */
		return $output;
	}

}
