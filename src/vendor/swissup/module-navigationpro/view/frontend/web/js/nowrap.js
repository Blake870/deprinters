define([
    'jquery',
    'Swissup_Navigationpro/js/rtl'
], function ($, rtl) {
    'use strict';

    /**
     * Prepare and return "More.." container
     *
     * @param  {Element} menu
     * @return {jQuery}
     */
    function getContainer(menu) {
        var container = $(menu).find('.navpro-item-more');

        if (container.length) {
            return container;
        }

        $(menu).append(
            '<li class="li-item level0 size-small level-top parent last caret-hidden navpro-item-more">' +
                '<a class="nav-a level-top nav-a-icon-more" href="#">' +
                    '<svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">' + //eslint-disable-line max-len
                        '<path d="M4,12 C5.1045695,12 6,11.1045695 6,10 C6,8.8954305 5.1045695,8 4,8 C2.8954305,8 2,8.8954305 2,10 C2,11.1045695 2.8954305,12 4,12 Z M10,12 C11.1045695,12 12,11.1045695 12,10 C12,8.8954305 11.1045695,8 10,8 C8.8954305,8 8,8.8954305 8,10 C8,11.1045695 8.8954305,12 10,12 Z M16,12 C17.1045695,12 18,11.1045695 18,10 C18,8.8954305 17.1045695,8 16,8 C14.8954305,8 14,8.8954305 14,10 C14,11.1045695 14.8954305,12 16,12 Z"></path>' + //eslint-disable-line max-len
                    '</svg>' +
                '</a>' +
                '<div class="navpro-dropdown navpro-dropdown-level1 size-small" data-level="0" role="menu">' +
                    '<div class="navpro-dropdown-inner">' +
                        '<div class="navpro-row gutters">' +
                            '<div class="navpro-col navpro-col-12">' +
                                '<ul class="children navpro-wrapped-items" data-columns="1"></ul>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<span class="navpro-shevron"></span>' +
                '</div>' +
            '</li>'
        );

        return $(menu).find('.navpro-item-more');
    }

    /**
     * Apply css class changes, when moving menu item
     *
     * @param  {Object} item
     * @param  {Number} increment
     */
    function changeLevel(item, increment) {
        var className = $(item).attr('class'),
            dataLevel = $(item).data('level'),
            level = className.match(/level(\d+)/);

        $(item).removeClass('level-top');

        className = className.replace(
            'level' + level[1],
            'level' + (parseInt(level[1], 10) + increment)
        );

        $(item).attr('class', className);

        if (dataLevel !== undefined) {
            $(item).data('level', parseInt(dataLevel, 10) + increment);
        }
    }

    /**
     * Apply CSS class changes, when moving item up
     *
     * @param {Object} item
     */
    function levelUp(item) {
        changeLevel(item, -1);
    }

    /**
     * Apply CSS class changes, when moving item down
     *
     * @param {Object} item
     */
    function levelDown(item) {
        changeLevel(item, 1);
    }

    /**
     * Move items into the "More.." dropdown
     * @param {Object} items
     */
    function hideItems(items) {
        var container = getContainer(items.parent());

        container.find('.navpro-wrapped-items').prepend(items);

        items.find('> a').removeClass('level-top');
        items.each(function () {
            levelDown(this);
        });
        items.find('.navpro-dropdown').each(function () {
            levelDown(this);
        });
    }

    /**
     * Move items out from the "More.." dropdown
     * @param {Object} items
     */
    function showItems(items) {
        var menu = items.parents('.navpro-menu');

        getContainer(menu).before(items);

        items.find('> a').addClass('level-top');
        items.each(function () {
            levelUp(this);
        });
        items.find('.navpro-dropdown').each(function () {
            levelUp(this);
        });
    }

    /**
     * Move items into "More.." submenu
     *
     * @param {Element} menu
     */
    function build(menu) {
        var itemsToMove = $(),
            container = getContainer(menu),
            windowWidth = $(window).width(),
            offset = menu.className.match(/navpro-nowrap-offset-(\d+)/),
            liWidth = 0,
            moreItemWidth = 0,
            stopProcessing = false,
            left, right, isFullyVisible, firstItem;

        offset = offset ? offset[1] : 0;

        container.css({
            display: 'none'
        });
        showItems(container.find('.navpro-wrapped-items > li'));
        $(menu).removeClass('navpro-nowrap-ready')
            .data('navpro-width', $(menu).outerWidth());

        firstItem = $(menu).children('li').first();

        if (firstItem.get(0).offsetLeft < 0 ||
            firstItem.get(0).offsetLeft + firstItem.outerWidth() > windowWidth) {

            $(menu).addClass('navpro-nowrap-justify-start');
        }

        right = Math.round(Math.min(
            $(menu).offset().left + $(menu).outerWidth(),
            windowWidth
        ));

        $(menu).children('li').not('.navpro-item-more').each(function () {
            liWidth += parseInt($(this).width(), 10);
        });

        if ($(menu).width() - liWidth <= 20) {
            right -= offset;
        }

        itemsToMove = $($(menu).children('li').get().reverse()).filter(function (i, el) {
            // don't create nowrap, when menu has mobile view
            if (left === $(el).offset().left) {
                stopProcessing = true;
            }
            left = $(el).offset().left;

            if (rtl.isRtl()) {
                isFullyVisible = Math.round(left - moreItemWidth) > 0;
            } else {
                isFullyVisible = Math.round(left + $(el).outerWidth() + moreItemWidth) <= right;
            }

            if (isFullyVisible) {
                return false;
            }

            // do not move "More..." item. Move previous item instead.
            if ($(el).hasClass('navpro-item-more')) {
                return false;
            }

            moreItemWidth = 60;

            return true;
        });

        $(menu).addClass('navpro-nowrap-ready');
        $(menu).removeClass('navpro-nowrap-justify-start');

        if (stopProcessing || !itemsToMove.length) {
            return;
        }

        container.css({
            display: ''
        });

        hideItems($(itemsToMove.get().reverse()));
    }

    /**
     * @param  {Element}  menu
     * @return {Boolean}
     */
    function isEnabled(menu) {
        return $(menu).hasClass('navpro-nowrap') &&
            !$(menu).parent('.navpro').hasClass('orientation-vertical');
    }

    /**
     * init nowrap feature
     */
    function start() {
        $('.navpro-menu').each(function () {
            if (!isEnabled(this)) {
                return $(this).removeClass('navpro-nowrap');
            }
            build(this);
        });
    }
    start();

    $('body').on('navpro:windowResize', function () {
        start();
    });

    return {

        /**
         * Init nowrap feature
         *
         * @param {Element} menu - Element to init nowrap feature
         */
        init: function (menu) {
            if (!isEnabled(menu)) {
                return;
            }

            getContainer(menu);
        }
    };
});
