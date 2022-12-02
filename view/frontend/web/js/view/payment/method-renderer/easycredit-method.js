define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/method-list',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/full-screen-loader',
        'uiRegistry'
    ],
    function (ko, $, Component, paymentMethods, customer, setPaymentInformation, selectBillingAddress, additionalValidators, customerData, quote, storage, urlBuilder, fullScreenLoader, registry) {
        'use strict';

        var onHydrated = function (selector, cb) {    
            window.setTimeout(function() {
                if (!document.querySelector(selector) || !document.querySelector(selector).classList.contains('hydrated')) {
                    return onHydrated(selector, cb);
                }
                cb();
            }, 50)
        };

        return Component.extend(
            {
                defaults: {
                    template: 'Netzkollektiv_EasyCredit/payment/easycredit',
                    isAvailable: true,
                    apiKey: window.checkoutConfig.payment.easycredit.apiKey,
                    errorMessage: null,
                    numberOfInstallments: false,
                    calculatedForGrandTotal: null,
                    redirectAfterPlaceOrder: false,
                    componentHtml: ''
                },
                initObservable: function () {
                    this._super()
                    .observe(
                        [
                            'isAvailable',
                            'apiKey',
                            'errorMessage',
                            'componentHtml',
                            'numberOfInstallments',
                            'calculatedForGrandTotal'
                        ]
                    );

                    this.isSelected = ko.computed(function() {
                        return this.getCode() === this.isChecked()
                    }.bind(this));

                    this.checkAvailable();
                    this.renderComponent();

                    var billingAddressComponent = registry.get(
                        function (item) { 
                            return item.component == 'Magento_Checkout/js/view/billing-address';
                        }
                    )
                    if (typeof billingAddressComponent !== 'undefined') {
                        billingAddressComponent
                            .isAddressDetailsVisible
                            .subscribe(this.checkAvailable.bind(this));
                    }

                    this.isSelected.subscribe(this.checkAvailable.bind(this));
                    paymentMethods.subscribe(this.checkAvailable.bind(this));
                    this.isAvailable.subscribe(this.renderComponent.bind(this))
                    this.errorMessage.subscribe(this.renderComponent.bind(this))
                    this.getAmount.subscribe(this.renderComponent.bind(this))

                    return this;
                },
                getAmount: ko.computed(function () {
                    return (quote.totals()) ? quote.totals().grand_total : 0;
                }),
                getData: function () {
                    var data = {
                        method: this.getCode(),
                        additional_data: {}
                    };

                    if (this.numberOfInstallments > 0) {
                        data.additional_data.duration = this.numberOfInstallments;
                        data.additional_data.cc_type = this.numberOfInstallments;

                    }
            
                    return data;
                },
                getCheckoutMethod: function () {
                    return customer.isLoggedIn() ? 'customer' : 'guest';
                },
                getIsAvailableUri: function (quote) {
                    var method = this.getCheckoutMethod();

                    var params = {};
                    if (method == 'guest') {
                        params.quoteId = quote.getQuoteId();
                    }
                    var urls = {
                        'guest': '/easycredit/checkout/data/:quoteId',
                        'customer': '/easycredit/checkout/data'
                    };
                    var uri = urlBuilder.createUrl(urls[method], params);
                    return uri;
                },
                getCheckoutStartUri: function (quote) {
                    var method = this.getCheckoutMethod();

                    var params = {};
                    if (method == 'guest') {
                        params.quoteId = quote.getQuoteId();
                    }
                    var urls = {
                        'guest': '/easycredit/checkout/start/:quoteId',
                        'customer': '/easycredit/checkout/start'
                    };
                    var uri = urlBuilder.createUrl(urls[method], params);
                    return uri;
                },
                startCheckout () {
                    var uri = this.getCheckoutStartUri(quote);

                    storage.get(uri).done(function (data) {
                        if (data.redirect_url) {
                            customerData.invalidate(['cart']);
                            $.mage.redirect(data.redirect_url);
                        }
                    }.bind(this)).fail(function (response) {
                        var message = response.responseJSON.message;
                        if (message) {
                            this.errorMessage(message);
                            $('easycredit-checkout')
                                .get(0)
                                .dispatchEvent(new Event('closeModal'));
                        }

                        this.isAvailable(false);
                        fullScreenLoader.stopLoader(true);
                    }.bind(this));
                },
                renderComponent: function () {
                    var attrs = {
                        'webshop-id':   this.apiKey(),
                        'is-active':    true,
                        'amount':       this.getAmount(),
                        'alert':        this.errorMessage(),
                    };

                    var html = [];
                    Object.keys(attrs).forEach(function(key, index) {
                        var value = attrs[key];
                        if (value !== null) {
                            html.push(key + '="' + attrs[key] + '"');                            
                        }
                    });

                    this.componentHtml("<easycredit-checkout " + html.join(' ') + "></easycredit-checkout>");
                    this.handlePaymentConfirm();
                },
                checkAvailable: function () {

                    fullScreenLoader.startLoader();

                    var uri = this.getIsAvailableUri(quote);

                    storage.get(uri).done(function (data) {
                        fullScreenLoader.stopLoader(true);
                        this.errorMessage(data.error_message);

                        if (data.error_message !== null) {
                            var radioButton = $('#easycredit');

                            if (this.errorMessage() !== null && radioButton.prop('checked')) {
                                radioButton.prop('checked', false);
                            }

                            return this.isAvailable(false);
                        }    
                        return this.isAvailable(true);              
                    }.bind(this)).fail(function () {
                        this.isAvailable(false);
                        fullScreenLoader.stopLoader(true);
                    }.bind(this));
                    return true;
                },
                handlePaymentConfirm: function () {
                    onHydrated('easycredit-checkout', function(){
                        $('easycredit-checkout').submit(function(e){
                            if (!additionalValidators.validate()) {
                                $('easycredit-checkout')
                                    .get(0)
                                    .dispatchEvent(new Event('closeModal'));
                                return;
                            }
                            this.numberOfInstallments = e.detail.numberOfInstallments;
                            this.continueToEasyCredit();
                        }.bind(this));
                    }.bind(this));
                },
                continueToEasyCredit: function () {
                    if (additionalValidators.validate()) {
                        selectBillingAddress(quote.shippingAddress());
                        this.selectPaymentMethod();
                        setPaymentInformation(this.messageContainer, quote.paymentMethod()).done(function() {
                            this.startCheckout();
                        }.bind(this));

                        return false;
                    }
                }
            }
        );
    }
);
