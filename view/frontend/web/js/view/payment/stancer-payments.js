define([
  'uiComponent',
  'Magento_Checkout/js/model/payment/renderer-list',
], function (Component, rendererList) {
  'use strict';

  rendererList.push({
    type: 'stancer_payments',
    component: 'StancerIntegration_Payments/js/view/payment/method-renderer/iframe-methods',
  });

  /**
   * Add view logic here if needed
   **/
  return Component.extend({});
});
