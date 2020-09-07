define([
    'jquery',
    'underscore',
    'matchMedia',
    'slick'
], function ($, _, mediaCheck) {
    'use strict';

    return function (config, element) {
        var unslickIndex,
            unslick,
            media;

        config = $.extend({
            rows: 0,
            responsive: []
        }, config);

        /**
         * Add new slide using ajax load
         *
         * @param  {Number} slideIndex - Index where to insert new slide
         * @param  {Object} slick
         * @return {Promise}
         */
        function loadSlide(slideIndex, slick) {
            var data = {},
                pageVar,
                blockData = slick.slickGetOption('blockData');

            //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
            pageVar = blockData.page_var_name ? blockData.page_var_name : 'p';
            data[pageVar] = slideIndex + 1;
            data.referer = window.location.href;
            data.block_data = blockData;
            //jscs:enable requireCamelCaseOrUpperCaseIdentifiers

            return $.ajax({
                    cache: true,
                    slideIndex: slideIndex,
                    url: slick.slickGetOption('dataSourceUrl'),
                    data: data
                })
                .done(function (json) {
                    slick.$slider.slick('slickAdd', $(json.html), slideIndex, true);

                    // init magento scripts
                    slick.$slider.find('.slick-active').trigger('contentUpdated');

                    // remove dummy slide with loading because when it is last page
                    if (json.isLastPage) {
                        slick.$slider.slick('slickRemove', slideIndex + 1);
                    }
                });
        }

        // observe before Slides change for current instance
        $(element).on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            if (slick.$slides.eq(nextSlide).closest('.slide').hasClass('loading')) {
                loadSlide(nextSlide, slick);
            }
        });

        unslickIndex = _.findIndex(config.responsive, function (entry) {
            return entry.settings === 'unslick';
        });

        if (unslickIndex > -1) {
            unslick = config.responsive.splice(unslickIndex, 1)[0];
            media = '(max-width: ' + unslick.breakpoint + 'px)';

            if (config.mobileFirst) {
                media = media.replace('max-width', 'min-width');
            }

            mediaCheck({
                media: media,
                entry: $.proxy(function () {
                    if ($(element).hasClass('slick-initialized')) {
                        $(element).slick('unslick');
                    }
                }, this),
                exit: $.proxy(function () {
                    $(element).slick(config);
                }, this)
            });
        } else {
            $(element).slick(config);
        }
    };
});
