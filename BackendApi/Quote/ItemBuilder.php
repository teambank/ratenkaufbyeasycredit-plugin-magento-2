<?php
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\BackendApi\Quote;

use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Store\Model\StoreManagerInterface;
use Teambank\RatenkaufByEasyCreditApiV3 as Api;

class ItemBuilder
{
    private $_categoryResource;
    private $_storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryResource $categoryResource
    ) {
        $this->_categoryResource = $categoryResource;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param  $categoryIds
     * @return array|bool|null|string
     */
    private function getDeepestCategoryName($categoryIds)
    {
        if (is_array($categoryIds) && count($categoryIds) > 0) {
            $categoryId = end($categoryIds);
            return $this->_categoryResource->getAttributeRawValue(
                $categoryId,
                'name',
                $this->_storeManager->getStore()->getId()
            );
        }
        return null;
    }

    private function buildSkus($item): array
    {
        $skus = [];
        foreach (\array_filter(
            [
            'sku' => $item->getSku(),
            'ean' => $item->getEan()
            ]
        ) as $type => $sku) {
            $skus[] = new Api\Model\ArticleNumberItem(
                [
                'numberType' => $type,
                'number' => $sku
                ]
            );
        }
        return $skus;
    }

    public function build($item)
    {
        return new Api\Model\ShoppingCartInformationItem(
            [
            'productName' => $item->getName(),
            'quantity' => $item->getQty(),
            'price' => $item->getPrice(),
            'manufacturer' => $item->getProduct()->getData('manufacturer'),
            'productCategory' => $this->getDeepestCategoryName($item->getProduct()->getCategoryIds()),
            'articleNumber' => $this->buildSkus($item)
            ]
        );
    }
}