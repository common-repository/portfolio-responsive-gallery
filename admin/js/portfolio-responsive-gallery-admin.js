(function($) {
    'use strict';


    $(document).ready(function() {
        $('#ays-portfolio-form').on('submit', function(e) {
            let isValid = false;
            if ($(this).find(`[data-required="true"]`).length > 0) {
                $(this).find(`[data-required="true"]`).each(function() {
                    if ($(this).val() == '' || $(this).val() == null) {
                        let el = $(this)
                        if (el.parents('.acordion-el-for-clone').length) {
                            return true;
                        }
                        let name = el.hasClass('ays_project_main_img_hi') ? "Project main image" : "";
                        let tabContent = $(this).parents('.ays-portfolio-tab-content');
                        let tabID = tabContent.attr('id');
                        let tab = $(`.nav-tab-wrapper a.nav-tab[href="#${tabID}"]`)
                        $(document).find('.nav-tab-wrapper a.nav-tab').each(function() {
                            if ($(this).hasClass('nav-tab-active')) {
                                $(this).removeClass('nav-tab-active');
                            }
                        });
                        tab.addClass('nav-tab-active');
                        $(document).find('#ays-portfolio-form > .ays-portfolio-tab-content').each(function() {
                            if ($(this).hasClass('ays-portfolio-tab-content-active'))
                                $(this).removeClass('ays-portfolio-tab-content-active');
                        });
                        tabContent.addClass('ays-portfolio-tab-content-active');

                        $("html, body").animate({
                            scrollTop: el.offset().top - 40
                        }, "slow");
                        let errorDiv = $(`<div class="ays-alert alert alert-danger alert-dismissible fade show">
                                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                <strong>${name?name:'Some'}</strong> field is required. Please fill it for save the poll.
                                            </div>`);
                        $('#ays-portfolio-form .ays-alert').fadeOut();
                        $('#ays-portfolio-form').append(errorDiv)
                        setTimeout(() => {
                            errorDiv.fadeOut()
                        }, 7500);
                        isValid = false;
                        return false;
                    } else {
                        isValid = true;
                    }
                })
            } else {
                isValid = true;
            }
            if (isValid) {
                $('.acordion-el-for-clone').remove()
            } else {
                e.preventDefault()
                return false
            }
        })
        $(document).find('#ays-portfolio-form > .nav-tab-wrapper a.nav-tab').on('click', function(e) {
            let elemenetID = $(this).attr('href');
            $(document).find('#ays-portfolio-form > .nav-tab-wrapper a.nav-tab').each(function() {
                if ($(this).hasClass('nav-tab-active')) {
                    $(this).removeClass('nav-tab-active');
                }
            });
            $(this).addClass('nav-tab-active');
            $(document).find('#ays-portfolio-form > .ays-portfolio-tab-content').each(function() {
                if ($(this).hasClass('ays-portfolio-tab-content-active'))
                    $(this).removeClass('ays-portfolio-tab-content-active');
            });
            $(`#ays-portfolio-form > ${elemenetID}.ays-portfolio-tab-content`).addClass('ays-portfolio-tab-content-active');
            e.preventDefault();
        });

        $(document).on('click', '#tab1 .nav-tab-wrapper a.nav-tab', function(e) {
            let elemenetID = $(this).attr('href');
            let tokos = 100 / 3;
            let ays_project_fields_width = $(this).parents('.ays-project-fields').find('.ays_project_fields').width();

            $($(this).parent().parent().parent()).find('.nav-tab-wrapper a.nav-tab').each(function() {
                if ($(this).hasClass('nav-tab-active')) {
                    $(this).removeClass('nav-tab-active');
                }
            });
            $(this).addClass('nav-tab-active');
            $($(this).parent().parent().parent()).find('.ays-project-attributes-content').each(function() {
                if ($(this).hasClass('ays-project-attributes-content-active'))
                    $(this).removeClass('ays-project-attributes-content-active');
            });


            $('#tab1 .ays-project-attributes-content' + elemenetID).addClass('ays-project-attributes-content-active');

            $(this).parents('.ays-project-fields').find('.ays_project_fields').css({
                "transform": "translateX(" + $(this).attr('tabindex') * -(tokos) + "%)"
            });

            e.preventDefault();
        });

        // $(document).find('#ays-attribute-name').on('input', function() {
        //     $('#ays-attribute-slug').val($(this).val().replace(/  +/g, ' ').replace(/[^\w\s]/gi, '').toLowerCase().split(' ').join('_'));
        // });
        //$(document).find('.ays_gpg_lightbox_color').wpColorPicker();

        let current_fs, next_fs, previous_fs; //fieldsets
        let left, opacity, scale; //fieldset properties which we will animate
        let animating; //flag to prevent quick multi-click glitches

        $(document).find('.gpg_opacity_demo').css('opacity', $(document).find('.gpg_opacity_demo_val').val())


        $(document).on('input', '.gpg_opacity_demo_val', function() {
            $(document).find('.gpg_opacity_demo').css('opacity', $(this).val());
        });

        $(document).on('input', '.ays_project_name_input', function() {
            $(this).parents('li').find('.ays_project_name p').text($(this).val());
            if ($(this).val() == '') {
                $(this).parents('li').find('.ays_project_name p').text('No name');
            }
        });

        $(document).on('click', '.ays-add-multiple-images', function(e) {
            openMediaUploader_forMultiple(e, $(this));
        });

        $(document).on('click', '.ays-add-video', function(e) {
            openMediaUploader_forVideo(e, $(this));
        });

        if ($(document).find('#show_title').attr('checked')) {
            $(document).find('.show_with_date').css('display', 'inline-block');
        } else {
            $(document).find('.show_with_date').css('display', 'none');
        }


        $(document).find('#show_title').on('click', function() {
            $(document).find('.show_with_date').toggle();
            if ($(document).find('#show_title').attr('checked')) {
                $(document).find('.show_with_date').css('display', 'inline-block');
            } else {
                $(document).find('.show_with_date').css('display', 'none');
            }
        });

        $(document).on('click', '.ays_project_main_image_add_icon', function(e) {
            openMediaUploader_MainImage(e, $(this), 'ays_project_main_image_add_icon');
        });

        $(document).on('click', '.ays_clear_projects', function(e) {
            let accordion = $(document).find('ul.ays-accordion');
            accordion.children('li').remove();
        });

        $(document).on('click', '.ays_project_main_image_div .ays_image_edit', function(e) {
            openMediaUploader_MainImage(e, $(this), 'ays_image_edit');
        });

        $(document).on('click', '.ays_project_add_image_div .ays_image_edit_div', function(e) {
            openMediaUploader_forEditImage(e, $(this));
        });

        $(document).on('click', '.ays_project_images_add_icon', function(e) {
            openMediaUploader_forAddImage(e, $(this));
        });

        $(document).on('click', '.ays_project_url_open_js', function(e) {
            if ($(this).attr('checked')) {
                $(this).parent().find('.ays_project_url_open').val('on');
            } else {
                $(this).parent().find('.ays_project_url_open').val('off');
            }
        });

        //        $(document).find('#ays_project_add_id').val($(document).find('ul.ays-accordion li').length);
        $(document).find('.ays-add-project').on('click', function(e) {
            e.preventDefault();
            let accordion = $(document).find('ul.ays-accordion'),
                accordion_el = $(document).find('ul.ays-accordion li'),
                accordion_el_length = accordion_el.length,
                noimage_path = $(document).find('#noimage_path').val(),
                ays_project_add_id = parseInt($(document).find('#ays_project_add_id').val()),
                hamar = parseInt(accordion_el_length) + 1,
                wp_editor_id = 'editor-1',
                wp_editor_settings = {};
            // Remember to wp_enqueue_editor(); inside PHP.


            // Remove editor
            //                wp.editor.remove('test-editor');
            //                wp_editor_for_project =  wp.editor.initialize(wp_editor_id, true);
            var accordion_el_data_index;
            if (parseInt(accordion_el_length) == 0) {
                accordion_el_data_index = 1;
            } else {
                accordion_el_data_index = parseInt(accordion_el[accordion_el_length - 1].getAttribute('data-hamar')) + 1;
            }


            var d = new Date()
            var date = d.getTime();
            date = Math.floor(date / 1000);
            let testLi = $('.acordion-el-for-clone li').clone();
            //            testLi.empty();
            testLi.attr("data-hamar", accordion_el_data_index);
            accordion.append(testLi);
            testLi.find('.nav-tab-wrapper a').each(function(e) {
                //console.log(accordion_el_data_index);
                $(this).attr('href', "#ays_project" + accordion_el_data_index + "_tab" + (e + 1));
            });
            testLi.find('.ays-project-attributes-content').each(function(e) {
                $(this).attr('id', "ays_project" + accordion_el_data_index + "_tab" + (e + 1));
            });
            testLi.find("#ays_project" + accordion_el_data_index + "_tab1 .ays_image_attr_item .col-sm-9.replacable").html(`<textarea id="ays_project_desc` + (accordion_el_data_index) + `" class="ays-textarea" name="ays_project_description[]"></textarea>`);
            testLi.find("input[type=text]").val("");
            testLi.find("input[type=tel]").val("");
            testLi.find("input[type=number]").val("");
            testLi.find("input[type=url]").val("");
            testLi.find("input[type=email]").val("");
            testLi.find("input[type=checkbox]").removeAttr("checked");
            testLi.find("input[type=checkbox].ays_li_collapse").attr("checked", "checked");
            testLi.find("input[type=radio]").removeAttr("checked");
            testLi.find("input[type=email]").val("");
            testLi.find("select option").removeAttr("selected");
            testLi.find("input[type=hidden]").val("");
            testLi.find(".ays_project_action").val("insert");
            testLi.find("img").removeAttr("src");
            testLi.find(".ays_project_main_image_div, .ays_project_main_img").removeAttr("style");
            testLi.find(".ays_project_main_image_div, .ays_project_main_img").parent().find('.ays_image_thumb').remove();
            testLi.find(".ays_project_main_image_div").html(`
                                            <div class="ays_project_main_image_add_icon"><i class="fas fa-plus ays-upload-btn"></i></div>
                                            <img class="ays_project_main_image_path">`);
            testLi.find(".ays_project_images_div").html(`                                          
                                        <div class="ays_project_add_images_div">
                                            <div class="ays_project_images_add_icon"><i class="fas fa-plus ays-upload-btn"></i></div>
                                        </div>`);
            testLi.find(".ays_project_name p").html('No name');
            testLi.find("textarea").html("");
            testLi.find(".ays_project_main_image_path").remove();

            accordion_el_length++;
            // Add editor
            var wp_editor_for_project = wp.editor.initialize(
                'ays_project_desc' + accordion_el_data_index, {
                    tinymce: {
                        wpautop: true,
                        theme: "modern",
                        skin: "lightgray",
                        formats: {
                            alignleft: [{
                                    selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li',
                                    styles: {
                                        textAlign: 'left'
                                    }
                                },
                                {
                                    selector: 'img,table,dl.wp-caption',
                                    classes: 'alignleft'
                                }
                            ],
                            aligncenter: [{
                                    selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li',
                                    styles: {
                                        textAlign: 'center'
                                    }
                                },
                                {
                                    selector: 'img,table,dl.wp-caption',
                                    classes: 'aligncenter'
                                }
                            ],
                            alignright: [{
                                    selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li',
                                    styles: {
                                        textAlign: 'right'
                                    }
                                },
                                {
                                    selector: 'img,table,dl.wp-caption',
                                    classes: 'alignright'
                                }
                            ],
                            strikethrough: {
                                inline: 'del'
                            }
                        },
                        relative_urls: false,
                        remove_script_host: false,
                        convert_urls: false,
                        browser_spellcheck: true,
                        fix_list_elements: false,
                        entities: "38,amp,60,lt,62,gt",
                        entity_encoding: "raw",
                        keep_styles: false,
                        paste_webkit_styles: "font-weight font-style color",
                        preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
                        wpeditimage_disable_captions: false,
                        wpeditimage_html5_captions: true,
                        plugins: "charmap,hr,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview",
                        resize: "vertical",
                        menubar: false,
                        indent: true,
                        toolbar1: "formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,wp_adv",
                        toolbar2: "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
                        toolbar3: "",
                        toolbar4: "",
                        tabfocus_elements: ":prev,:next",
                        body_class: "id post-type-post post-status-publish post-format-standard",
                    },
                    quicktags: {
                        'id': 'ays_project_desc' + accordion_el_data_index,
                        'buttons': 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,fullscreen'
                    }
                }
            );
            $(document).find('#accordion_number').attr('value', hamar);
            $(document).find('#ays_project_add_id').val(ays_project_add_id + 1);
        });

        $(document).find('ul.ays-accordion').sortable({
            handle: '.fa-arrows-alt',
            axis: 'y',
            opacity: 0.8,
            placeholder: 'clone',
            cursor: 'move'
        });

        $(document).on('click', '.ays-delete-project', function() {
            var accordion_el_number = $(document).find('#accordion_number').val();
            var ays_project_add_id = parseInt($(document).find('#ays_project_add_id').val());
            //            var ays_project_add_id = document.getElementById('ays_project_add_id').value;
            //            ays_project_add_id = ays_project_add_id.split('');
            //            console.log(accordion_el);
            var hamar = parseInt(accordion_el_number) - 1;
            if (hamar < 0) {
                hamar = 0;
            }
            $(document).find('#accordion_number').attr('value', hamar);
            var ays_project_delete_id = $(document).find('#ays_project_delete_id').val();
            if ($(this).attr('row-id')) {
                $(document).find('#ays_project_delete_id').val(ays_project_delete_id + $(this).attr('row-id') + '***');
            } else {
                $(document).find('#ays_project_delete_id').val(ays_project_delete_id + '');
                //                ays_project_add_id.splice(ays_project_add_id.length-3, 3);
                //                ays_project_add_id = ays_project_add_id.join('');
                $(document).find('#ays_project_add_id').val(ays_project_add_id - 1);
            }

            $(this).parent().css({
                'animation-name': 'slideOutLeft',
                'animation-duration': '.3s'
            });

            var a = $(this);
            setTimeout(function() {
                a.parent().remove();


                var accordion = $(document).find('ul.ays-accordion');
                var accordion_el = $(document).find('ul.ays-accordion li');
                var accordion_el_lenght = accordion_el.length;
                /* aaa.each(function(e, el){
                     console.log(e)
                     console.log(el)
                     el.setAttribute('data-hamar', e);
                     if(el.querySelector('.ays_project_images_hi') !== null){
                         el.querySelector('.ays_project_images_hi').setAttribute('name', 'ays_project_images['+e+'][]');
                     }
                 });*/

                for (var i = 0; i < accordion_el_lenght; i++) {
                    // console.log(accordion_el[i])
                    accordion_el[i].setAttribute('data-hamar', i);
                    if (accordion_el[i].querySelectorAll('.ays_project_images_hi') !== null) {
                        var ays_project_images_hi = accordion_el[i].querySelectorAll('.ays_project_images_hi');
                        for (var j = 0; j < ays_project_images_hi.length; j++) {
                            var attr_name = ays_project_images_hi[j];
                            // console.log(attr_name);
                            attr_name.setAttribute('name', 'ays_project_images[' + i + '][]');
                        }
                    }
                }

            }, 300);



        });


        $(document).on('click', '.ays_delete_image', function() {
            var accordion_el_number = $(document).find('#accordion_number').val();
            var ays_project_add_id = parseInt($(document).find('#ays_project_add_id').val());

            $(this).parent().css({
                'animation-name': 'bounceOut',
                'animation-duration': '.5s'
            });

            var a = $(this);
            setTimeout(function() {
                a.parents('.ays_project_add_image_div').remove();
            }, 450);



        });

        function openMediaUploader_forEditImage(e, element) {
            e.preventDefault();
            let aysGalleryUploader = wp.media.frames.items = wp.media({
                title: 'Upload image',
                button: {
                    text: 'Upload'
                },
                library: {
                    type: ['image']
                },
                multiple: false,
                frame: 'select',
            }).on('select', function(e) {
                var state = aysGalleryUploader.state();
                var selection = selection || state.get('selection');
                if (!selection) return;

                var attachment = selection.first();
                var display = state.display(attachment).toJSON();

                attachment = attachment.toJSON();

                var d = new Date()
                var date = d.getTime();
                date = Math.floor(date / 1000);

                var imgurl = attachment.url; //sizes[display.size].url;

                element.parent().parent().children('.ays_project_images_path').attr('src', imgurl);
                element.parent().parent().children('.ays_project_images_hi').val(imgurl);
                element.parent().parent().children('.ays_project_images_path').css('background-image', 'none');
            }).open();
            return false;

        }

        function openMediaUploader_forAddImage(e, element) {
            e.preventDefault();
            let aysGalleryUploader = wp.media.frames.items = wp.media({
                title: 'Upload images',
                button: {
                    text: 'Upload'
                },
                library: {
                    type: ['image']
                },
                multiple: true,
                frame: 'select',
            }).on('select', function(e) {
                var state = aysGalleryUploader.state();
                var selection = selection || state.get('selection');
                if (!selection) return;

                var attachment = selection.first();
                var display = state.display(selection).toJSON();

                var attachment = selection.toJSON();

                var d = new Date()
                var date = d.getTime();
                date = Math.floor(date / 1000);

                //var imgurl = attachment.url;//sizes[display.size].url;
                var ays_project_new_image = '';
                // console.log(attachment)
                // console.log(element.parent().parent())
                for (let i = 0; i < attachment.length; i++) {
                    let accordion = $(document).find('ul.ays-accordion'),
                        accordion_el_li = $(document).find('ul.ays-accordion li'),
                        accordion_el = element.parents('li').attr('data-hamar'),
                        accordion_el_length = accordion_el_li.length;
                    var accordion_el_data_index;
                    if (parseInt(accordion_el_length) == 0) {
                        accordion_el_data_index = 0;
                    } else {
                        accordion_el_data_index = parseInt(accordion_el);
                    }
                    ays_project_new_image = '<div class="ays_project_add_image_div" style="background-image:none">' +
                        '       <input type="hidden" name="ays_project_images[' + (accordion_el_data_index) + '][]" class="ays_project_images_hi" value="' + attachment[i].url + '">' +
                        '       <img class="ays_project_main_image_path" src="' + attachment[i].url + '">' +
                        '       <div class="ays_image_thumb">' +
                        '            <div class="ays_image_edit_div"><i class="fas fa-pencil-alt ays_image_edit"></i></div>' +
                        '       </div>' +
                        '       <button type="button" class="ays_delete_image button">x</button>' +
                        '   </div>';

                    element.parent().parent().prepend(ays_project_new_image);
                }
                /*
                    let accordion = $(document).find('ul.ays-accordion'),
                        accordion_el_li = $(document).find('ul.ays-accordion li'),
                        accordion_el = element.parents('li').attr('data-hamar'),
                        accordion_el_length = accordion_el_li.length;
                    for(var i = 0; i < accordion_el_lenght; i++){
                        accordion_el_li[i].setAttribute('data-hamar', i);
                        if(accordion_el_li[i].querySelectorAll('.ays_project_images_hi') !== null){
                            var ays_project_images_hi = accordion_el_li[i].querySelectorAll('.ays_project_images_hi');
                            for(var j = 0; j < ays_project_images_hi.length; j++){
                                var attr_name = ays_project_images_hi[j];
//                                console.log(attr_name);
                                attr_name.setAttribute('name', 'ays_project_images['+i+'][]');
                            }
                        }
                    }*/
            }).open();
            return false;

        }

        function openMediaUploader_MainImage(e, element, where) {
            e.preventDefault();
            let aysGalleryUploader = wp.media.frames.items = wp.media({
                title: 'Upload image',
                button: {
                    text: 'Upload'
                },
                library: {
                    type: ['image']
                },
                multiple: false,
                frame: 'select',
            }).on('select', function(e) {
                if (where == 'ays_project_main_image_add_icon') {
                    var state = aysGalleryUploader.state();
                    var selection = selection || state.get('selection');
                    if (!selection) return;

                    var attachment = selection.first();
                    var display = state.display(attachment).toJSON();
                    attachment = attachment.toJSON();

                    var d = new Date()
                    var date = d.getTime();
                    date = Math.floor(date / 1000);

                    var imgurl = attachment.url; //sizes[display.size].url;
                    element.parents('li').find('.ays_project_main_img').html(`
                                                    <img class="ays_project_main_image_path" src="` + imgurl + `">
                                                    <div class="ays_image_thumb">
                                                        <div class="ays_image_edit_div"><i class="fas fa-pencil-alt ays_image_edit"></i></div>
                                                    </div>`);
                    element.parents('li').find('.ays_project_main_img').css('background-image', 'none');
                    element.parent().parent().parent().find('.ays_project_main_img_hi').val(imgurl).data('required', true);
                    element.parent().parent().find('.ays_project_main_image_div').css('background-image', 'none');
                    element.parent().parent().find('.ays_project_main_image_div').html(`
                                                    <img class="ays_project_main_image_path" src="` + imgurl + `">
                                                    <div class="ays_image_thumb">
                                                        <div class="ays_image_edit_div"><i class="fas fa-pencil-alt ays_image_edit"></i></div>
                                                    </div>`);
                    element.parent().parent().find('.ays_image_thumb').css('display', 'block');
                    element.parent().parent().find('.ays_project_main_image_add_icon').remove();

                } else {
                    if (where == 'ays_image_edit') {
                        var state = aysGalleryUploader.state();
                        var selection = selection || state.get('selection');
                        if (!selection) return;

                        var attachment = selection.first();
                        var display = state.display(attachment).toJSON();

                        attachment = attachment.toJSON();

                        var d = new Date()
                        var date = d.getTime();
                        date = Math.floor(date / 1000);

                        var imgurl = attachment.url; //sizes[display.size].url; 
                        element.parents('li').find('.ays_project_main_img').children('.ays_project_main_image_path').attr('src', imgurl);
                        element.parents('li').find('.ays_project_main_img').children('.ays_project_main_image_path').css('background-image', 'none');
                        element.parent().parent().parent().find('.ays_project_main_image_path').attr('src', imgurl);
                        element.parent().parent().parent().parent().find('.ays_project_main_img_hi').val(imgurl).data('required', true);
                        element.parent().parent().parent().find('.ays_project_main_image_path').css('background-image', 'none');
                    }
                }
            }).open();
            return false;

        }

        function openMediaUploader_forVideo(e, element) {
            e.preventDefault();
            let aysUploader = wp.media.frames.aysUploader = wp.media({
                title: 'Upload video',
                button: {
                    text: 'Upload'
                },
                multiple: false,
                library: {
                    type: ['video']
                },
                frame: "video",
                state: "video-details"
            }).on('select', function() {
                var state = aysUploader.state();
                var selection = selection || state.get('selection');
                if (!selection) return;

                var attachment = selection.first();
                var display = state.display(selection).toJSON();

                var attachment = selection.toJSON();
                // console.log(attachment);
            }).open();
            return;

        }

        function openMediaUploader_forMultiple(e, element) {
            e.preventDefault();
            let aysUploader = wp.media.frames.aysUploader = wp.media({
                title: 'Upload images',
                button: {
                    text: 'Upload'
                },
                multiple: true,
                library: {
                    type: ['image']
                },
                frame: "select"
            }).on('select', function() {
                var state = aysUploader.state();
                var selection = selection || state.get('selection');
                if (!selection) return;

                var attachment = selection.first();
                var display = state.display(selection).toJSON();

                var attachment = selection.toJSON();
                var d = new Date()
                var date = d.getTime();
                date = Math.floor(date / 1000);
                //				let attachment = aysUploader.state().get('selection').toJSON();
                for (let i = 0; i < attachment.length; i++) {
                    let accordion = $(document).find('ul.ays-accordion'),
                        accordion_el = $(document).find('ul.ays-accordion li'),
                        accordion_el_length = accordion_el.length;
                    //                    if(accordion_el_length < 24){
                    let newListImage = '<li>' +
                        '			<i class="fas fa-trash-alt ays-delete-project"></i>' +
                        '			<i class="fas fa-arrows-alt ays-move-images"></i>' +
                        '			<div class="ays_image_thumb" style="display: block;">' +
                        '			<div class="ays_image_edit_div">' +
                        '			<i class="fas fa-pencil-alt ays_image_edit"></i></div>' +
                        '			<img class="ays_ays_img" alt="" src="' + attachment[i].url + '">' +
                        '			</div>' +
                        '           <input type="hidden" name="ays-image-path[]" value="' + attachment[i].url + '">' +
                        '			<div class="ays-image-attributes">' +
                        '               <div class="ays_image_attr_item">' +
                        '                   <label>Image title</label>' +
                        '	                <input class="ays_img_title" type="text" name="ays-image-title[]" value="' + (attachment[i].title) + '" placeholder="Image title"/>' +
                        '               </div>' +
                        '               <div class="ays_image_attr_item">' +
                        '                   <label>Image alt</label>' +
                        '                   <input class="ays_img_alt" type="text" name="ays-image-alt[]" value="' + (attachment[i].title) + '" placeholder=""Image alt"/>' +
                        '               </div>' +
                        '               <div class="ays_image_attr_item">' +
                        '                   <label>Image description</label>' +
                        '                   <input class="ays_img_desc" type="text" name="ays-image-description[]" placeholder="Image description"/>' +
                        '               </div>' +
                        '               <div class="ays_image_attr_item">' +
                        '                   <label>Image url</label>' +
                        '                   <input class="ays_img_url" type="url" name="ays-image-url[]"  placeholder="Image url"/>' +
                        '               </div>' +
                        '           <input type="hidden" name="ays-image-date[]" class="ays_img_date" value="' + (date) + '"/>' +
                        '           </div>' +
                        '         </li>';

                    accordion.append(newListImage);
                }
            }).open();
            return;

        }

        if ($('#ays-view-type').val() == 'mosaic') {
            $(document).find('#ays-columns-count').slideUp({
                'display': 'none'
            });
        }
        $('[data-toggle="tooltip"]').tooltip();
        $('#ays-portfolio-form select').select2();
        $('#ays-portfolio-attribute-form select').select2();

        $(document).on('change', '#ays-view-type', function() {
            if ($('#ays-view-type').val() == 'mosaic') {
                $(document).find('#ays-columns-count').css({
                    'animation-name': 'fadeOut',
                    'animation-duration': '.5s'
                });
                setTimeout(function() {
                    $(document).find('#ays-columns-count').css({
                        'display': 'none'
                    });
                }, 480);
            } else {
                $(document).find('#ays-columns-count').css({
                    'display': 'flex',
                    'animation-name': 'fadeIn',
                    'animation-duration': '.5s'
                });
            }
        });
    });

})(jQuery);