<?php
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;

class InvoiceSaveAfter implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        $event = $observer->getEvent();

        /**
         * @var Invoice $invoice
         */
        $invoice = $event->getData('invoice');

        if ($invoice->getBaseEasycreditAmount()) {
            $order = $invoice->getOrder();
            $order->setEasycreditAmountInvoiced(
                $order->getEasycreditAmountInvoiced() + $invoice->getEasycreditAmount()
            );
            $order->setBaseEasycreditAmountInvoiced(
                $order->getBaseEasycreditAmountInvoiced() + $invoice->getBaseEasycreditAmount()
            );
        }
    }
}
