define([
    'jquery',
    'underscore',
    'matchMedia',
    'mage/utils/wrapper',
    'Swissup_Navigationpro/js/aim',
    'Swissup_Navigationpro/js/touch',
    'Swissup_Navigationpro/js/rtl',
    'Swissup_Navigationpro/js/overlay',
    'Swissup_Navigationpro/js/sticky',
    'Swissup_Navigationpro/js/make-vertical-columns',
    'mage/menu'
], function ($, _, matchMedia, wrapper, aim, touch, rtl, overlay, sticky, makeVerticalColumns) {
    'use strict';

    $(window).on('resize', _.debounce(function () {
        $('body').trigger('navpro:windowResize');
    }, 100));

    $.widget('swissup.navpro', $.mage.menu, {
        options: {
            icons: {
                submenu: 'navpro-icon-caret'
            },
            menus: '.navpro-dropdown',
            responsive: true,
            expanded: true,
            delay: 300,
            position: {
                my: 'left top',
                at: 'right top',
                collision: 'flipfit fit'
            },
            level0: {
                position: {
                    my: 'center top',
                    at: 'center bottom',
                    collision: 'fit none'
                }
            }
        },

        /**
         * Init navigation
         * @private
         */
        _create: function () {
            sticky.init(this.element);

            this._super();

            // fit dropdown dimensions into screen size
            this._updateDimensions();
            $(window).on('resize', _.debounce(this._updateDimensions.bind(this), 100));

            if (!this._isAccordion()) {
                this._on(this.document, {
                    /**
                     * Close menu when click outside of menu. Fix for Apple devices.
                     * @param  {Event} event
                     */
                    vclick: function (event) {
                        if (!$(event.target).closest('.ui-menu-item').length) {
                            this.collapseAll(event);
                        }
                    }
                });
            }

            if (!this._isAccordion() && !$(this.element).hasClass('navpro-click')) {
                matchMedia({
                    media: '(max-width: ' + this._getMobileMaxWidth() + 'px)',
                    entry: this._toggleClickMode.bind(this),
                    exit: this._toggleHoverMode.bind(this)
                });
            } else {
                this._toggleClickMode();
            }

            // Magento 2.2.4 breadcrumbs compatibility
            $('[data-action="navigation"] > ul').data('mageMenu', false);
        },

        /**
         * Don't collapse accordion, when click outside of menu.
         * @see jquery/jquery-ui.js:11553
         */
        _on: function () {
            var self = this;

            if (arguments[0] === this.document && arguments[1] && arguments[1].click) {
                arguments[1].click = wrapper.wrap(
                    arguments[1].click,
                    function (o, e) {
                        if (self._isAccordion()) {
                            return;
                        }
                        o(e);
                    }
                );
            }

            return this._super.apply(this, arguments);
        },

        /**
         * @private
         */
        _listen: function () {
            // add toggle navigation listeners for mobile view
            // @todo: call parent method once only
            if (this.element.closest('.nav-sections').length) {
                this._super();
            }
        },

        /**
         * Toggle.
         */
        toggle: function () {
            var html = $('html');

            if (html.hasClass('nav-open')) {
                overlay.hide(this.element);
            } else {
                overlay.show(this.element);
            }

            this._super();
        },

        /**
         * Used for:
         *  Height calculation for vertical columns mode
         *  Dropdown positioning
         * @return {Boolean}
         */
        _shouldUseMobileFriendlyFallbacks: function () {
            return this._isAccordion() ||
                window.innerWidth <= this._getMobileMaxWidth() && this._isTransformable();
        },

        /**
         * Get max width for mobile view
         * @return {Number}
         */
        _getMobileMaxWidth: function () {
            return $(this.element).next('.navpro-mobile').width();
        },

        /**
         * Checks if menu orientation is vertical
         * @returns {bool}
         * @private
         */
        _isOrientationVertical: function () {
            return $(this.element).parent('.navpro').hasClass('orientation-vertical');
        },

        /**
         * Checks if menu is accordion
         * @returns {bool}
         * @private
         */
        _isAccordion: function () {
            return $(this.element).parent('.navpro').hasClass('navpro-accordion');
        },

        /**
         * Checks if menu is tranformable into accordion
         * @returns {bool}
         * @private
         */
        _isTransformable: function () {
            return $(this.element).parent('.navpro').hasClass('navpro-transformable');
        },

        /**
         * Add/remove 'shown' class on a dropdown Element when it does not have
         * display:none style.
         *
         * If display:none is found on a dropdown element, this method will
         * not stop an event. (xs-hide-dropdown compatibility)
         *
         * @param  {jQuery} dropdown
         * @param  {Event} event
         * @param  {Boolean} toggleMode
         */
        _toggleDropdownVisibility: function (dropdown, event, toggleMode) {
            var target = $(event.target).closest('.ui-menu-item'),
                currentTarget;

            if (dropdown.css('display') === 'none') {
                return;
            }

            // copy of mouseenter handler
            currentTarget = $(event.currentTarget);

            if (!this._isAccordion()) {
                currentTarget.siblings('.ui-menu-item.opened').removeClass('opened');
            }
            currentTarget.siblings().children('.ui-state-active').removeClass('ui-state-active');

            if (!currentTarget.children('.shown').length) {
                this.focus(event, currentTarget);
            }
            // end of copy

            if (!dropdown.hasClass('shown')) {
                event.preventDefault();
                this._open(dropdown);
            } else if (toggleMode) {
                event.preventDefault();
                this._close(currentTarget);
            } else if (!target.find('> a').length ||
                target.find('> a').attr('href') === '#') {

                this._close();
            }
        },

        /**
         * Overriden to prevent multiple mouseenter and mouseleave events
         */
        _toggleClickMode: function () {
            $(this.element).off('mouseleave mouseenter click focus blur');
            this._on({
                /**
                 * @param {Event} event
                 */
                'click .ui-state-disabled > a': function (event) {
                    event.preventDefault();
                },

                /**
                 * Prevent all events when clicking custom content inside dropdown
                 */
                'click .navpro-dropdown': function () {
                    // event.stopPropagation();
                },

                /**
                 * @param {Event} event
                 */
                'click .ui-menu-item:has(.navpro-dropdown)': function (event) {
                    var target = $(event.target).closest('.ui-menu-item'),
                        dropdown = target.children('.ui-menu');

                    if (target.get(0) !== event.currentTarget) {
                        return;
                    }

                    if (!dropdown.length) {
                        return;
                    }

                    // don't do anything if event.target is inside a dropdown
                    if ($.contains(dropdown.get(0), event.target)) {
                        return;
                    }

                    this._toggleDropdownVisibility(
                        dropdown,
                        event,
                        $(event.target).closest('.navpro-icon-caret').length > 0
                    );
                }
            });
        },

        /**
         * Overriden to prevent multiple mouseenter and mouseleave events
         */
        _toggleHoverMode: function () {
            $(this.element).off('mouseleave mouseenter click focus blur');
            this._on({

                /**
                 * @param {Event} event
                 */
                'click .ui-state-disabled > a': function (event) {
                    event.preventDefault();
                },

                /**
                 * @param {Event} event
                 */
                'click a.nav-a[href="#"]': function (event) {
                    event.preventDefault();
                },

                /**
                 * Prevent all events when clicking custom content inside dropdown
                 */
                'click .navpro-dropdown': function () {
                    // event.stopPropagation();
                },

                /**
                 * @param {Event} event
                 */
                'click .ui-menu-item:has(.navpro-dropdown)': function (event) {
                    var target = $(event.target).closest('.ui-menu-item'),
                        dropdown = target.children('.ui-menu');

                    if (target.get(0) !== event.currentTarget) {
                        return;
                    }

                    if (!dropdown.length) {
                        return;
                    }

                    // don't do anything if event.target is inside a dropdown
                    if ($.contains(dropdown.get(0), event.target)) {
                        return;
                    }

                    if (touch.touching()) {
                        this._toggleDropdownVisibility(
                            dropdown,
                            event,
                            $(event.target).closest('.navpro-icon-caret').length > 0
                        );
                    }
                },

                /**
                 * @param {Event} event
                 */
                'mouseenter .ui-menu-item': function (event) {
                    var target = $(event.currentTarget);

                    // Remove ui-state-active class from siblings of the newly focused menu item
                    // to avoid a jump caused by adjacent elements both having a class with a border
                    target.siblings().children('.ui-state-active').removeClass('ui-state-active');

                    // `if` is added to fix non-clickable items in first dropdown on ipad
                    if (!touch.touching() || $(event.target).closest('.ui-menu-item').children('.ui-menu').length) {
                        this.focus(event, target);
                    }
                },

                /**
                 * @param {Event} event
                 */
                'mouseleave': function (event) {
                    this.collapseAll(event, true);
                },

                /**
                 * @param {Event} event
                 * @returns {*}
                 */
                'mouseleave .ui-menu': this.collapseAll,

                /**
                 * @param {Event} event
                 */
                'mouseleave .ui-menu-item': function (event) {
                    var currentItem;

                    // close dropdown when mouseout to custom content
                    clearTimeout(this.timer);

                    currentItem = $(event.target).closest('.ui-menu-item');

                    this.timer = this._delay(function () {
                        this._close(currentItem);
                    }, this.delay);
                }
            });
        },

        /**
         * Overriden to prevent multiple mouseenter and mouseleave events
         */
        _toggleMobileMode: function () {
            // disable magento listeners
        },

        /**
         * Overriden to prevent multiple mouseenter and mouseleave events
         */
        _toggleDesktopMode: function () {
            // disable magento listeners
        },

        /**
         * Fit all submenus into viewport width
         */
        _updateDimensions: function () {
            var width = 1280,
                tmpWidth,
                dropdowns,
                parent;

            // prevent horizontal scrollbar
            $(this.element).find('.navpro-dropdown').css({
                left: '',
                top: ''
            });

            $('body').trigger('navpro:updateDimensionsBefore', this.element);

            // Sync dropdown width's with theme container width and viewport size
            $('#maincontent, .header.content').each(function () {
                var currentWidth = $(this).outerWidth();

                if (currentWidth && currentWidth > 300 && currentWidth < width) {
                    width = currentWidth;
                }
            });
            dropdowns = [
                '.navpro-dropdown.size-fullwidth > .navpro-dropdown-inner',
                '.navpro-dropdown.size-boxed > .navpro-dropdown-inner',
                '.navpro-dropdown.size-fullscreen',
                '.navpro-dropdown.size-xlarge',
                '.navpro-dropdown.size-large',
                '.navpro-dropdown.size-medium'
            ];
            $(dropdowns.join(','), this.element).each(function (i, el) {
                // 1. restore max dropdown size
                tmpWidth = width;

                if ($(el).hasClass('size-fullscreen')) {
                    tmpWidth = '';
                }

                $(el).css({
                    'max-width': tmpWidth
                });

                // 2. shrink it, to fit the viewport
                this._fitSubmenuWidth($(el));
            }.bind(this));

            // split vertical list into columns
            this._splitVerticalColumns();

            // amazon menu
            if ($(this.element).hasClass('navpro-amazon')) {
                $('.navpro-departments .navpro-dropdown-level2', this.element)
                    .not('.navpro-dropdown-expanded')
                    .each(function () {
                        parent = $(this).parent().closest('.navpro-dropdown');
                        $(this).css({
                            'min-height': parent.outerHeight()
                        });
                    });
            }

            // stacked menu
            if ($(this.element).hasClass('navpro-stacked')) {
                $('.navpro-dropdown', this.element)
                    .not('.navpro-dropdown-expanded')
                    .each(function () {
                        parent = $(this).parent().closest('.navpro-dropdown');
                        $(this).css({
                            'min-height': parent.outerHeight()
                        });
                    });
            }

            $('body').trigger('navpro:updateDimensionsAfter', this.element);
        },

        /**
         * Prepare vertical multicolumn dropdowns
         */
        _splitVerticalColumns: function () {
            var self = this;

            $('.vertical', this.element).each(function () {
                if (self._shouldUseMobileFriendlyFallbacks()) {
                    // use single column mode
                    $(this).height('auto');

                    return;
                }

                makeVerticalColumns(this, $(this).data('columns'));
            });
        },

        /**
         * Fit submenu width to stay inside viewport
         * @param  {jQuery} submenu
         */
        _fitSubmenuWidth: function (submenu) {
            var viewportWidth = $(window).width(),
                submenuWidth = submenu.width(),
                cssLeft = isNaN(parseInt(submenu.css('left'), 10)) ? 0 : parseInt(submenu.css('left'), 10),
                cssRight = isNaN(parseInt(submenu.css('right'), 10)) ? 0 : parseInt(submenu.css('right'), 10),
                left = Math.max(submenu.offset().left, cssLeft),
                right, overlap, parentWidth;

            if (rtl.isRtl()) {
                // cssRight may be grater that 0, when special css is applied (Amazon)
                left = cssRight <= 0 ? submenuWidth : cssRight + submenuWidth;
                overlap = left - viewportWidth;
            } else {
                // cssLeft may be grater that 0, when special css is applied (Amazon)
                right = cssLeft <= 0 ? submenuWidth : left + submenuWidth;
                overlap = right - viewportWidth;
            }

            // Additional logic for items inside "More.." dropdown
            if (submenu.parents('.navpro-wrapped-items').length) {
                parentWidth = 0;

                submenu.parents('.li-item')
                    .parents('.navpro-dropdown')
                    .each(function () {
                        parentWidth += $(this).outerWidth();
                    });

                if (submenuWidth + parentWidth > viewportWidth) {
                    overlap += parentWidth;
                }
            }

            if (overlap > 0) {
                if (overlap >= submenuWidth) {
                    overlap = submenuWidth / 2;
                }
                submenu.css({
                    'max-width': submenuWidth - overlap - 1
                });
            }
        },

        /**
         * Overridden to add dynamic delay value calculation
         * @param  {jQuery} submenu
         */
        _startOpening: function (submenu) {
            var delay = 0;

            clearTimeout(this.timer);

            // Don't open if already open fixes a Firefox bug that caused a .5 pixel
            // shift in the submenu position when mousing over the carat icon
            if (submenu.attr('aria-hidden') !== 'true') {
                return;
            }

            if (!touch.touching()) {
                delay = aim.getActivationDelay(
                    submenu,
                    submenu.parents(this.options.menus + ',ul.ui-menu').first(),
                    this._getSubmenuPosition(submenu)
                );
            }

            if (delay === false) {
                delay = this.options.delay;
            }

            this.timer = this._delay(function () {
                this._close();
                this._open(submenu);
            }, delay);
        },

        /**
         * Recalculate position according to possible 'translate' effects.
         *
         * @param {Object} position
         * @param {Object} feedback
         */
        _uiPosition: function (position, feedback) {
            var el = feedback.element.element,
                transform = el.hasClass('navpro-shevron') ?
                    el.closest('.navpro-dropdown').css('transform') : el.css('transform'),
                values = transform.match(/-?\d+\.?\d+|\d+/g),
                translateX,
                translateY;

            if (values && values.length === 6) {
                translateX = parseInt(values[4], 10);
                translateY = parseInt(values[5], 10);

                if (translateY) {
                    position.top += translateY;
                } else if (translateX) {
                    position.left += translateX;
                }
            }

            position.top = Math.round(position.top);
            position.left = Math.round(position.left);

            el.css(position);
        },

        /**
         * Get submenu positioning properties
         * @param  {jQuery} submenu
         * @return {Object}
         */
        _getSubmenuPosition: function (submenu) {
            var within, width, constraints,
                level = parseInt(submenu.data('level'), 10),
                parentLi = submenu.closest('li'),
                position = $.extend({
                    of: this.active,
                    using: this._uiPosition
                }, this.options.position);

            if (this.options['level' + level] &&
                this.options['level' + level].position) {

                within = this.element;
                width = $(this.element).outerWidth();

                constraints = [
                    '.header.content',
                    '.column.main',
                    '.page-main',
                    '.footer.content',
                    '.container'
                ];

                if (this._isOrientationVertical()) {
                    constraints.push('.page-wrapper');
                }

                // Constrain dropdown inside parent edges
                $(this.element)
                    .closest(constraints.join(','))
                    .each(function () {
                        var currentWidth = $(this).outerWidth();

                        if (currentWidth && currentWidth > width) {
                            within = this;
                        }
                    });

                position = $.extend(
                    {
                        within: within
                    },
                    position,
                    this.options['level' + level].position
                );
            }

            // manual dropdown positioning
            if (submenu.hasClass('navpro-stick-left') || parentLi.hasClass('navpro-stick-left')) {
                if (!this._isOrientationVertical() && level === 0) {
                    position = this._switchPosition(position, 'right', 'left');
                    position = this._switchPosition(position, 'center', 'left');
                } else {
                    position.my = position.my.replace('right ', 'left ');
                    position.at = position.at.replace('left ', 'right ');
                }
            } else if (submenu.hasClass('navpro-stick-right') || parentLi.hasClass('navpro-stick-right')) {
                if (!this._isOrientationVertical() && level === 0) {
                    position = this._switchPosition(position, 'left', 'right');
                    position = this._switchPosition(position, 'center', 'right');
                } else {
                    position.my = position.my.replace('left ', 'right ');
                    position.at = position.at.replace('right ', 'left ');
                }
            } else if (submenu.hasClass('navpro-stick-center') || parentLi.hasClass('navpro-stick-center')) {
                if (!this._isOrientationVertical() && parentLi.hasClass('level0')) {
                    position = this._switchPosition(position, 'left', 'center');
                    position = this._switchPosition(position, 'right', 'center');
                }
            }

            // RTL support
            if (rtl.isRtl()) {
                if (position.my.indexOf('left ') === 0) {
                    position.my = position.my.replace('left ', 'right ');
                } else {
                    position.my = position.my.replace('right ', 'left ');
                }

                if (position.at.indexOf('left ') === 0) {
                    position.at = position.at.replace('left ', 'right ');
                } else {
                    position.at = position.at.replace('right ', 'left ');
                }
            }

            return position;
        },

        /**
         * @param  {Object} position
         * @param  {String} from
         * @param  {String} to
         * @return {Object}
         */
        _switchPosition: function (position, from, to) {
            from += ' ';
            to += ' ';

            position.my = position.my.replace(from, to);
            position.at = position.at.replace(from, to);

            return position;
        },

        /**
         * @param {Object} event
         * @param {Boolean} all
         */
        collapseAll: function (event, all) {
            if (this._shouldUseMobileFriendlyFallbacks()) {
                return;
            }

            this._super(event, all);
        },

        /**
         * Overridden for:
         *  1. Add ability to use different position per level
         *  2. Remove `show` and `hide` methods in favor of classNames
         *
         * @param {jQuery} submenu - jQuery object with element
         */
        _open: function (submenu) {
            clearTimeout(this.timer);

            if (!this._shouldUseMobileFriendlyFallbacks()) {
                this.element.find('.ui-menu').not(submenu.parents('.ui-menu'))
                    .removeClass('shown')
                    .attr('aria-hidden', 'true');
            }

            if (!this._shouldUseMobileFriendlyFallbacks()) {
                submenu.position(this._getSubmenuPosition(submenu));
                submenu.find('> .navpro-shevron').first().position({
                    my: 'center top',
                    at: 'center bottom',
                    of: this.active,
                    using: this._uiPosition
                });
            }

            this.active
                .addClass('opened')
                .closest('.navpro-dropdown, .navpro')
                .addClass('shown-child');
            submenu
                .addClass('shown')
                .removeAttr('aria-hidden')
                .attr('aria-expanded', 'true');

            if (this.active.hasClass('navpro-overlay') ||
                this.element.hasClass('navpro-overlay')) {

                overlay.show(this.element);
            }
        },

        /**
         * Overridden to remove `show` and `hide` methods in favor of classNames
         * @param  {jQuery} startMenu
         */
        _close: function (startMenu) {
            if (!startMenu) {
                startMenu = this.active ? this.active.parent() : this.element;
            }

            startMenu
                .find('.ui-menu')
                    .removeClass('shown')
                    .attr('aria-hidden', 'true')
                    .attr('aria-expanded', 'false')
                .end()
                .find('a.ui-state-active')
                    .removeClass('ui-state-active')
                .end()
                .find('li.ui-menu-item.opened').addBack('li.ui-menu-item.opened')
                    .removeClass('opened')
                    .closest('.navpro-dropdown, .navpro')
                        .removeClass('shown-child');

            if (!this.element.find('li.opened.navpro-overlay').length &&
                !this.element.hasClass('navpro-overlay') ||
                this.element === startMenu) {

                overlay.hide(this.element);
            }
        },

        /**
         * Overridden to fix the following issues:
         *
         *   1. Do not add `ui-menu-item` class to the `navpro-dropdown-inner`
         *      Fixed with more precise selector: **li**:not(.ui-menu-item):has(a)
         *
         *   2. Add `ui-menu-item` class to the each li of `ul.children` element
         *      Fixed with `.add(menus.find('ul.children')...` logic
         */
        refresh: function () {
            var menus,
                icon = this.options.icons.submenu,
                submenus = this.element.find(this.options.menus),
                item, menu, submenuCarat;

            this.element.toggleClass('ui-menu-icons', !!this.element.find('.ui-icon').length);

            // Initialize nested menus
            submenus.filter(':not(.ui-menu)')
                .addClass('ui-menu ui-widget ui-widget-content ui-corner-all')
                .removeClass('shown')
                .attr({
                    role: this.options.role,
                    'aria-hidden': 'true',
                    'aria-expanded': 'false'
                })
                .each(function () {
                    menu = $(this);
                    item = menu.prev('a');
                    submenuCarat = $('<span>')
                        .addClass('ui-menu-icon ui-icon ' + icon)
                        .data('ui-menu-submenu-carat', true);

                    item
                        .attr('aria-haspopup', 'true')
                        .prepend(submenuCarat);
                    menu.attr('aria-labelledby', item.attr('id'));
                });

            menus = submenus.add(this.element);

            // Don't refresh list items that are already adapted
            menus.children('li:not(.ui-menu-item):has(a)')
                .add(menus.find('ul.children').children('li:not(.ui-menu-item):has(a)'))
                .addClass('ui-menu-item')
                .attr('role', 'presentation')
                .children('a')
                    .uniqueId()
                    .addClass('ui-corner-all')
                    .attr({
                        tabIndex: -1,
                        role: this._itemRole()
                    });

            // Initialize unlinked menu-items containing spaces and/or dashes only as dividers
            menus.children('li:not(.ui-menu-item)')
                .add(menus.find('ul.children').children('li:not(.ui-menu-item)'))
                .each(function () {
                    item = $(this);
                    // hyphen, em dash, en dash
                    if (!/[^\-\u2014\u2013\s]/.test(item.text())) {
                        item.addClass('ui-widget-content ui-menu-divider');
                    }
                });

            // Add aria-disabled attribute to any disabled menu item
            menus.children('.ui-state-disabled')
                .add(menus.find('ul.children').children('.ui-state-disabled'))
                .attr('aria-disabled', 'true');

            // If the active item has been removed, blur the menu
            if (this.active && !$.contains(this.element[0], this.active[0])) {
                this.blur();
            }
        },

        /**
         * Prevent inherited logic, because it cause error
         */
        _keydown: function () {
            // Prevent inherited logic, because it cause error
            // for complex dropdowns with inner forms
        }
    });

    return $.swissup.navpro;
});
