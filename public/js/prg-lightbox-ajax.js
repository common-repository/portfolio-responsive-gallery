(function ($) {
    $(function () {
        $(document).on('click', '.open_project_lightbox', function () {
            launchLightbox($(this).attr('data-project'))
        });

        let owl = "";
        let thumbs = "";
        let syncedSecondary = true;

        function syncPosition(el) {
            let count = el.item.count - 1;
            let current = Math.round(el.item.index - (el.item.count / 2) - .5);

            if (current < 0) {
                current = count;
            }
            if (current > count) {
                current = 0;
            }

            thumbs
                .find('.owl-item')
                .removeClass('current')
                .eq(current)
                .addClass('current');
            let onscreen = thumbs.find('.owl-item.active').length - 1;
            let start = thumbs.find('.owl-item.active').first().index();
            let end = thumbs.find('.owl-item.active').last().index();

            if (current > end) {
                thumbs.data('owl.carousel').to(current, 100, true);
            }
            if (current < start) {
                thumbs.data('owl.carousel').to(current - onscreen, 100, true);
            }
            setTimeout(function () {
                $('#carousel-custom-dots .owl-stage').css({
                    transform: 'translate3d(0px,0px,0px)'
                });
            }, 300)
        }

        function syncPosition2(el) {
            if (syncedSecondary) {
                let number = el.item.index;
                owl.data('owl.carousel').to(number, 100, true);
            }
        }

        function launchLightbox(project_id) {
            let data = {
                project_id
            };
            data.action = 'ays_portfolio_load_project';
            $.post({
                url: prg_ajax_public.ajax_url,
                data,
                success: function (resp) {
                    $.lightbox($(resp), {
                        fixed: true,
                        retina: true,
                        requestKey: 'fs-lightbox',
                        overlay: true,
                        labels: {close: 'X'}
                    });
                    setTimeout(() => {
                        owl = $("#ays_project_images_ul").owlCarousel({
                            loop: true,
                            margin: 0,
                            nav: false,
                            items: 1,
                            dots: false,
                        });
                        thumbs = $('#carousel-custom-dots');
                        let imagesCount = $("#ays_project_images_ul").attr('data-count');
                        owl.on('changed.owl.carousel', syncPosition)
                        thumbs.on('initialized.owl.carousel', function () {
                            thumbs.find('.owl-item').eq(0).addClass('current');
                        }).owlCarousel({
                            items: imagesCount,
                            dots: false,
                            nav: false,
                            smartSpeed: 200,
                            slideSpeed: 500,
                            center: false,
                            responsiveRefreshRate: 100
                        }).on('changed.owl.carousel', syncPosition2);

                        thumbs.on('click', '.owl-item', function (e) {
                            e.preventDefault();
                            let number = $(this).index();
                            owl.data('owl.carousel').to(number, 300, true);
                        });

                        setTimeout(() => {
                            owl.trigger('refresh.owl.carousel');
                        }, 200);
                    }, 300)
                }
            });
        }
    })
})(jQuery)