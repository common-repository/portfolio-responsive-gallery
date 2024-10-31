<?php
$action              = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';
$heading             = '';
$id                  = (isset($_GET['portfolio_attribute'])) ? absint(intval($_GET['portfolio_attribute'])) : null;
$portfolio_attribute = [
	'id'        => $this->portfolio_attributes_obj->get_new_attr_id(),
	'name'      => '',
	'slug'      => '',
	'type'      => '',
	'published' => ''
];

switch ( $action ) {
	case 'add':
		$heading = __('Add new attribute', $this->plugin_name);
		break;
	case 'edit':
		$heading             = __('Edit attribute', $this->plugin_name);
		$portfolio_attribute = $this->portfolio_attributes_obj->get_portfolio_attribute_by_id($id);
		break;
}
if (isset($_POST['ays_submit'])) {
	$result = $this->portfolio_attributes_obj->add_or_edit_portfolio_attribute($_POST, $id);
}
if (isset($_POST['ays_apply'])) {
	$this->portfolio_attributes_obj->add_or_edit_portfolio_attribute($_POST, $id, "apply");
}

?>

<div class="wrap">
    <div class="container-fluid">
        <h1><?= $heading; ?></h1>
        <hr/>
        <form class="ays-portfolio-attribute-form" id="ays-portfolio-attribute-form" method="post">

            <div class="form-group row">
                <div class="col-sm-2">
                    <label for='ays-attribute-name'><?= __('Name', $this->plugin_name); ?></label>
                </div>
                <div class="col-sm-10">
                    <input class='ays-text-input' id='ays-attribute-name' name='ays_prg_attr_name' required type='text'
                           value='<?= (isset($portfolio_attribute['name'])) ? stripslashes(htmlentities($portfolio_attribute['name'])) : ''; ?>'>
                </div>
            </div>

            <hr/>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label><?= __('Slug', $this->plugin_name); ?></label>
                </div>
                <div class="col-sm-10">
                    <input class='ays-text-input' id='ays-attribute-slug' name='ays_prg_attr_slug' required readonly
                           type='text' value="prg_attr_<?= $portfolio_attribute['id']; ?>">
                </div>
            </div>

            <hr/>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label><?= __('Type', $this->plugin_name); ?></label>
                </div>
                <div class="col-sm-10">
                    <select class='ays-text-input ays-text-input-short' id='ays-attribute-type'
                            name='ays_prg_attr_type'>
                        <option value="text" <?= (isset($portfolio_attribute['type']) && $portfolio_attribute['type'] == 'text') ? 'selected' : ''; ?>><?= __('Text', $this->plugin_name); ?></option>
                        <option value="textarea" <?= (isset($portfolio_attribute['type']) && $portfolio_attribute['type'] == 'textarea') ? 'selected' : ''; ?>><?= __('Textarea', $this->plugin_name); ?></option>
                        <option value="email" <?= (isset($portfolio_attribute['type']) && $portfolio_attribute['type'] == 'email') ? 'selected' : ''; ?>><?= __('E-Mail', $this->plugin_name); ?></option>
                        <option value="number" <?= (isset($portfolio_attribute['type']) && $portfolio_attribute['type'] == 'number') ? 'selected' : ''; ?>><?= __('Number', $this->plugin_name); ?></option>
                        <option value="tel" <?= (isset($portfolio_attribute['type']) && $portfolio_attribute['type'] == 'tel') ? 'selected' : ''; ?>><?= __('Telephone', $this->plugin_name); ?></option>
                        <option value="url" <?= (isset($portfolio_attribute['type']) && $portfolio_attribute['type'] == 'url') ? 'selected' : ''; ?>><?= __('URL', $this->plugin_name); ?></option>
                        <option value="date" <?= (isset($portfolio_attribute['type']) && $portfolio_attribute['type'] == 'date') ? 'selected' : ''; ?>><?= __('Date', $this->plugin_name); ?></option>
                    </select>
                </div>
            </div>

            <hr/>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label><?= __('Attribute status', $this->plugin_name); ?></label>
                </div>

                <div class="col-sm-3">
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-publish" name="ays_prg_attr_publish"
                               value="1" <?= ($portfolio_attribute["published"] == '') ? "checked" : ""; ?> <?= ($portfolio_attribute['published'] == '1') ? 'checked' : ''; ?> />
                        <label class="form-check-label"
                               for="ays-publish"> <?= __('Published', $this->plugin_name); ?> </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-unpublish" name="ays_prg_attr_publish"
                               value="0" <?= ($portfolio_attribute['published'] == '0') ? 'checked' : ''; ?> />
                        <label class="form-check-label"
                               for="ays-unpublish"> <?= __('Unpublished', $this->plugin_name); ?> </label>
                    </div>
                </div>
            </div>

            <hr/>
			<?php
			wp_nonce_field('portfolio_attribute_action', 'portfolio_attribute_action');
			$other_attributes = array('id' => 'ays-button');
			submit_button(__('Save attribute', $this->plugin_name), 'primary', 'ays_submit', true, $other_attributes);
			if ($id != null) {
				submit_button(__('Apply attribute', $this->plugin_name), '', 'ays_apply', true, $other_attributes);
			}
			?>
        </form>
    </div>
</div>