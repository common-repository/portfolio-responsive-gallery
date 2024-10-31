<?php
ob_start();

class Portfolio_Responsive_Gallery_Attributes_List_Table extends WP_List_Table {
	private $plugin_name;

	/** Class constructor */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
		parent::__construct([
			'singular' => __('Attribute', $this->plugin_name), //singular name of the listed records
			'plural'   => __('Attributes', $this->plugin_name), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		]);
		add_action('admin_notices', array($this, 'porfolio_attribute_notices'));
	}

	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_portfolio_attributes( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}ays_portfolio_attributes";

		if (!empty($_REQUEST['orderby'])) {
			$sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
			$sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' DESC';
		} else {
			$sql .= ' ORDER BY id DESC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ($page_number - 1) * $per_page;


		$result = $wpdb->get_results($sql, 'ARRAY_A');

		return $result;
	}

	public function get_new_attr_id() {
		global $wpdb;
		$portfolio_attribute_table = $wpdb->prefix . 'ays_portfolio_attributes';
		$sql                       = "SELECT id FROM $portfolio_attribute_table ORDER BY id DESC LIMIT 1";

		return 1 + $wpdb->get_var($sql);
	}

	public function add_or_edit_portfolio_attribute( $data , $id=null, $ays_change_type="") {
		global $wpdb;
		$portfolio_attribute_table = $wpdb->prefix . 'ays_portfolio_attributes';

		if (isset($data["portfolio_attribute_action"]) && wp_verify_nonce($data["portfolio_attribute_action"], 'portfolio_attribute_action')) {
			$name    = stripslashes(sanitize_text_field($data['ays_prg_attr_name']));
			$slug    = stripslashes($data['ays_prg_attr_slug']);
			$type    = stripslashes($data['ays_prg_attr_type']);
			$publish = absint($data['ays_prg_attr_publish']);
			if (!$id) {
				$result  = $wpdb->insert(
					$portfolio_attribute_table,
					array(
						'name'      => $name,
						'slug'      => $slug,
						'type'      => $type,
						'published' => $publish
					),
					array('%s', '%s', '%s', '%d')
				);
				$message = 'created';
			} else {
				$result  = $wpdb->update(
					$portfolio_attribute_table,
					array(
						'name'      => $name,
						'slug'      => $slug,
						'type'      => $type,
						'published' => $publish
					),
					array('id' => $id),
					array('%s', '%s', '%s', '%d'),
					array('%d')
				);
				$message = 'updated';
			}

			if ($result >= 0) {
				if ($ays_change_type != '') {
					$url = esc_url_raw(add_query_arg()) . '&status=' . $message;
					wp_redirect($url);
				} else {
					$url = esc_url_raw(remove_query_arg(['action', 'portfolio_attribute'])) . '&status=' . $message;
					wp_redirect($url);
				}
			}
		}
	}

	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_portfolio_attribute( $id ) {
		global $wpdb;
		$wpdb->delete(
			"{$wpdb->prefix}ays_portfolio_attributes",
			['id' => $id],
			['%d']
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ays_portfolio_attributes";

		return $wpdb->get_var($sql);
	}


	/** Text displayed when no customer data is available */
	public function no_items() {
		_e('There are no portfolio attributes yet.', $this->plugin_name);
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
			case 'name':
			case 'slug':
			case 'type':
			case 'published':
			case 'id':
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
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	function column_slug( $item ) {
		return sprintf('<input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%s" />', $item["slug"]);
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {
		$delete_nonce = wp_create_nonce($this->plugin_name . '-delete-portfolio-attribute');

		$title = sprintf('<a href="?page=%s&action=%s&portfolio_attribute=%d"><strong>' . $item['name'] . '</strong></a>', esc_attr($_REQUEST['page']), 'edit', absint($item['id']));

		$actions = [
			'edit'   => sprintf('<a href="?page=%s&action=%s&portfolio_attribute=%d">' . __('Edit', $this->plugin_name) . '</a>', esc_attr($_REQUEST['page']), 'edit', absint($item['id'])),
			'delete' => sprintf('<a href="?page=%s&action=%s&portfolio_attribute=%s&_wpnonce=%s">' . __('Delete', $this->plugin_name) . '</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce)
		];

		return $title . $this->row_actions($actions);
	}


	function column_published( $item ) {
		switch ( $item['published'] ) {
			case "1":
				return '<span class="ays-publish-status"><i class="far fa-check-square" aria-hidden="true"></i> ' . __('Published', $this->plugin_name) . '</span>';
				break;
			case "0":
				return '<span class="ays-publish-status"><i class="far fa-square" aria-hidden="true"></i> ' . __('Unublished', $this->plugin_name) . '</span>';
				break;
		}
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'        => '<input type="checkbox" />',
			'name'      => __('Name', $this->plugin_name),
			'slug'      => __('Slug', $this->plugin_name),
			'type'      => __('Type', $this->plugin_name),
			'published' => __('Status', $this->plugin_name),
			'id'        => __('ID', $this->plugin_name),
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name'      => array('title', true),
			'type'      => array('type', true),
			'published' => array('published', true),
			'id'        => array('id', true),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => __('Delete', $this->plugin_name)
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page('portfolio_attributes_per_page', 5);
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args([
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		]);

		$this->items = self::get_portfolio_attributes($per_page, $current_page);
	}


	public function get_portfolio_attribute_by_id( $attribut_id ) {
		global $wpdb;
		$sql    = "SELECT * FROM {$wpdb->prefix}ays_portfolio_attributes where 'id'=" . absint($attribut_id);
		$result = $wpdb->get_row($sql, 'ARRAY_A');

		return $result;
	}


	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ('delete' === $this->current_action()) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr($_REQUEST['_wpnonce']);

			if (!wp_verify_nonce($nonce, $this->plugin_name . '-delete-portfolio-attribute')) {
				die('Go get a life script kiddies');
			} else {
				self::delete_portfolio_attribute(absint($_GET['portfolio_attribute']));

				// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
				// add_query_arg() return the current url

				$url = esc_url_raw(remove_query_arg(['action', 'portfolio_attribute', '_wpnonce'])) . '&status=deleted';
				wp_redirect($url);
			}

		}

		// If the delete bulk action is triggered
		if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete') || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')) {

			$delete_ids = esc_sql($_POST['bulk-delete']);

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_portfolio_attribute($id);

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
			// add_query_arg() return the current url
			$url = esc_url_raw(remove_query_arg(['action', 'portfolio_attribute', '_wpnonce'])) . '&status=deleted';
			wp_redirect($url);
		}
	}


	public function porfolio_attribute_notices() {
		$status = (isset($_REQUEST['status'])) ? sanitize_text_field($_REQUEST['status']) : '';

		if (empty($status)) {
			return;
		}

		if ('created' == $status) {
			$updated_message = esc_html(__('Portfolio attribute created.', $this->plugin_name));
		} elseif ('updated' == $status) {
			$updated_message = esc_html(__('Portfolio attribute saved.', $this->plugin_name));
		} elseif ('deleted' == $status) {
			$updated_message = esc_html(__('Portfolio attribute deleted.', $this->plugin_name));
		}

		if (empty($updated_message)) {
			return;
		}

		?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
		<?php
	}
}