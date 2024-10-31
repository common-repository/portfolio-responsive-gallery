(function($) {
    'use strict';
    $(function() {
        $(window).on('open.lightbox', function() {
            $('body').append("<div class='levon_lightbox_overlay'></div>");
            $('.levon_lightbox_overlay').css({
                display: 'block',
                animation: 'fadeIn .5s'
            });
        }).on('close.lightbox', function() {
            $('.levon_lightbox_overlay').remove();
        });

        function closeLightBox() {
            $(window).off('open.lightbox').off('close.lightbox')
            $(window).on('open.lightbox', function() {
                $('body').append("<div class='levon_lightbox_overlay'></div>");
                $('.levon_lightbox_overlay').css({
                    display: 'block',
                    animation: 'fadeIn .5s'
                });
            }).on('close.lightbox', function() {
                $('.levon_lightbox_overlay').remove();
            });
            $('body, html').removeClass('no-scroll');
            $(document).find('button.fs-lightbox-close').trigger('click')
        }
        $(document).on('click', '#prg-modal-close', closeLightBox)
        $(document).on('click', '.levon_lightbox_overlay', closeLightBox)

        //projects slider
        function indexing() {
            let uniqId = 'prgRow' + Date.now();
            let row = $(this);
            let cells = row.children('div');
            let cellsCount = cells.length;
            cells.each(function() {
                let index = +$(this).index();
                $(this).attr('data-index', index);
                $(this).attr('data-count', cellsCount);
                $(this).attr('data-prg', uniqId);
            })
        }
        $('.prg-container .prg_grid_row').each(indexing)
        $('.prg-container .prg_mosaic_row').each(indexing)
        $(document).find('.open_project_lightbox').on('click', function() {
            let cell = $(this).parent().parent();
            let index = +cell.attr('data-index')
            if (isNaN(index)) {
                cell = $(this).parent();
                index = +cell.attr('data-index')
            }
            let cellCount = cell.attr('data-count');

            let uniqId = cell.attr('data-prg')
            let next = index == cellCount - 1 ? 0 : index + 1;
            let prev = index == 0 ? cellCount - 1 : index - 1;

            let leftArrow = $(`<div class="prg-arrows" id="prg-left" data-prg="${uniqId}" data-index="${prev}">
                <svg enable-background="new 0 0 35 35" height="35px" id="Layer_1" version="1.1" viewBox="0 0 35 35" width="35px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path d="M7.701,14.276l9.586-9.585c0.879-0.878,2.317-0.878,3.195,0l0.801,0.8c0.878,0.877,0.878,2.316,0,3.194  L13.968,16l7.315,7.315c0.878,0.878,0.878,2.317,0,3.194l-0.801,0.8c-0.878,0.879-2.316,0.879-3.195,0l-9.586-9.587  C7.229,17.252,7.02,16.62,7.054,16C7.02,15.38,7.229,14.748,7.701,14.276z" fill="#d3d3d3"/>
                </svg>
            </div>`);
            let rightArrow = $(`<div class="prg-arrows" id="prg-right" data-prg="${uniqId}" data-index="${next}">
                <svg enable-background="new 0 0 35 35" height="35px" id="Layer_1" version="1.1" viewBox="0 0 35 35" width="35px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path d="M7.701,14.276l9.586-9.585c0.879-0.878,2.317-0.878,3.195,0l0.801,0.8c0.878,0.877,0.878,2.316,0,3.194  L13.968,16l7.315,7.315c0.878,0.878,0.878,2.317,0,3.194l-0.801,0.8c-0.878,0.879-2.316,0.879-3.195,0l-9.586-9.587  C7.229,17.252,7.02,16.62,7.054,16C7.02,15.38,7.229,14.748,7.701,14.276z" fill="#d3d3d3"/>
                </svg>
            </div>`);
            setTimeout(() => {
                $('.ays_project').prepend(leftArrow)
                $('.ays_project').append(rightArrow)
            }, 1500);
        });

        $(document).on('click', '.prg-arrows svg', function() {
            let index = +$(this).parent().attr('data-index');
            let uniqId = $(this).parent().attr('data-prg');
            $(window).off('open.lightbox').off('close.lightbox')
            $(window).on('close.lightbox', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('.levon_lightbox_overlay').empty().append(`
                    <div class="loader-levon">
                        <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg"         xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                           width="100px" height="100px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;"      xml:space="preserve">
                        <path fill="#000" d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,      6.543-14.615,14.615-14.615V6.461z">
                          <animateTransform attributeType="xml"
                            attributeName="transform"
                            type="rotate"
                            from="0 25 25"
                            to="360 25 25"
                            dur="0.8s"
                            repeatCount="indefinite"/>
                          </path>
                        </svg>
                    </div>
                `)
                return false;
            });
            $('body, html').addClass('no-scroll');
            $(document).find('button.fs-lightbox-close').trigger('click');
            setTimeout(() => {
                $(document)
                    .find(`[data-index="${index}"][data-prg="${uniqId}"]`)
                    .find('.open_project_lightbox')
                    .trigger('click');
            }, 500);
        })

    })
})(jQuery);