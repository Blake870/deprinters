var config = {
    map: {
        '*': {
            'highlightCarousel': 'Swissup_Highlight/js/carousel'
        }
    },
    config: {
        mixins: {
            // Fixed multiple intializations after ajax request
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Swissup_Highlight/js/mixin/catalog-add-to-cart': true
            }
        }
    }
};
