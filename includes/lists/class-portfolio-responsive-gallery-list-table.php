<?php
ob_start();

class Portfolio_Responsive_Gallery_List_Table extends WP_List_Table {
	private $plugin_name;

	/** Class constructor */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
		parent::__construct(array(
			"singular" => __("Portfolio", $this->plugin_name), //singular name of the listed records
			"plural"   => __("Portfolios", $this->plugin_name), //plural name of the listed records
			"ajax"     => false, //does this table support ajax?
		));
		add_action("admin_notices", array($this, "portfolio_notices"));

	}

	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_portfolios( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}ays_portfolio";

		if (!empty($_REQUEST["orderby"])) {
			$sql .= " ORDER BY " . esc_sql($_REQUEST["orderby"]);
			$sql .= !empty($_REQUEST["order"]) ? " " . esc_sql($_REQUEST["order"]) : " ASC";
		}

		$sql .= " LIMIT $per_page";
		$sql .= " OFFSET " . ($page_number - 1) * $per_page;

		$result = $wpdb->get_results($sql, "ARRAY_A");

		return $result;
	}

	public function get_portfolio_by_id( $id ) {
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}ays_portfolio WHERE id=" . absint(intval($id));

		$result = $wpdb->get_row($sql, "ARRAY_A");

		return $result;
	}

	public function get_projects_by_portfolio_id( $id ) {
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}ays_portfolio_items WHERE portfolio_id = '" . absint(intval($id)) . "'";

		$result = $wpdb->get_results($sql, "ARRAY_A");

		return $result;
	}

	public function add_or_edit_portfolio( $data, $portfolio_id = null, $submit_type = "" ) {
		global $wpdb;
		$portfolio_table       = $wpdb->prefix . "ays_portfolio";
		$portfolio_items_table = $wpdb->prefix . "ays_portfolio_items";
		$project_attributes    = self::get_portfolio_attributes_for_projects();

		if (isset($data["ays_portfolio_action"]) && wp_verify_nonce($data["ays_portfolio_action"], "ays_portfolio_action")) {
//			 echo "<pre>";
//			 print_r($data);
//			 wp_die();

			$portfolio_name        = wp_unslash(sanitize_text_field($data["portfolio_name"]));
			$portfolio_description = wpautop($data["portfolio_description"]);
			$portfolio_width       = isset($data['ays-prg-width']) && $data['ays-prg-width'] != 0 ? absint($data['ays-prg-width']) : 0;

			$prg_view_type      = sanitize_text_field($data['ays-view-type']);
			$columns_count      = absint(intval($data['ays-columns-count']));
			$image_sizes        = sanitize_text_field($data['ays_image_sizes']);
			$show_title         = isset($data['ays_project_show_title']) ? $data['ays_project_show_title'] : '';
			$show_title_on      = wp_unslash(sanitize_text_field($data['ays_project_show_title_on']));
			$ays_images_loading = wp_unslash(sanitize_text_field($data['ays_images_loading']));
			$images_fit         = sanitize_text_field($data['ays-lightbox-images-fit']);

			$ays_show_prg_title = wp_unslash(sanitize_text_field($data['ays_prg_title_show']));
			$ays_show_prg_desc  = wp_unslash(sanitize_text_field($data['ays_prg_desc_show']));

			$ays_project_action = $data["ays_project_action"];
			$ays_update_action  = array();
			$ays_insert_action  = array();

			foreach ( $ays_project_action as $key => $action ) {
				if ($action == 'update') {
					$ays_update_action[] = sanitize_text_field($key);
				} elseif ($action == 'insert') {
					$ays_insert_action[] = sanitize_text_field($key);
				}
			}

			$project_images   = ($data["ays_project_images"]);
			$main_images      = ($data["ays_project_main_img"]);
			$project_name     = ($data["ays_project_name"]);
			$project_desc     = ($data["ays_project_description"]);
			$project_urls     = ($data["ays_project_url"]);
			$project_url_open = ($data['ays_project_url_open']);
			$accordion_number = absint(intval($data['ays_accordion_number']));

			$portfolio_options = array(
				'accordion_number'      => $accordion_number,
				"prg_width"             => $portfolio_width,
				'columns_count'         => $columns_count,
				'prg_view_type'         => $prg_view_type,
				'show_prg_title'        => $ays_show_prg_title,
				'show_prg_desc'         => $ays_show_prg_desc,
				"image_sizes"           => $image_sizes,
				"show_project_title"    => $show_title,
				"show_project_title_on" => $show_title_on,
				"prg_images_loading"    => $ays_images_loading,
				"images_fit"            => $images_fit,
			);

			if ($portfolio_id == null) {
				$portfolio_result = $wpdb->insert(
					$portfolio_table,
					array(
						"name"        => $portfolio_name,
						"description" => $portfolio_description,
						"options"     => json_encode($portfolio_options),
					),
					array("%s", "%s", "%s")
				);
				$message          = "created";
			} else {
				$portfolio_result = $wpdb->update(
					$portfolio_table,
					array(
						"name"        => $portfolio_name,
						"description" => $portfolio_description,
						"options"     => json_encode($portfolio_options),
					),
					array("id" => $portfolio_id),
					array("%s", "%s", "%s"),
					array("%d")
				);
				$message          = "updated";
			}

			if ($data["ays_project_update_id"] != '') {
				$project_update_id = explode('***', $data["ays_project_update_id"]);
			} else {
				$project_update_id = array();
			}

			if ($data["ays_project_delete_id"] != '') {
				$project_delete_id = explode('***', trim($data["ays_project_delete_id"], '***'));
			} else {
				$project_delete_id = array();
			}

			if (!empty($ays_insert_action)) {
				foreach ( $ays_insert_action as $key ) {

					if ($portfolio_id == null) {
						$portfolio_id = $wpdb->insert_id;
					}
					if ($project_url_open[$key] == null) {
						$url_open = "";
					} else {
						$url_open = $project_url_open[$key];
					}
					$portfolio_items_options = array(
						'url_open' => $url_open,
					);

					$project_attributes_values = array();
					foreach ( $project_attributes as $hamar => $attribute ) {
						if (array_key_exists($attribute['slug'], $data)) {
							$project_attributes_values[$attribute['slug']] = sanitize_text_field($data[$attribute['slug']][$key]);
						}
					}

					$project_images_str     = !empty($project_images[$key]) ? sanitize_text_field(implode('***', $project_images[$key])) : '';
					$portfolio_items_result = $wpdb->insert(
						$portfolio_items_table,
						array(
							"portfolio_id" => $portfolio_id,
							"name"         => isset($project_name[$key]) && !empty($project_name[$key]) ? sanitize_text_field($project_name[$key]) : "Nameless project",
							"description"  => stripslashes($project_desc[$key]),
							"project_url"  => (isset($project_urls[$key])) ? wp_http_validate_url($project_urls[$key]) : '',
							"main_image"   => isset($main_images[$key]) && !empty($main_images[$key]) ? $main_images[$key] : '',
							"images"       => $project_images_str,
							"options"      => json_encode($portfolio_items_options),
							"attributes"   => json_encode($project_attributes_values),
						),
						array("%d", "%s", "%s", "%s", "%s", "%s", "%s", "%s")
					);

				}
			}

			if (!empty($ays_update_action)) {
				foreach ( $ays_update_action as $key ) {

					$project_images_str = sanitize_text_field(implode('***', $project_images[$key]));
					if ($project_url_open[$key] == null) {
						$url_open = "";
					} else {
						$url_open = $project_url_open[$key];
					}
					$portfolio_items_options = array(
						'url_open' => $url_open,
					);

					$project_attributes_values = array();
					foreach ( $project_attributes as $hamar => $attribute ) {
						if (array_key_exists($attribute['slug'], $data)) {
							$project_attributes_values[$attribute['slug']] = sanitize_text_field($data[$attribute['slug']][$key]);
						}
					}
					$portfolio_items_result = $wpdb->update(
						$portfolio_items_table,
						array(
							"portfolio_id" => $portfolio_id,
							"name"         => $project_name[$key],
							"description"  => stripslashes($project_desc[$key]),
							"project_url"  => (isset($project_urls[$key])) ? wp_http_validate_url($project_urls[$key]) : '',
							"main_image"   => isset($main_images[$key]) && !empty($main_images[$key]) ? $main_images[$key] : '',
							"images"       => (!empty($project_images_str)) ? $project_images_str : '',
							"options"      => json_encode($portfolio_items_options),
							"attributes"   => json_encode($project_attributes_values),
						),
						array("id" => $project_update_id[$key]),
						array("%d", "%s", "%s", "%s", "%s", "%s", "%s", "%s"),
						array("%d")
					);
				}
			}

			if (!empty($project_delete_id) || $project_delete_id !== null) {
				foreach ( $project_delete_id as $key => $id ) {
					$portfolio_items_result = $wpdb->delete(
						$portfolio_items_table,
						["id" => $id],
						["%d"]
					);
				}
			}

			if ($portfolio_result >= 0) {
				if ($submit_type == '') {
					$url = esc_url_raw(remove_query_arg([
							"action",
							"portfolio"
						])) . "&status=" . $message . "&type=success";
					wp_redirect($url);
					exit();
				} else {
					$url = esc_url_raw(add_query_arg()) . "&status=" . $message . "&type=success";
					wp_redirect($url);
					exit();
				}
			}
		}
	}

	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_portfolios( $id ) {
		global $wpdb;
		$wpdb->delete(
			"{$wpdb->prefix}ays_portfolio",
			["id" => $id],
			["%d"]
		);
		$wpdb->delete(
			"{$wpdb->prefix}ays_portfolio_items",
			["portfolio_id" => $id],
			["%d"]
		);

	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ays_portfolio";

		return $wpdb->get_var($sql);
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
		_e("There are no portfolios yet.", $this->plugin_name);
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case "name":
			case "description":
				return wp_unslash($item[$column_name]);
				break;
			case "shortcode":
			case "id":
				return $item[$column_name];
				break;
			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			"<input type='checkbox' name='bulk-delete[]' value='%s' />", $item["id"]
		);
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {
		$delete_nonce = wp_create_nonce($this->plugin_name . "-delete-portfolio");

		$name = sprintf("<a href='?page=%s&action=%s&portfolio=%d'><b>" . $item["name"] . "</b></a>", esc_attr($_REQUEST["page"]), "edit", absint($item["id"]));

		$actions = array(
			"edit"   => sprintf("<a href='?page=%s&action=%s&portfolio=%d'>" . __('Edit', $this->plugin_name) . "</a>", esc_attr($_REQUEST["page"]), "edit", absint($item["id"])),
			"delete" => sprintf("<a href='?page=%s&action=%s&portfolio=%s&_wpnonce=%s'>" . __('Delete', $this->plugin_name) . "</a>", esc_attr($_REQUEST["page"]), "delete", absint($item["id"]), $delete_nonce),
		);

		return $name . $this->row_actions($actions);
	}

	function column_shortcode( $item ) {
		return sprintf("<input type='text' onClick='this.setSelectionRange(0, this.value.length)' readonly value='[portfolio_responsive_gallery id=%s]' style='width: 300px;' />", $item["id"]);
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			"cb"          => "<input type='checkbox' />",
			"name"        => __("Name", $this->plugin_name),
			"description" => __("Description", $this->plugin_name),
			"shortcode"   => __("Shortcode", $this->plugin_name),
			"id"          => __("ID", $this->plugin_name),
		);

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			"name" => array("name", true),
			"id"   => array("id", true),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			"bulk-delete" => __("Delete", $this->plugin_name),
		);

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page("portfolios_per_page", 5);
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args([
			"total_items" => $total_items, //WE have to calculate the total number of items
			"per_page"    => $per_page, //WE have to determine how many items to show on a page
		]);

		$this->items = self::get_portfolios($per_page, $current_page);
	}

	public function process_bulk_action() {
		//Detect when a bulk action is being triggered...
		$message = "deleted";
		if ("delete" === $this->current_action()) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr($_REQUEST["_wpnonce"]);

			if (!wp_verify_nonce($nonce, $this->plugin_name . "-delete-portfolio")) {
				die("Go get a life script kiddies");
			} else {
				self::delete_portfolios(absint($_GET["portfolio"]));

				// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
				// add_query_arg() return the current url

				$url = esc_url_raw(remove_query_arg([
						"action",
						"portfolio",
						"_wpnonce"
					])) . "&status=" . $message . "&type=success";
				wp_redirect($url);
				exit();
			}

		}

		// If the delete bulk action is triggered
		if ((isset($_POST["action"]) && $_POST["action"] == "bulk-delete")
		    || (isset($_POST["action2"]) && $_POST["action2"] == "bulk-delete")
		) {

			$delete_ids = esc_sql($_POST["bulk-delete"]);

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_portfolios($id);

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
			// add_query_arg() return the current url

			$url = esc_url_raw(remove_query_arg([
					"action",
					"portfolio",
					"_wpnonce"
				])) . "&status=" . $message . "&type=success";
			wp_redirect($url);
			exit();
		}
	}

	public function get_portfolio_attributes_for_projects() {
		global $wpdb;
		$sql     = "SELECT * FROM {$wpdb->prefix}ays_portfolio_attributes WHERE 'published'=1";
		$results = $wpdb->get_results($sql, 'ARRAY_A');

		return $results;
	}

	public function portfolio_notices() {
		$status = (isset($_REQUEST["status"])) ? sanitize_text_field($_REQUEST["status"]) : "";
		$type   = (isset($_REQUEST["type"])) ? sanitize_text_field($_REQUEST["type"]) : "";

		if (empty($status)) {
			return;
		}

		if ("created" == $status) {
			$updated_message = esc_html(__("Portfolio created.", $this->plugin_name));
		} elseif ("updated" == $status) {
			$updated_message = esc_html(__("Portfolio saved.", $this->plugin_name));
		} elseif ("deleted" == $status) {
			$updated_message = esc_html(__("Portfolio deleted.", $this->plugin_name));
		} elseif ("error" == $status) {
			$updated_message = __("You're not allowed to add portfolio for more galleries please checkout to <a href='http://ays-pro.com/index.php/wordpress/portfolio-responsive-gallery' target='_blank'>PRO version</a>.", $this->plugin_name);
		}

		if (empty($updated_message)) {
			return;
		}

		?>
        <div class="notice notice-<?php echo $type; ?> is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
		<?php
	}
}
