<?php
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Service;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Framework\Exception\LocalizedException;

use Netzkollektiv\EasyCredit\Api\CheckoutInterface;
use Netzkollektiv\EasyCredit\Api\Data\CheckoutDataInterface;
use Netzkollektiv\EasyCredit\BackendApi\QuoteBuilder;
use Netzkollektiv\EasyCredit\BackendApi\StorageFactory;
use Netzkollektiv\EasyCredit\Helper\Data as EasyCreditHelper;
use Netzkollektiv\EasyCredit\Logger\Logger;

use Teambank\RatenkaufByEasyCreditApiV3\ApiException;

class Checkout implements CheckoutInterface
{
    public function __construct(
        private CartRepositoryInterface $quoteRepository,
        private CheckoutSession $checkoutSession,
        private EasyCreditHelper $easyCreditHelper,
        private CheckoutDataInterface $checkoutData,
        private QuoteBuilder $easyCreditQuoteBuilder,
        private Logger $logger,
        private StorageFactory $storageFactory
    ) {
    }

    /**
     * @api
     * @param  string $cartId
     * @return \Netzkollektiv\EasyCredit\Api\Data\CheckoutDataInterface
     */
    public function getCheckoutData($cartId)
    {
        try {
            $ecQuote = $this->easyCreditQuoteBuilder->build();
            $this->easyCreditHelper->getCheckout()->isAvailable(
                $ecQuote
            );
        } catch (\Exception $e) {
            $this->checkoutData->setErrorMessage($e->getMessage());
        }

        return $this->checkoutData;
    }

    /**
     * @throws LocalizedException
     */
    private function _validateQuote() : void
    {
        $quote = $this->checkoutSession->getQuote();

        if (!$quote->hasItems() || $quote->getHasError()) {
            throw new LocalizedException(__('Unable to initialize easyCredit Payment.'));
        }
    }

    private function getStorage () {
        return $this->storageFactory->create(
            [
            'payment' => $this->checkoutSession->getQuote()->getPayment()
            ]
        );
    }

    /**
     * @api
     * @param  string $cartId
     * @param  boolean $express
     * @return \Netzkollektiv\EasyCredit\Api\Data\CheckoutDataInterface
     */
    public function start($cartId, $express = false)
    {
        try {
            try {
                $this->_validateQuote();

                if ($express) {
                    $this->getStorage()->clear();
                    $this->getStorage()->set('express', true);
                    $this->prepareExpressCheckout();
                }

                $ecQuote = $this->easyCreditQuoteBuilder->build();
                $this->easyCreditHelper->getCheckout()->start(
                    $ecQuote
                );

                $quote = $this->checkoutSession->getQuote();
                $quote->getPayment()->save(); // @phpstan-ignore-line
                $quote->collectTotals();
                $this->quoteRepository->save($quote);

                if ($url = $this->easyCreditHelper->getCheckout()->getRedirectUrl()) {
                    $this->checkoutData->setRedirectUrl($url);
                }
            } catch (ApiException $e) {
                $response = json_decode((string) $e->getResponseBody());
                if ($response === null || !isset($response->violations)) {
                    throw new \Exception('violations could not be parsed');
                }

                $messages = [];
                foreach ($response->violations as $violation) {
                    $messages[] = implode(': ',[$violation->field, $violation->message]);
                }

                throw new WebapiException(
                    __(implode(', ', $messages)), 
                    0, 
                    WebapiException::HTTP_FORBIDDEN
                );
            }
        } catch (WebapiException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->logger->error($e);
            throw new WebapiException(
                __('Es ist ein Fehler aufgetreten. Leider steht Ihnen easyCredit-Ratenkauf derzeit nicht zur Verfügung.'), 
                0, 
                WebapiException::HTTP_FORBIDDEN
            );
        }
        return $this->checkoutData;
    }

    protected function prepareExpressCheckout () {
        $quote = $this->checkoutSession->getQuote();
        $shippingAddress = $quote->getShippingAddress();

        if ($shippingAddress->getCountryId() === null) {
            $shippingAddress->setCountryId('DE');
        }
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates();
        $shippingMethod = current($shippingAddress->getAllShippingRates());

        if ($shippingMethod) {
            $shippingAddress->setShippingMethod($shippingMethod->getCode());
            $shippingAddress->collectShippingRates();
        }
        $this->quoteRepository->save($quote);
    }
}