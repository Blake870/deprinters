define([
    'jquery',
], function ($) {
    'use strict';

    return {
        bubble: function(event) {
            if (event == 'update') {
                var input = $('#gallery-value'),
                    images = $('#images div[data-role="image"]:not(".removed")'),
                    imagesObj,
                    imagesArr = [];

                images.each(function(index, item) {
                    imagesObj = {
                        'file': $(item).find('input[name$="[file]"]').val(),
                        'position': $(item).find('input[name$="[position]"]').val(),
                        'label': $(item).find('input[name$="[label]"]').val()
                    };

                    imagesArr.push($.param(imagesObj));
                });

                input.val(imagesArr.join(';'));
            }
        }
    }
});
