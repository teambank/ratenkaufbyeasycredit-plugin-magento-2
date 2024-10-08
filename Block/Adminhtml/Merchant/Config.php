<?php
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Block\Adminhtml\Merchant;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Integration\Model\Oauth\TokenFactory;

class Config extends Template
{
    private TokenFactory $tokenFactory;

    private AdminSession $adminSession;

    public function __construct(
        Context $context,
        TokenFactory $tokenFactory,
        AdminSession $adminSession,
        array $data = []
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->adminSession = $adminSession;
        parent::__construct($context, $data);
    }

    private function getToken()
    {
        $token = $this->tokenFactory->create()
            ->createAdminToken($this->adminSession->getUser()->getId());

        return $token->getToken();
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return json_encode(
            [
                'endpoints' => [
                    'list' => $this->getBaseUrl() . 'rest/V1/easycredit/transactions?ids={transactionId}',
                    'get' => $this->getBaseUrl() . 'rest/V1/easycredit/transaction/{transactionId}',
                    'refund' => $this->getBaseUrl() . 'rest/V1/easycredit/transaction/{transactionId}/refund',
                    'capture' => $this->getBaseUrl() . 'rest/V1/easycredit/transaction/{transactionId}/capture',
                ],
                'apiBaseUrl' => 'https://ratenkauf.easycredit.de',
                'request_config' => [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->getToken(),
                    ],
                ],
            ]
        );
    }
}
