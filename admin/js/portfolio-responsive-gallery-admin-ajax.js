(function($){
    'use strict';
    
  /*  $(document).ready(function(){
        $(document).on('click', ".ays-add-project", function(){
            $.ajax({
                url: prg_admin_ajax.ajax_url,
                data:  {
                    action: 'ays_get_attr_for_project'
                },
                method: 'post',
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    if( response !== 0 ) {
                        $(document).find('.ays_project_action').each(function(e, el){
                            var accordion_el_data_hamar = parseInt(el.parentElement.getAttribute('data-hamar'));
                            let ays_proj_attributes;
                            let ays_proj_data_new = el.getAttribute('data-new');
                            console.log(ays_proj_data_new)
                            if(ays_proj_data_new == 1 && el.value == 'insert' ){
                                for(var i = 0; i < response.length; i++){
                                    if(response[i].type == 'textarea'){
                                        ays_proj_attributes = `<div class="form-group row">
                                                    <div class="col-sm-3">
                                                        <label>`+response[i].name+`</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <textarea id="proj_attr_desc`+accordion_el_data_hamar+`_`+response[i].slug+`" class="ays-textarea" name="`+response[i].slug+`[]"></textarea>
                                                    </div>
                                                </div>
                                                <hr/>`;
                                    }else{
                                        ays_proj_attributes = `<div class="form-group row">
                                                    <div class="col-sm-3">
                                                        <label>`+response[i].name+`</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="ays-text-input" type="`+response[i].type+`" name="`+response[i].slug+`" />
                                                    </div>
                                                </div>
                                                <hr/>`;
                                    } el.parentElement.querySelector('.ays_project_options_content.ays_project_attributes_content').innerHTML += ays_proj_attributes;
                                    wp.editor.initialize(
                                        'proj_attr_desc'+accordion_el_data_hamar+'_'+response[i].slug,
                                        { 
                                        tinymce: {
                                            wpautop: true,
                                            theme:"modern",
                                            skin:"lightgray",
                                            formats:{
                                                alignleft: [
                                                    {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign:'left'}},
                                                    {selector: 'img,table,dl.wp-caption', classes: 'alignleft'}
                                                ],
                                                aligncenter: [
                                                    {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign:'center'}},
                                                    {selector: 'img,table,dl.wp-caption', classes: 'aligncenter'}
                                                ],
                                                alignright: [
                                                    {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign:'right'}},
                                                    {selector: 'img,table,dl.wp-caption', classes: 'alignright'}
                                                ],
                                                strikethrough: {inline: 'del'}
                                            },
                                            relative_urls:false,
                                            remove_script_host:false,
                                            convert_urls:false,
                                            browser_spellcheck:true,
                                            fix_list_elements:false,
                                            entities:"38,amp,60,lt,62,gt",
                                            entity_encoding:"raw",
                                            keep_styles:false,
                                            paste_webkit_styles:"font-weight font-style color",
                                            preview_styles:"font-family font-size font-weight font-style text-decoration text-transform",
                                            wpeditimage_disable_captions:false,
                                            wpeditimage_html5_captions:true,                       plugins:"charmap,hr,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview",
                                            resize:"vertical",
                                            menubar:false,
                                            indent:true,                       toolbar1:"formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,wp_adv",
                                            toolbar2:"strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
                                            toolbar3:"",
                                            toolbar4:"",
                                            tabfocus_elements:":prev,:next",
                                            body_class:"id post-type-post post-status-publish post-format-standard",
                                            },
                                            quicktags: {
                                                'id': 'proj_attr_desc'+accordion_el_data_hamar+'_'+response[i].slug,
                                                'buttons': 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,fullscreen'
                                            }
                                        }
                                    );
                                }
                            }
                        });
                    }
                }
            });
        });
    });  */
    
    
})(jQuery);