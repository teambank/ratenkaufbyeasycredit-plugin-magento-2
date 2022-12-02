<?php
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Service;

use Magento\Framework\Webapi\Exception as WebapiException;

use Netzkollektiv\EasyCredit\Api\VerifyCredentialsInterface;
use Netzkollektiv\EasyCredit\Helper\Data as EasyCreditHelper;
use Netzkollektiv\EasyCredit\Logger\Logger;

use Teambank\RatenkaufByEasyCreditApiV3\Integration\ApiCredentialsInvalidException;
use Teambank\RatenkaufByEasyCreditApiV3\Integration\ApiCredentialsNotActiveException;

class VerifyCredentials implements VerifyCredentialsInterface
{

    /**
     * @var EasyCreditHelper
     */
    private $easyCreditHelper;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EasyCreditHelper $easyCreditHelper,
        Logger $logger
    ) {
        $this->easyCreditHelper = $easyCreditHelper;
        $this->logger = $logger;
    }

    public function verifyCredentials(?string $apiKey, ?string $apiToken, ?string $apiSignature = null)
    {
        try {
            $this->easyCreditHelper
                ->getCheckout()
                ->verifyCredentials($apiKey, $apiToken, $apiSignature);
            return true;
        } catch (ApiCredentialsInvalidException $e) {
            throw new WebapiException(
                __('Credentials incorrect. Please review the inserted values and try again.'), 
                0, 
                WebapiException::HTTP_FORBIDDEN
            );
        } catch (ApiCredentialsNotActiveException $e) {
            throw new WebapiException(
                __('The provided API credentials are valid, but not yet activated'), 
                0, 
                WebapiException::HTTP_FORBIDDEN
            );
        } catch (\Throwable $e) {
            $this->logger->error($e);
            throw new WebapiException(
                __('Error verifying credentials. Please try again later.'), 
                0, 
                WebapiException::HTTP_FORBIDDEN
            );
        }
    }
}