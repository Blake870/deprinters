define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    var placeholders = {
            'img': '<div class="navpro-content-html-entity navpro-content-image"><svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill-rule="evenodd"><path d="M11,13 L8,10 L2,16 L11,16 L18,16 L13,11 L11,13 Z M0,3.99406028 C0,2.8927712 0.898212381,2 1.99079514,2 L18.0092049,2 C19.1086907,2 20,2.89451376 20,3.99406028 L20,16.0059397 C20,17.1072288 19.1017876,18 18.0092049,18 L1.99079514,18 C0.891309342,18 0,17.1054862 0,16.0059397 L0,3.99406028 Z M15,9 C16.1045695,9 17,8.1045695 17,7 C17,5.8954305 16.1045695,5 15,5 C13.8954305,5 13,5.8954305 13,7 C13,8.1045695 13.8954305,9 15,9 Z"></path></g></svg></div>', // eslint-disable-line max-len
            'iframe': '<div class="navpro-content-html-entity navpro-content-iframe"><svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill-rule="evenodd"><path d="M9,10 L7,10 L7,12 L9,12 L9,14 L11,14 L11,12 L13,12 L13,10 L11,10 L11,8 L9,8 L9,10 Z M0,2.99508929 C0,1.8932319 0.898212381,1 1.99079514,1 L18.0092049,1 C19.1086907,1 20,1.8926228 20,2.99508929 L20,17.0049107 C20,18.1067681 19.1017876,19 18.0092049,19 L1.99079514,19 C0.891309342,19 0,18.1073772 0,17.0049107 L0,2.99508929 Z M2,5 L18,5 L18,17 L2,17 L2,5 Z"></path></g></svg></div>', // eslint-disable-line max-len
            'video': '<div class="navpro-content-html-entity navpro-content-video"><svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill-rule="evenodd"><path d="M0,3.99406028 C0,2.8927712 0.898212381,2 1.99079514,2 L18.0092049,2 C19.1086907,2 20,2.89451376 20,3.99406028 L20,16.0059397 C20,17.1072288 19.1017876,18 18.0092049,18 L1.99079514,18 C0.891309342,18 0,17.1054862 0,16.0059397 L0,3.99406028 Z M6,4 L14,4 L14,16 L6,16 L6,4 Z M2,5 L4,5 L4,7 L2,7 L2,5 Z M2,9 L4,9 L4,11 L2,11 L2,9 Z M2,13 L4,13 L4,15 L2,15 L2,13 Z M16,5 L18,5 L18,7 L16,7 L16,5 Z M16,9 L18,9 L18,11 L16,11 L16,9 Z M16,13 L18,13 L18,15 L16,15 L16,13 Z M8,7 L13,10 L8,13 L8,7 Z"></path></g></svg></div>', // eslint-disable-line max-len
            'audio': '<div class="navpro-content-html-entity navpro-content-audio"><svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill-rule="evenodd"><path d="M5,7 L1,7 L1,13 L5,13 L10,18 L10,2 L5,7 Z M16.363961,16.363961 C17.9926407,14.7352814 19,12.4852814 19,10 C19,7.51471863 17.9926407,5.26471863 16.363961,3.63603897 L14.9497475,5.05025253 C16.2164983,6.31700338 17,8.06700338 17,10 C17,11.9329966 16.2164983,13.6829966 14.9497475,14.9497475 L16.363961,16.363961 L16.363961,16.363961 Z M13.5355339,13.5355339 C14.4403559,12.6307119 15,11.3807119 15,10 C15,8.61928813 14.4403559,7.36928813 13.5355339,6.46446609 L12.1213203,7.87867966 C12.6642136,8.42157288 13,9.17157288 13,10 C13,10.8284271 12.6642136,11.5784271 12.1213203,12.1213203 L13.5355339,13.5355339 L13.5355339,13.5355339 Z"></path></g></svg></div>', // eslint-disable-line max-len
            'widget': '<div class="navpro-content-html-entity navpro-content-widget"><svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill-rule="evenodd"><path d="M20,14 L20,18.0059397 C20,19.1072288 19.1054862,20 18.0059397,20 L14,20 L14,17.9981014 C14,16.8867064 13.1045695,16 12,16 C10.8877296,16 10,16.8945804 10,17.9981014 L10,20 L5.99406028,20 C4.8927712,20 4,19.1054862 4,18.0059397 L4,14 L1.99810135,14 C0.894580447,14 0,13.1122704 0,12 C0,10.8954305 0.886706352,10 1.99810135,10 L4,10 L4,5.99406028 C4,4.8927712 4.89451376,4 5.99406028,4 L10,4 L10,1.99810135 C10,0.894580447 10.8877296,0 12,0 C13.1045695,0 14,0.886706352 14,1.99810135 L14,4 L18.0059397,4 C19.1072288,4 20,4.89451376 20,5.99406028 L20,10 L17.9981014,10 C16.8867064,10 16,10.8954305 16,12 C16,13.1122704 16.8945804,14 17.9981014,14 L20,14 Z"></path></g></svg></div>', // eslint-disable-line max-len
            'p': '<p class="navpro-content-html-entity navpro-content-paragraph"></p>',
            'span': '<span class="navpro-content-html-entity navpro-content-span"></span>',
            'a': '<span class="navpro-content-html-entity navpro-content-link"></span>',
            'button': '<span class="navpro-content-html-entity navpro-content-button"></span>'
        },
        mapping = {
            'img': placeholders.img,
            'iframe': placeholders.iframe,
            'video': placeholders.video,
            'audio': placeholders.audio,
            'p': placeholders.p,
            'span': placeholders.span,
            'a': placeholders.a,
            'button': placeholders.button
        };

    return function (item) {
        var cleanContent = item.content.replace(' srcset=', ' data-srcset='), // prevent errors in chrome console
            html = $('<div class="navpro-content-html-wrapper">' + cleanContent + '</div>');

        html.find('[style], [class], [id]').removeAttr('style class id');

        _.each(mapping, function (replacement, selector) {
            html.find(selector).replaceWith(replacement);
        });

        html.find(':not(div,p,span,svg,g,path)').remove();

        html.find('*').contents()
            .add(html.contents())
            .filter(function () {
                return this.nodeType === 3 &&
                    $(this).text().trim().length &&
                    $(this).text().indexOf('{{widget') > -1;
            })
            .replaceWith(placeholders.widget);

        html.find('*').contents()
            .add(html.contents())
            .filter(function () {
                return this.nodeType === 3 &&
                    $(this).text().trim().length;
            })
            .replaceWith(placeholders.p);

        return {
            /**
             * @return {String}
             */
            render: function () {
                return html.html();
            },

            /**
             * @param {jQuery} container
             */
            afterRender: function (container) {
                //
            }
        };
    };
});
