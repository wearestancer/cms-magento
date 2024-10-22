define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'StancerIntegration_Payments/js/model/iframe',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/url',
    'Magento_Ui/js/modal/alert'
], function ($, Component, iframe, fullScreenLoader, url, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'StancerIntegration_Payments/payment/iframe-methods',
            paymentReady: false,
            paymentUrl: null
        },
        redirectAfterPlaceOrder: false,
        isInAction: iframe.isInAction,

        /**
         * @return {exports}
         */
        initObservable: function () {
            this._super().observe('paymentReady');

            this._super().observe('paymentUrl');

            return this;
        },

        getCode: function () {
            return 'stancer_payments';
        },

        getTitle: function () {
            return window.checkoutConfig.payment.stancer_payments.title ?? 'Stancer Payments';
        },

        getFlow: function () {
            return window.checkoutConfig.payment.stancer_payments.flow ?? 'iframe';
        },

        /**
         * @return {*}
         */
        isPaymentReady: function () {
            return this.paymentReady();
        },

        /**
         * Get action url for payment method iframe.
         * @returns {String}
         */
        getActionUrl: function () {
            return this.isInAction() ? this.paymentUrl() : '';
        },

        /**
         * Places order in pending payment status.
         */
        placePendingPaymentOrder: function () {
            if (this.placeOrder()) {
                fullScreenLoader.startLoader();
                this.isInAction(true);
                document.addEventListener('click', iframe.stopEventPropagation, true);
            }
        },

        /**
         * @return {*}
         */
        getPlaceOrderDeferredObject: function () {
            var self = this;

            return this._super().fail(function () {
                fullScreenLoader.stopLoader();
                self.isInAction(false);
                document.removeEventListener('click', iframe.stopEventPropagation, true);
            });
        },

        /**
         * After place order callback
         */
        afterPlaceOrder: function () {
            if (this.getFlow() === 'redirect') {
                window.location.replace(url.build('stancer/redirect'));
                return;
            }

            $.ajax({
                method: 'GET',
                url: url.build('stancer/redirect/iframe'),
                data: { paymentMethod: this.getCode() },
                dataType: 'json'
            })
                .done(
                    function (resp) {
                        this.paymentUrl(resp.url);
                        this.paymentReady(true);
                        this.iframeIsLoaded = true;
                        this.isPlaceOrderActionAllowed(true);
                        window.addEventListener('message', (e) => iframe.returnIframe(e));
                    }.bind(this)
                )
                .fail(
                    function (resp) {
                        this.getPlaceOrderDeferredObject();
                        this.error(resp.responseJSON.error);
                    }.bind(this)
                )
                .always(function () {
                    fullScreenLoader.stopLoader();
                });
        },

        /**
         * Hide loader when iframe is fully loaded.
         */
        iframeLoaded: function () {
            fullScreenLoader.stopLoader();
        },

        /**
         * Show alert message
         * @param {String} message
         */
        error: function (message) {
            alert({
                content: message
            });
        }
    });
});
