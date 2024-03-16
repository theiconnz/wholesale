/**
 * Theiconnz
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
define([
    'Magento_Ui/js/modal/alert',
    "jquery",
    "jquery/ui",
    'mage/validation'
], function (alert, $) {
    "use strict";

    $.widget(
        'Theiconnz.wholesale', {
        options: {
            validationURL: '',
            formelement: "#wholesale_form",
            successblock: "#wholesale_form",
            formcontainer: ".wholesale-data-form",
            formid: "wholesale_form",
            hideonsuccess: ".success_hide",
        },

        /** @inheritdoc */
        _create: function () {
            this._on(this.element, {
                'submit': this.onSubmit
            });
        },
        /**
         * Validates requested form.
         *
         * @return {Boolean}
         */
        isValid: function () {
            return this.element.validation() && this.element.validation('isValid');
        },
        /**
         * Validates updated shopping cart data.
         *
         * @param {String} url - request url
         * @param {Object} data - post data for ajax call
         */
        validateItems: function (url, data) {
            $.extend(data, {
                'form_key': $.mage.cookies.get('form_key'),
            });

            var form = $(this.options.formelement);
            var formdata = new FormData(form[0]);

            $.ajax({
                url: url,
                data: formdata,
                type: 'POST',
                dataType: 'json',
                context: this,
                processData: false,
                contentType: false,
                enctype: 'multipart/form-data',

                /** @inheritdoc */
                beforeSend: function () {
                    $(document.body).trigger('processStart');
                },

                /** @inheritdoc */
                complete: function () {
                    $(document.body).trigger('processStop');
                }
            })
            .done(function (response) {
                if (response.success) {
                    this.onSuccess();
                } else {
                    this.onError(response);
                }
            })
            .fail(function () {
                this.onError('Wholesale registration submit failed.');
            });
        },
        /**
         * Form validation succeed.
         */
        onSuccess: function () {
            $(this.options.formcontainer).hide();
            $(this.options.hideonsuccess).hide();
            $(this.options.successblock).show();
            document.getElementById(this.options.formid).reset();
            $("#wholesale-success-block").addClass('active');
        },

        /**
         * Form validation failed.
         */
        onError: function (response) {
            if (response['error_message']) {
                alert({
                    content: response['error_message']
                });
            }
        },
        /**
         * Prevents default submit action and calls form validator.
         *
         * @param {Event} event
         * @return {Boolean}
         */
        onSubmit: function (event) {
            event.preventDefault();
            if (!this.options.validationURL) {
                return true;
            }

            if (this.isValid()) {
                this.validateItems(this.options.validationURL, this.element.serialize());
            }
            return false;
        },
   });

    return $.Theiconnz.wholesale;
});
