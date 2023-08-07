<?php
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Block\Sales\Order\Invoice\Totals;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObjectFactory;
use Magento\Sales\Block\Adminhtml\Order\Invoice\Totals;
use Magento\Framework\DataObject;
class Fee extends Template
{

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    public function initTotals()
    {

        /**
         * @var Totals $parent
         */
        $parent = $this->getParentBlock();
        $source = $parent->getSource();

        $amount = $source->getEasycreditAmount();
        $baseAmount = $source->getBaseEasycreditAmount();

        if ($amount === null || (float) $amount == 0) {
            return $this;
        }

        $total = new DataObject(
            [
            'code'  => 'easycredit_amount',
            'value' => $amount,
            'base_value' => $baseAmount,
            'label' => __('Interest')
            ]
        );
        $after = 'subtotal';

        $parent->addTotal($total, $after);
        return $this;
    }
}
