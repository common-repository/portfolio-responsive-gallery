<?php

$action  = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';
$heading = '';
$id      = (isset($_GET['portfolio'])) ? absint(intval($_GET['portfolio'])) : null;

$prg_options = array(
	'columns_count'         => '3',
	'accordion_number'      => 1,
	'prg_view_type'         => 'grid',
	"show_prg_title"        => "on",
	"show_prg_desc"         => "on",
	"image_sizes"           => "full_size",
	"show_project_title"    => "on",
	"show_project_title_on" => "image_hover",
	"prg_images_loading"    => "all_loaded",
	"images_fit"            => "contain",
);
$portfolio   = array(
	"id"          => "",
	"name"        => __("Demo name", $this->plugin_name),
	"description" => __("Demo description", $this->plugin_name),
	"options"     => json_encode($prg_options, true),
);

switch ( $action ) {
	case 'add':
		$heading              = __('Add new portfolio', $this->plugin_name);
		$portfolio_attributes = $this->portfolio_obj->get_portfolio_attributes_for_projects();
		break;
	case 'edit':
		$heading              = __('Edit portfolio', $this->plugin_name);
		$portfolio            = $this->portfolio_obj->get_portfolio_by_id($id);
		$portfolio_items      = $this->portfolio_obj->get_projects_by_portfolio_id($id);
		$portfolio_attributes = $this->portfolio_obj->get_portfolio_attributes_for_projects();
		break;
    default:
        break;
}

if (isset($_POST["ays-submit"]) || isset($_POST["ays-submit-top"])) {
	$this->portfolio_obj->add_or_edit_portfolio($_POST, $id);
}
if (isset($_POST["ays-apply"]) || isset($_POST["ays-apply-top"])) {
	$this->portfolio_obj->add_or_edit_portfolio($_POST, $id, "apply");
}

$prg_options = json_decode($portfolio['options'], true);

$projects_id = array();
if ($id != null) {
	if (isset($portfolio_items)) {
		foreach ( $portfolio_items as $key => $project ) {
			$projects_id[] = $project['id'];
		}
	}
}

$projects_id = implode('***', $projects_id);

if ($prg_options['accordion_number'] == null || !isset($prg_options['accordion_number'])) {
	$accordion_number = 1;
} else {
	$accordion_number = $prg_options['accordion_number'];
}

$image_sizes = $this->ays_get_all_image_sizes();

?>

