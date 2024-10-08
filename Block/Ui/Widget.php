<?php
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Block\Ui;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;

class Widget extends Template
{
    private ScopeConfigInterface $scopeConfig;

    private CheckoutSession $checkoutSession;

    private PaymentHelper $paymentHelper;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        PaymentHelper $paymentHelper,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $context->getScopeConfig();
        $this->paymentHelper = $paymentHelper;

        parent::__construct($context, $data);
    }

    public function getApiKey()
    {
        return $this->scopeConfig->getValue(
            'payment/easycredit/credentials/api_key',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    public function getConfigValue($key)
    {
        return $this->scopeConfig->getValue(
            'payment/easycredit/' . $key,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAmount(): ?float
    {
        if ($this->getData('amount')) {
            return $this->getData('amount');
        }

        $totals = $this->checkoutSession->getQuote()->getTotals();
        return $totals['grand_total']->getValue();
    }

    public function getPaymentHelper()
    {
        return $this->paymentHelper;
    }
}
