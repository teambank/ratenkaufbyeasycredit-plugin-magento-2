define([
  "uiComponent",
  "Magento_Checkout/js/model/payment/renderer-list",
], function (Component, rendererList) {
  "use strict";
  rendererList.push({
    type: "easycredit_rechnung",
    component:
      "Netzkollektiv_EasyCredit/js/view/payment/method-renderer/easycredit-method",
  });
  rendererList.push({
    type: "easycredit_ratenkauf",
    component:
      "Netzkollektiv_EasyCredit/js/view/payment/method-renderer/easycredit-method",
  });
  return Component.extend({});
});