<div class="wrap">
    <div class="container-fluid">
        <form id="ays-portfolio-form" method="post">
            <h1 class="wp-heading-inline">
				<?= $heading; ?>
                <input type="submit" name="ays-submit-top" class="ays-submit action-button button-primary"
                       value="<?= __("Save changes", $this->plugin_name); ?>"/>
				<?php if ($id != null): ?>
                    <input type="submit" name="ays-apply-top" class="ays-submit action-button button"
                           value="<?= __("Apply changes", $this->plugin_name); ?>"/>
				<?php endif; ?>
            </h1>
            <div class="nav-tab-wrapper">
                <a href="#tab1" class="nav-tab nav-tab-active"><?= __("Projects", $this->plugin_name); ?></a>
                <a href="#tab2" class="nav-tab"><?= __("Portfolio Settings", $this->plugin_name); ?></a>
                <a href="#tab3" class="nav-tab"><?= __("Project Settings", $this->plugin_name); ?></a>
            </div>
            <div id="tab1" class="ays-portfolio-tab-content ays-portfolio-tab-content-active">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="portfolio_name"><?= __("Portfolio Name", $this->plugin_name); ?></label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" required name="portfolio_name" id="portfolio_name" class="ays-text-input"
                               placeholder="<?= __("Portfolio Name", $this->plugin_name); ?>"
                               value="<?= wp_unslash($portfolio["name"]); ?>"/>
                    </div>
                </div>
                <hr/>
                <div class="ays-field">
                    <label for="portfolio_description"><?= __("Portfolio Description", $this->plugin_name); ?></label>
					<?php

					$portfolio_desc           = stripslashes(($portfolio['description']));
					$portfolio_desc_editor_id = 'portfolio_description';
					$portfolio_desc_settings  = array(
						'editor_height' => '150',
						'textarea_name' => 'portfolio_description',
						'editor_class'  => 'ays-textarea',
						'media_buttons' => false
					);
					wp_editor($portfolio_desc, $portfolio_desc_editor_id, $portfolio_desc_settings);
					?>
                </div>
                <hr/>
                <p class="ays-subtitle"><?= __('Add Project', $this->plugin_name); ?></p>
                <h6><?= __('Create your portfolio projects', $this->plugin_name); ?></h6>
                <button class="ays-add-project button"><?= __("Add project +", $this->plugin_name); ?></button>
                <button type="button"
                        class="ays_clear_projects button"><?= __("Delete all projects", $this->plugin_name); ?></button>
                <hr/>

                <input type="hidden" id="accordion_number" name="ays_accordion_number"
                       value="<?= $accordion_number; ?>">
                <input type="hidden" name="ays_project_add_id" id="ays_project_add_id" value='0'>
                <input type="hidden" name="ays_project_update_id" id="ays_project_update_id"
                       value="<?= $projects_id; ?>">
                <input type="hidden" name="ays_project_delete_id" id="ays_project_delete_id" value="">
                <ul class="acordion-el-for-clone">
                    <li data-hamar="0">
                        <input type="hidden" data-new="0" class="ays_project_action" name="ays_project_action[]"
                               value="insert">
                        <input type="checkbox" class="ays_li_collapse" checked>
                        <i class="fas fa-trash-alt ays-delete-project"></i>
                        <i class="fas fa-arrows-alt ays-move-images"></i>
                        <i class="fas fa-angle-down ays-animated-arrow"></i>
                        <div class="ays_project_main_img">
                            <input type="hidden" name="ays_project_main_image_path[]">
                            <img class="ays_project_main_image_path">
                        </div>
                        <div class="ays_project_name"><p><?= __("No name", $this->plugin_name); ?></p></div>
                        <div class="ays-project-fields">
                            <div class="nav-tab-wrapper">
                                <a href="#ays_project0_tab1" tabindex="0"
                                   class="nav-tab nav-tab-active"><?= __("General", $this->plugin_name); ?></a>
                                <a href="#ays_project0_tab2" tabindex="1"
                                   class="nav-tab"><?= __("Options", $this->plugin_name); ?></a>
                                <a href="#ays_project0_tab3" tabindex="2"
                                   class="nav-tab"><?= __("Attributes", $this->plugin_name); ?></a>
                            </div>
                            <div class="ays_project_fields">
                                <div class="ays_project_fields_tab">
                                    <div id="ays_project0_tab1"
                                         class="ays-project-attributes-content ays-project-attributes-content-active">
                                        <div class="ays_project_images">
                                            <div style="border-right: 1px solid #ccc; margin-right:10px; padding:10px; max-height: 120px; max-width:120px;">
                                                <input type="hidden" name="ays_project_main_img[]"
                                                       class="ays_project_main_img_hi" data-required="true">
                                                <div class="ays_project_main_image_div">
                                                    <div class="ays_project_main_image_add_icon"><i
                                                                class="fas fa-plus ays-upload-btn"></i></div>
                                                    <img class="ays_project_main_image_path">
                                                </div>
                                                <p style="font-size:12px; color:grey; font-style:italic;">
                                                    <?= __("Select project main image", $this->plugin_name); ?>
                                                </p>
                                            </div>
                                            <div class="ays_project_images_div">
                                                <div class="ays_project_add_images_div">
                                                    <div class="ays_project_images_add_icon"><i
                                                                class="fas fa-plus ays-upload-btn"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ays_project_attr">
                                            <p></p>
                                            <div class="ays_image_attr_item form-group row">
                                                <div class="col-sm-3">
                                                    <label><?= __("Project name", $this->plugin_name); ?></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input class="ays-text-input ays_project_name_input" type="text"
                                                           name="ays_project_name[]"
                                                           placeholder="<?= __("Project name", $this->plugin_name); ?>"/>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="ays_image_attr_item form-group row">
                                                <div class="col-sm-3">
                                                    <label><?= __("Project description", $this->plugin_name); ?></label>
                                                </div>
                                                <div class="col-sm-9 replacable">
													<?php
													$project_desc           = '';
													$project_desc_editor_id = 'project_desc';
													$qt                     = array(
														'id'      => $project_desc_editor_id,
														'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close',
													);
													$tinymce_buttons        = array(
														'toolbar1' => "formatselect,bold,italic,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,wp_adv",
														'toolbar2' => "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
													);
													$project_desc_settings  = array(
														'editor_height'       => '140',
														'textarea_name'       => 'ays_project_description[]',
														'editor_class'        => 'ays-textarea',
														'media_buttons'       => false,
														'wpautop'             => true,
														'teeny'               => false,
														'dfw'                 => true,
														'_content_editor_dfw' => true,
														'tinymce'             => $tinymce_buttons,
														'quicktags'           => $qt,
													);
													wp_editor($project_desc, $project_desc_editor_id, $project_desc_settings);
													?>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="ays_image_attr_item form-group row">
                                                <div class="col-sm-3">
                                                    <label><?= __("Project url", $this->plugin_name); ?></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input class="ays-text-input" type="url" name="ays_project_url[]"
                                                           placeholder="<?= __("Project url", $this->plugin_name); ?>"/>
                                                </div>
                                            </div>
                                            <input type="hidden" name="ays_project_date[]" class="ays_project_date"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="ays_project_fields_tab">
                                    <div id="ays_project0_tab2" class="ays-project-attributes-content">
                                        <div class="ays_project_options_content">
                                            <h6><?= __('Set your project options', $this->plugin_name); ?></h6>
                                            <hr/>
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label><?= __("Open URL in new tab", $this->plugin_name); ?></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <label>
                                                        <input class="ays_project_url_open_js" type="checkbox"
                                                               name="ays_project_url_open[]"/>
                                                        <input type="hidden" class="ays_project_url_open"
                                                               name="ays_project_url_open[]" value="off">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ays_project_fields_tab">
                                    <div id="ays_project0_tab3" class="ays-project-attributes-content">
                                        <div class="ays_project_options_content ays_project_attributes_content">
                                            <h6><?= __('Set your project attributes', $this->plugin_name); ?></h6>
                                            <hr/>
											<?php
											if (isset($portfolio_attributes)) {
												foreach ( $portfolio_attributes as $key => $attribute ):
													?>
													<?php
													if ($attribute['type'] == "textarea"):
														?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-3">
                                                                <label><?= __($attribute['name'], $this->plugin_name); ?></label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <textarea name="<?= $attribute['slug']; ?>[]"
                                                                          class="ays-textarea"></textarea>
                                                            </div>
                                                        </div>
                                                        <hr/>
													<?php
													else:
														?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-3">
                                                                <label><?= __($attribute['name'], $this->plugin_name); ?></label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input class="ays-text-input"
                                                                       type="<?= $attribute['type']; ?>"
                                                                       name="<?= $attribute['slug']; ?>[]"/>
                                                            </div>
                                                        </div>
                                                        <hr/>
													<?php
													endif;
												endforeach;
											}
											?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="ays-accordion">
					<?php
					if ($id == null):
						?>
                        <li data-hamar="0">
                            <input type="hidden" data-new="0" class="ays_project_action" name="ays_project_action[]"
                                   value="insert">
                            <input type="checkbox" class="ays_li_collapse" checked>
                            <i class="fas fa-trash-alt ays-delete-project"></i>
                            <i class="fas fa-arrows-alt ays-move-images"></i>
                            <i class="fas fa-angle-down ays-animated-arrow"></i>
                            <div class="ays_project_main_img">
                                <input type="hidden" name="ays_project_main_image_path[]">
                                <img class="ays_project_main_image_path">
                            </div>
                            <div class="ays_project_name"><p>No name</p></div>
                            <div class="ays-project-fields">
                                <div class="nav-tab-wrapper">
                                    <a href="#ays_project0_tab1" tabindex="0"
                                       class="nav-tab nav-tab-active"><?= __("General", $this->plugin_name); ?></a>
                                    <a href="#ays_project0_tab2" tabindex="1"
                                       class="nav-tab"><?= __("Options", $this->plugin_name); ?></a>
                                    <a href="#ays_project0_tab3" tabindex="2"
                                       class="nav-tab"><?= __("Attributes", $this->plugin_name); ?></a>
                                </div>
                                <div class="ays_project_fields">
                                    <div class="ays_project_fields_tab">
                                        <div id="ays_project0_tab1"
                                             class="ays-project-attributes-content ays-project-attributes-content-active">
                                            <div class="ays_project_images">
                                                <div style="border-right: 1px solid #ccc; margin-right:10px; padding:10px; max-height: 120px; max-width:120px;">
                                                    <input type="hidden" name="ays_project_main_img[]"
                                                           class="ays_project_main_img_hi" data-required="true">
                                                    <div class="ays_project_main_image_div">
                                                        <div class="ays_project_main_image_add_icon"><i
                                                                    class="fas fa-plus ays-upload-btn"></i></div>
                                                        <img class="ays_project_main_image_path">
                                                    </div>
                                                    <p style="font-size:12px; color:grey; font-style:italic;">Select
                                                        project main image</p>
                                                </div>
                                                <div class="ays_project_images_div">
                                                    <div class="ays_project_add_images_div">
                                                        <div class="ays_project_images_add_icon"><i
                                                                    class="fas fa-plus ays-upload-btn"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ays_project_attr">
                                                <p></p>
                                                <div class="ays_image_attr_item form-group row">
                                                    <div class="col-sm-3">
                                                        <label><?= __("Project name", $this->plugin_name); ?></label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="ays-text-input ays_project_name_input" type="text"
                                                               name="ays_project_name[]"
                                                               placeholder="<?= __("Project name", $this->plugin_name); ?>"/>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="ays_image_attr_item form-group row">
                                                    <div class="col-sm-3">
                                                        <label><?= __("Project description", $this->plugin_name); ?></label>
                                                    </div>
                                                    <div class="col-sm-9 replacable">
														<?php

														$project_desc           = '';
														$project_desc_editor_id = 'project_desc0';
														$qt                     = array(
															'id'      => $project_desc_editor_id,
															'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close',
														);
														$tinymce_buttons        = array(
															'toolbar1' => "formatselect,bold,italic,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,wp_adv",
															'toolbar2' => "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
														);
														$project_desc_settings  = array(
															'editor_height'       => '140',
															'textarea_name'       => 'ays_project_description[]',
															'editor_class'        => 'ays-textarea',
															'media_buttons'       => false,
															'wpautop'             => true,
															'teeny'               => false,
															'dfw'                 => true,
															'_content_editor_dfw' => true,
															'tinymce'             => $tinymce_buttons,
															'quicktags'           => $qt,
														);
														wp_editor($project_desc, $project_desc_editor_id, $project_desc_settings);
														?>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="ays_image_attr_item form-group row">
                                                    <div class="col-sm-3">
                                                        <label><?= __("Project url", $this->plugin_name); ?></label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="ays-text-input" type="url"
                                                               name="ays_project_url[]"
                                                               placeholder="<?= __("Project url", $this->plugin_name); ?>"/>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="ays_project_date[]"
                                                       class="ays_project_date"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ays_project_fields_tab">
                                        <div id="ays_project0_tab2" class="ays-project-attributes-content">
                                            <div class="ays_project_options_content">
                                                <h6><?= __('Set your project options', $this->plugin_name); ?></h6>
                                                <hr/>
                                                <div class="form-group row">
                                                    <div class="col-sm-3">
                                                        <label><?= __("Open URL", $this->plugin_name); ?></label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <label>
                                                            <input class="ays_project_url_open_js" type="checkbox"
                                                                   name="ays_project_url_open[]"/>
                                                            <span> in new tab</span>
                                                            <input type="hidden" class="ays_project_url_open"
                                                                   name="ays_project_url_open[]" value="off">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ays_project_fields_tab">
                                        <div id="ays_project0_tab3" class="ays-project-attributes-content">
                                            <div class="ays_project_options_content ays_project_attributes_content">
                                                <h6><?= __('Set your project attributes', $this->plugin_name); ?></h6>
                                                <hr/>
												<?php
												if (isset($portfolio_attributes)) {
													foreach ( $portfolio_attributes as $key => $attribute ):
														?>
														<?php
														if ($attribute['type'] == "textarea"):
															?>
                                                            <div class="form-group row">
                                                                <div class="col-sm-3">
                                                                    <label><?= __($attribute['name'], $this->plugin_name); ?></label>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <textarea name="<?= $attribute['slug']; ?>[]"
                                                                              class="ays-textarea"></textarea>
                                                                </div>
                                                            </div>
                                                            <hr/>
														<?php
														else:
															?>
                                                            <div class="form-group row">
                                                                <div class="col-sm-3">
                                                                    <label><?= __($attribute['name'], $this->plugin_name); ?></label>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <input class="ays-text-input"
                                                                           type="<?= $attribute['type']; ?>"
                                                                           name="<?= $attribute['slug']; ?>[]"/>
                                                                </div>
                                                            </div>
                                                            <hr/>
														<?php
														endif;
													endforeach;
												}
												?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
					<?php
					else:
						$project_i = 0;
						if (isset($portfolio_items)) {
							foreach ( $portfolio_items as $key => $project ) {
								$project_options    = json_decode($project["options"], true);
								$project_attributes = json_decode($project['attributes'], true);
								$project_images     = ($project["images"] == null || $project["images"] == '') ? [] : explode('***', $project["images"]);
								?>
                                <li data-hamar="<?= $project_i; ?>">
                                    <input type="hidden" data-new="0" class="ays_project_action"
                                           name="ays_project_action[]"
                                           value="update">
                                    <input type="checkbox" class="ays_li_collapse" checked>
                                    <i class="fas fa-trash-alt ays-delete-project"
                                       row-id="<?= $project['id']; ?>"></i>
                                    <i class="fas fa-arrows-alt ays-move-images"></i>
                                    <i class="fas fa-angle-down ays-animated-arrow"></i>
                                    <div class="ays_project_main_img" style="background-image:none;">
                                        <input type="hidden" name="ays_project_main_image_path[]"
                                               value="<?= $project['main_image']; ?>">
                                        <img class="ays_project_main_image_path" <?= $project['main_image'] == '' ? '' : "src='" . $project['main_image'] . "'"; ?>>
                                    </div>
                                    <div class="ays_project_name">
                                        <p><?= $project['name'] == '' ? 'No name' : $project['name']; ?></p></div>
                                    <div class="ays-project-fields">
                                        <div class="nav-tab-wrapper">
                                            <a href="#ays_project<?= $project_i; ?>_tab1" tabindex="0"
                                               class="nav-tab nav-tab-active"><?= __("General", $this->plugin_name); ?></a>
                                            <a href="#ays_project<?= $project_i; ?>_tab2" tabindex="1"
                                               class="nav-tab"><?= __("Options", $this->plugin_name); ?></a>
                                            <a href="#ays_project<?= $project_i; ?>_tab3" tabindex="2"
                                               class="nav-tab"><?= __("Attributes", $this->plugin_name); ?></a>
                                        </div>
                                        <div class="ays_project_fields">
                                            <div class="ays_project_fields_tab">
                                                <div id="ays_project<?= $project_i; ?>_tab1"
                                                     class="ays-project-attributes-content ays-project-attributes-content-active">
                                                    <div class="ays_project_images">
                                                        <div style="border-right: 1px solid #ccc; margin-right:10px; padding:10px; max-height: 120px; max-width:120px;">
                                                            <input type="hidden" name="ays_project_main_img[]"
                                                                   class="ays_project_main_img_hi"
                                                                   value="<?= $project['main_image']; ?>"
                                                                   data-required="true">
                                                            <div class="ays_project_main_image_div"
                                                                 style="background-image:none;">
																<?php if ($project['main_image'] == ''): ?>
                                                                    <div class="ays_project_main_image_add_icon"><i
                                                                                class="fas fa-plus ays-upload-btn"></i>
                                                                    </div>
																<?php endif; ?>
                                                                <img class="ays_project_main_image_path" <?= $project['main_image'] == '' ? '' : "src='" . $project['main_image'] . "'"; ?>>
                                                                <div class="ays_image_thumb" <?= $project['main_image'] == '' ? 'style="display:none"' : ''; ?>>
                                                                    <div class="ays_image_edit_div"><i
                                                                                class="fas fa-pencil-alt ays_image_edit"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p style="font-size:12px; color:grey; font-style:italic;"><?= __("Select project main image", $this->plugin_name); ?> </p>
                                                        </div>
                                                        <div class="ays_project_images_div">
															<?php
															foreach ( $project_images as $i => $image ):
																?>
                                                                <div class="ays_project_add_image_div"
                                                                     style="background-image:none;">
                                                                    <img class="ays_project_images_path"
                                                                         src="<?= $image; ?>">
                                                                    <input type="hidden"
                                                                           name="ays_project_images[<?= $project_i; ?>][]"
                                                                           class="ays_project_images_hi"
                                                                           value="<?= $image; ?>">
                                                                    <div class="ays_image_thumb">
                                                                        <div class="ays_image_edit_div"><i
                                                                                    class="fas fa-pencil-alt ays_image_edit"></i>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button"
                                                                            class="ays_delete_image button">
                                                                        x
                                                                    </button>
                                                                </div>
															<?php
															endforeach;
															?>
                                                            <div class="ays_project_add_images_div">
                                                                <div class="ays_project_images_add_icon"><i
                                                                            class="fas fa-plus ays-upload-btn"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ays_project_attr">
                                                        <p></p>
                                                        <div class="ays_image_attr_item form-group row">
                                                            <div class="col-sm-3">
                                                                <label><?= __("Project name", $this->plugin_name); ?></label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input class="ays-text-input ays_project_name_input"
                                                                       type="text" name="ays_project_name[]"
                                                                       placeholder="<?= __("Project name", $this->plugin_name); ?>"
                                                                       value="<?= $project['name']; ?>"/>
                                                            </div>
                                                        </div>
                                                        <hr/>
                                                        <div class="ays_image_attr_item form-group row">
                                                            <div class="col-sm-3">
                                                                <label><?= __("Project description", $this->plugin_name); ?></label>
                                                            </div>
                                                            <div class="col-sm-9 replacable">
																<?php

																$project_desc           = stripslashes(($project['description']));
																$project_desc_editor_id = 'project_desc' . $project_i;
																$qt                     = array(
																	'id'      => $project_desc_editor_id,
																	'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close',
																);
																$tinymce_buttons        = array(
																	'toolbar1' => "formatselect,bold,italic,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,wp_adv",
																	'toolbar2' => "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
																);
																$project_desc_settings  = array(
																	'editor_height'       => '140',
																	'textarea_name'       => 'ays_project_description[]',
																	'editor_class'        => 'ays-textarea',
																	'media_buttons'       => false,
																	'wpautop'             => true,
																	'teeny'               => false,
																	'dfw'                 => true,
																	'_content_editor_dfw' => true,
																	'tinymce'             => $tinymce_buttons,
																	'quicktags'           => $qt,
																);
																wp_editor($project_desc, $project_desc_editor_id, $project_desc_settings);
																?>
                                                            </div>
                                                        </div>
                                                        <hr/>
                                                        <div class="ays_image_attr_item form-group row">
                                                            <div class="col-sm-3">
                                                                <label><?= __("Project url", $this->plugin_name); ?></label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input class="ays-text-input" type="url"
                                                                       name="ays_project_url[]"
                                                                       placeholder="<?= __("Project url", $this->plugin_name); ?>"
                                                                       value="<?= isset($project['project_url']) ? $project['project_url'] : ""; ?>"/>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="ays_project_date[]"
                                                               class="ays_project_date"
                                                               value="<?= isset($project_options['project_date']) ? $project_options['project_date'] : ""; ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ays_project_fields_tab">
                                                <div id="ays_project<?= isset($project_i) ? $project_i : 999; ?>_tab2"
                                                     class="ays-project-attributes-content">
                                                    <div class="ays_project_options_content">
                                                        <h6><?= __('Set your project options', $this->plugin_name); ?></h6>
                                                        <hr/>
                                                        <div class="form-group row">
                                                            <div class="col-sm-3">
                                                                <label><?= __("Open URL in new tab", $this->plugin_name); ?></label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <label>
                                                                    <input class="ays_project_url_open_js"
                                                                           type="checkbox" <?= (isset($project_options['url_open']) && $project_options['url_open'] == "on") ? "checked" : ""; ?> />
                                                                    <input type="hidden" class="ays_project_url_open"
                                                                           name="ays_project_url_open[]"
                                                                           value="<?= isset($project_options['url_open']) ? $project_options['url_open'] : ""; ?>">
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ays_project_fields_tab">
                                                <div id="ays_project<?= $project_i; ?>_tab3"
                                                     class="ays-project-attributes-content">
                                                    <div class="ays_project_options_content ays_project_attributes_content">
                                                        <h6><?= __('Set your project attributes', $this->plugin_name); ?></h6>
                                                        <hr/>
														<?php
														if (isset($portfolio_attributes)) {
															foreach ( $portfolio_attributes as $hamar => $attribute ):
																?>
																<?php
																if ($attribute['type'] == "textarea"):
																	?>
                                                                    <div class="form-group row">
                                                                        <div class="col-sm-3">
                                                                            <label><?= __($attribute['name'], $this->plugin_name); ?></label>
                                                                        </div>
                                                                        <div class="col-sm-9">
                                                                            <textarea
                                                                                    name="<?= $attribute['slug']; ?>[]"
                                                                                    class="ays-textarea"><?= isset($project_attributes[$attribute['slug']]) ? stripslashes(htmlentities($project_attributes[$attribute['slug']])) : ""; ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <hr/>
																<?php
																else:
																	?>
                                                                    <div class="form-group row">
                                                                        <div class="col-sm-3">
                                                                            <label><?= __($attribute['name'], $this->plugin_name); ?></label>
                                                                        </div>
                                                                        <div class="col-sm-9">
                                                                            <input class="ays-text-input"
                                                                                   type="<?= $attribute['type']; ?>"
                                                                                   name="<?= $attribute['slug']; ?>[]"
                                                                                   value="<?= isset($project_attributes[$attribute['slug']]) ? stripslashes(htmlentities($project_attributes[$attribute['slug']])) : ""; ?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <hr/>
																<?php
																endif;
															endforeach;
														}
														?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
								<?php
								$project_i ++;
							}
						}
					endif;
					?>
                </ul>

            </div>
            <div id="tab2" class="ays-portfolio-tab-content">
                <p class="ays-subtitle"><?= __('Set up your portfolio', $this->plugin_name); ?></p>
                <h6><?= __('General options', $this->plugin_name); ?></h6>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label>
							<?= __("Show portfolio header", $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip"
                               title="<?= __("You can decide whether to show the title and description of the portfolio or not", $this->plugin_name); ?>">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <label class="ays_prg_image_hover_icon"><?= __("Show portfolio title ", $this->plugin_name); ?>
                            <input type="checkbox" class=""
                                   name="ays_prg_title_show" <?= (isset($prg_options['show_prg_title']) && $prg_options['show_prg_title'] == "on") ? "checked" : ""; ?>/>
                            <a class="ays_help poqr_tooltip" data-toggle="tooltip"
                               title="<?= __("If it is marked it will show the title", $this->plugin_name); ?>">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </label>
                        <label class="ays_prg_image_hover_icon"><?= __("Show portfolio description ", $this->plugin_name); ?>
                            <input type="checkbox" class=""
                                   name="ays_prg_desc_show" <?= (isset($prg_options['show_prg_title']) && $prg_options['show_prg_desc'] == "on") ? "checked" : ""; ?>/>
                            <a class="ays_help" data-toggle="tooltip"
                               title="<?= __("If it is marked it will show the description", $this->plugin_name); ?>">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </label>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_image_sizes">
							<?= __("Projects main image size", $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip"
                               title='<?= __('The size of the project main image in portfolio. For optimize page speed recomended "Medium" or "Medium_large".', $this->plugin_name); ?>'>
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <select name="ays_image_sizes" id="ays_image_sizes">
                            <option value="full_size"><?= __('Full size'); ?></option>
							<?php
							foreach ( $image_sizes as $key => $size ):
								?>
                                <option <?= $prg_options["image_sizes"] == $key ? 'selected' : ''; ?>
                                        value="<?= $key; ?>">
									<?php
									$name = ucfirst($key);
									echo __("$name ({$size['width']}x{$size['height']})");
									?>
                                </option>
							<?php
							endforeach;
							?>
                        </select>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label>
							<?= __("Images loading", $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip"
                               title="<?= __("The images are loaded according to two principles: already loaded portfolio with images and at first opens portfolio after then the images", $this->plugin_name); ?>">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <div>
                            <label class="ays_prg_image_hover_icon"><?= __("Global loading ", $this->plugin_name); ?>
                                <input type="radio" class=""
                                       name="ays_images_loading" <?= ((isset($prg_options['prg_images_loading']) && $prg_options['prg_images_loading'] != 'current_loaded') || !isset($prg_options['prg_images_loading'])) ? 'checked' : ""; ?>
                                       value="all_loaded"/>
                            </label>
                            <label class="ays_prg_image_hover_icon"><?= __("Lazy loading ", $this->plugin_name); ?>
                                <input type="radio" class=""
                                       name="ays_images_loading" <?= (isset($prg_options['prg_images_loading']) && $prg_options['prg_images_loading'] == 'current_loaded') ? 'checked' : ""; ?>
                                       value="current_loaded"/></label>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays-prg-width">
							<?= __("Portfolio Width", $this->plugin_name); ?> (px)
                            <a class="ays_help" data-toggle="tooltip"
                               title="<?= __("This field shows the width of the Portfolio", $this->plugin_name); ?>">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" name="ays-prg-width" id="ays-prg-width"
                               value=<?= isset($prg_options['prg_width']) && $prg_options['prg_width'] > 0 ? $prg_options['prg_width'] : 0; ?>>
                        <span class="ays_prg_image_hover_icon_text"><?= __("For 100% leave blank or 0", $this->plugin_name); ?></span>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays-view-type">
							<?= __("Portfolio view type", $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip"
                               title="This section notes the view type of the Portfolio that is in what sequence should the pictures of projects be">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-3">
                        <div>
                            <select name="ays-view-type" id="ays-view-type">
                                <option value="grid" <?= isset($prg_options['prg_view_type']) && $prg_options['prg_view_type'] == 'mosaic' ? "selected" : ""; ?>>
                                    Grid
                                </option>
                                <!-- <option value="mosaic" <? //=isset($prg_options['prg_view_type']) && $prg_options['prg_view_type'] == 'mosaic' ? "selected" : "";?>>
                                    Mosaic
                                </option> -->
                            </select>
                        </div>
                    </div>
                    <div id="ays-columns-count" class="col-sm-6 row">
                        <div class="col-sm-4">
                            <label>
								<?= __("Columns count", $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip"
                                   title="<?= __("The counts of the columns of the Portfolio", $this->plugin_name); ?>">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="number" name="ays-columns-count" class="ays-text-input ays-text-input-short"
                                   placeholder="<?= __("Default", $this->plugin_name); ?>: 3"
                                   value="<?= isset($prg_options['columns_count']) ? $prg_options['columns_count'] : 3; ?>"/>
                            <span class="ays_prg_image_hover_icon_text"><?= __("Default: 3", $this->plugin_name); ?></span>
                        </div>
                    </div>
                </div>

            </div>
            <div id="tab3" class="ays-portfolio-tab-content">
                <p class="ays-subtitle"><?= __('Set up your projects', $this->plugin_name); ?></p>
                <h6><?= __('Projects settings', $this->plugin_name); ?></h6>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="show_title">
							<?= __("Show title of project", $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip"
                               title="<?= __("The name of the project is written in the below of it", $this->plugin_name); ?>">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <label class="ays_prg_image_hover_icon"><?= __("Show title ", $this->plugin_name); ?>
                            <input type="checkbox" id="show_title" class=""
                                   name="ays_project_show_title" <?= (isset($prg_options['show_project_title']) && $prg_options['show_project_title'] == "on") ? "checked" : ""; ?>/></label>

                        <div class="show_with_date"
                             style="display: inline-block;"><?= __("Show on", $this->plugin_name); ?>:
                            <label class="ays_prg_image_hover_icon"><?= __("Project hover ", $this->plugin_name); ?>
                                <input type="radio" class="ays_project_show_title_on"
                                       name="ays_project_show_title_on" <?= (isset($prg_options['show_project_title_on']) && $prg_options['show_project_title_on'] == "image_hover") || !isset($prg_options['show_project_title_on']) ? "checked" : ""; ?>
                                       value="image_hover"/></label>
                            <label class="ays_prg_image_hover_icon"><?= __("Project thumbnail ", $this->plugin_name); ?>
                                <input type="radio" class="ays_project_show_title_on"
                                       name="ays_project_show_title_on" <?= (isset($prg_options['show_project_title_on']) && $prg_options['show_project_title_on'] == "project_image") ? "checked" : ""; ?>
                                       value="project_image"/></label>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays-lightbox-images-fit">
							<?= __("Lightbox image fit", $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip"
                               title='This option is used to specify how the image should be resized to fit its container. This option tells the content to fill the container in a variety of ways; such as "preserve that aspect ratio"(Contain) or "stretch up and take up as much space as possible"(Cover).'>
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <div>
                            <select name="ays-lightbox-images-fit" id="ays-view-type">
                                <option value="contain" <?= isset($prg_options['images_fit']) && $prg_options['images_fit'] == 'contain' ? "selected" : ""; ?>>
                                    Contain
                                </option>
                                <option value="cover" <?= isset($prg_options['images_fit']) && $prg_options['images_fit'] == 'cover' ? "selected" : ""; ?>>
                                    Cover
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
			<?php
			wp_nonce_field('ays_portfolio_action', 'ays_portfolio_action');
			?>
            <hr/>
            <div class="ays_submit_button">
                <input type="submit" name="ays-submit" class="ays-submit button-primary"
                       value="<?= __("Save changes", $this->plugin_name); ?>"/>
				<?php if ($id != null): ?>
                    <input type="submit" name="ays-apply" class="ays-submit button"
                           value="<?= __("Apply changes", $this->plugin_name); ?>"/>
				<?php endif; ?>
            </div>
        </form>
    </div>