var config = {
    config: {
        mixins: {
            'mage/collapsible': {
                'js/mixin/collapsible/stop-tab-scroll-into-viewport': true
            },
            'Magento_Catalog/js/related-products': {
                'js/mixin/catalog/related-products-correct-scope': true
            }
        }
    },
    shim: {
        'js/lib/jquery.visible': ['jquery'],
        'js/lib/sticky-kit': ['jquery']
    },
    map: {
        '*': {
            'argentoTabs': 'js/argento-tabs',
            'argentoSticky': 'js/argento-sticky',
            'jquery/visible': 'js/lib/jquery.visible'
        }
    },
    deps: [
        'js/argento-base',
        'js/argento-theme',
        'js/argento-custom'
    ]
};
