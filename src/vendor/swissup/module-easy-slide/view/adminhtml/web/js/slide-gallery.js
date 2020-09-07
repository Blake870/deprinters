define([
    'jquery',
    'mage/template',
    'baseImage',
    'productGallery'
], function ($) {
    'use strict';

    /**
     * Slide gallery widget
     */
    $.widget('swissup.slideGallery', $.mage.productGallery, {

        /**
        * Set image as main
        * @param {Object} imageData
        */
        setBase: function (imageData) {
            return false;
        },

        /**
         * Listener for dialog open
         * @param  {Event} event
         */
        onDialogOpen: function (event) {
            var imageData = this.$dialog.data('imageData');

            this._super(event);

            $('#slide-enabled').on('change', function (e) {
                var target = $(e.target),
                    targetName = target.attr('name'),
                    isActive = target.val();

                $('input[type="hidden"][name="' + targetName + '"]').val(isActive);
                imageData['is_active'] = isActive;
            });

            $('#slide-title').on('change', function (e) {
                var target = $(e.target),
                    targetName = target.attr('name'),
                    title = target.val();

                $('input[type="hidden"][name="' + targetName + '"]').val(title);
                imageData.title = title;
            });

            $('#slide-link').on('change', function (e) {
                var target = $(e.target),
                    targetName = target.attr('name'),
                    link = target.val();

                $('input[type="hidden"][name="' + targetName + '"]').val(link);
                imageData.link = link;
            });

            $('#slide-link-target').on('change', function (e) {
                var target = $(e.target),
                    targetName = target.attr('name'),
                    linkTarget = target.val();

                $('input[type="hidden"][name="' + targetName + '"]').val(linkTarget);
                imageData.target = linkTarget;
            });

            $('#slide-description').on('change', function (e) {
                var target = $(e.target),
                    targetName = target.attr('name'),
                    desc = target.val();

                $('input[type="hidden"][name="' + targetName + '"]').val(desc);
                imageData.description = desc;
            });

            $('#slide-desc-position').on('change', function (e) {
                var target = $(e.target),
                    targetName = target.attr('name'),
                    desc = target.val();

                $('input[type="hidden"][name="' + targetName + '"]').val(desc);
                imageData['desc_position'] = desc;
            });

            $('#slide-desc-background').on('change', function (e) {
                var target = $(e.target),
                    targetName = target.attr('name'),
                    desc = target.val();

                $('input[type="hidden"][name="' + targetName + '"]').val(desc);
                imageData['desc_position'] = desc;
            });

            $(event.target)
                .find('[data-role=type-selector]')
                .each($.proxy(function (index, checkbox) {
                    var $checkbox = $(checkbox),
                        parent = $checkbox.closest('.item'),
                        selectedClass = 'selected',
                        isChecked = this.options.types[$checkbox.val()].value == imageData.file;

                    $checkbox.prop(
                        'checked',
                        isChecked
                    );
                    parent.toggleClass(selectedClass, isChecked);
                }, this));
        },

        _initDialog: function () {
            var $dialog = $(this.dialogContainerTmpl());

            $dialog.modal({
                'type': 'slide',
                title: $.mage.__('Slide Detail'),
                buttons: [],
                opened: function () {
                    $dialog.trigger('open');
                },
                closed: function () {
                    $dialog.trigger('close');
                }
            });

            $dialog.on('open', this.onDialogOpen.bind(this));
            $dialog.on('close', function () {
                var $imageContainer = $dialog.data('imageContainer');

                $('#slide-enabled').trigger('change');
                $('#slide-title').trigger('change');
                $('#slide-link').trigger('change');
                $('#slide-link-target').trigger('change');
                $('#slide-description').trigger('change');
                $('#slide-desc-position').trigger('change');
                $('#slide-desc-background').trigger('change');
                $imageContainer.removeClass('active');
                $dialog.find('#hide-from-product-page').remove();
            });

            $dialog.on('change', '[data-role=type-selector]', function () {
                var parent = $(this).closest('.item'),
                    selectedClass = 'selected';

                parent.toggleClass(selectedClass, $(this).prop('checked'));
            });

            $dialog.on('change', '[data-role=type-selector]', $.proxy(this._notifyType, this));

            $dialog.on('change', '[data-role=visibility-trigger]', $.proxy(function (e) {
                var imageData = $dialog.data('imageData');

                this.element.trigger('updateVisibility', {
                    disabled: $(e.currentTarget).is(':checked'),
                    imageData: imageData
                });
            }, this));

            $dialog.on('change', '[data-role="image-description"]', function (e) {
                var target = $(e.target),
                    targetName = target.attr('name'),
                    desc = target.val(),
                    imageData = $dialog.data('imageData');

                this.element.find('input[type="hidden"][name="' + targetName + '"]').val(desc);

                imageData.label = desc;
                imageData.label_default = desc;

                this.element.trigger('updateImageTitle', {
                    imageData: imageData
                });
            }.bind(this));

            this.$dialog = $dialog;
        }
    });

    return $.swissup.slideGallery;
});
