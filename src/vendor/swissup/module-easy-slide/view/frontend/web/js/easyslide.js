define([
    'jquery',
    'Swissup_EasySlide/js/swiper'
], function ($, Swiper) {
    'use strict';

    $.widget('swissup.easyslide', {
        options: {
            autoHeight: true,
            centeredSlides: true,
            slidesPerView: 'auto',
            loop: true,
            roundLengths: true
        },

        /**
         * @inheritdoc
         */
        _create: function () {
            this._super();

            if (this.options.startRandomSlide) {
                // set random slide to start with
                this.options.initialSlide = Math.round(Math.random() * this.countSlides());
                // show slider hidden with opacity
                this.options.on = {
                    init: this.showSlider.bind(this)
                };
            }

            // Adjust slider size when slide is loaded lazy.
            if (this.options.lazy) {
                this.options.on = $.extend({}, this.options.on, {
                    lazyImageReady: function (slide) {
                        this.update();
                    }
                });
            }

            this.swiper = new Swiper(this.element, this.options);
        },

        /**
         * Show slider hidden with css opacity
         */
        showSlider: function () {
            this.element.css('opacity', 1);
        },

        /**
         * Get number of slides before init
         * @return {Number}
         */
        countSlides: function () {
            return $('.swiper-slide', this.element).length;
        },

        /**
         * Update / reinit slider
         */
        updateSlider: function () {
            this.swiper.update();
        }
    });

    return $.swissup.easyslide;
});
