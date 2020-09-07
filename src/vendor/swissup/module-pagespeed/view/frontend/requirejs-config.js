var config = {
    shim: {
        'Swissup_Pagespeed/js/lib/loadCSS': {
            exports: 'loadCSS'
        },
        'Swissup_Pagespeed/js/lib/cssrelpreload': {
            deps: [
                'Swissup_Pagespeed/js/lib/loadCSS'
            ]
        }
    },
    deps: [
        'Swissup_Pagespeed/js/lib/cssrelpreload',
        'Swissup_Pagespeed/js/lib/lazysizes.min',
        'Swissup_Pagespeed/js/lib/respimage.min',
        'jquery/jquery.cookie'
    ]
};
