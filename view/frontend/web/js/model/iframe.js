define(['ko'], function (ko) {
    'use strict';

    let isInAction = ko.observable(false);

    return {
        isInAction: isInAction,

        /**
         * @param {Event} e
         * @returns void
         */
        returnIframe: function (e) {
            if(!e.origin.includes('stancer.com')){
                return;
            }

            if (e.data.status === 'finished' || e.data.status === 'error') {
                window.postMessage({ stopRedirection: true });
                window.location.href = e.data.url;
            }
        },

        /**
         * @param {jQuery.Event} event
         */
        stopEventPropagation: function (event) {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
    };
});
