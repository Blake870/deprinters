define([
    'jquery',
    'Magento_Ui/js/lib/view/utils/async',
    'slick'
], function ($) {
    'use strict';

    // initialize slick carousel for '.product-items' when it is wrapped in '.argento-init-slick'
    $.async(
        {
            selector: '.argento-init-slick .product-items'
        },
        function (productList) {
            $(productList).slick(
                {
                    'rtl': $(document.body).hasClass('rtl'),
                    'slidesToShow': 5,
                    'slidesToScroll': 5,
                    'dots': false,
                    'responsive': [
                        {
                            'breakpoint': 1024,
                            'settings': {
                                'slidesToShow': 4,
                                'slidesToScroll': 4
                            }
                        },
                        {
                            'breakpoint': 600,
                            'settings': {
                                'slidesToShow': 3,
                                'slidesToScroll': 3
                            }
                        },
                        {
                            'breakpoint': 480,
                            'settings': {
                                'slidesToShow': 2,
                                'slidesToScroll': 2
                            }
                        },
                        {
                            'breakpoint': 376,
                            'settings': {
                                'slidesToShow': 1,
                                'slidesToScroll': 1
                            }
                        }
                    ],
                    'autoplay': false
                }
            );
        }
    );
});
