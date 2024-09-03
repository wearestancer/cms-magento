<?php
/**
 * This file is a part of Stancer Magento module.
 *
 * See readme for more informations.
 *
 * @link https://www.stancer.com/
 * @license MIT
 * @copyright 2023-2024 Stancer / Iliad 78
 *
 * @package stancer/cms-magento
 */

namespace StancerIntegration\Payments\Gateway\Http\Client;

use Exception;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;
use StancerIntegration\Payments\Helper\PhoneFormatter;
use StancerIntegration\Payments\Model\Adapter\StancerAdapter;

abstract class AbstractTransaction implements ClientInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Logger
     */
    protected $customLogger;

    /**
     * @var StancerAdapter
     */
    protected $adapter;

    /**
     * @var
     */
    protected $phoneFormatter;

    /**
     * Construct AbstractTransaction class
     *
     * @since 1.0.0
     *
     * @param LoggerInterface $logger
     * @param Logger $customLogger
     * @param StancerAdapter $adapter
     * @param PhoneFormatter $phoneFormatter
     */
    public function __construct(LoggerInterface $logger, Logger $customLogger, StancerAdapter $adapter, PhoneFormatter $phoneFormatter)
    {
        $this->logger = $logger;
        $this->customLogger = $customLogger;
        $this->adapter = $adapter;
        $this->phoneFormatter = $phoneFormatter;
    }

    /**
     * @inheritdoc
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $data = $transferObject->getBody();
        $log = [
            'request' => $data,
            'client' => static::class
        ];
        $response['object'] = [];

        try {
			$formattedData = $this->phoneFormatter->updatePhoneNumbers($data);
            $response['object'] = $this->process($formattedData);
        } catch (Exception $e) {
            $message = __($e->getMessage() ?: 'Sorry, but something went wrong');
            $this->logger->critical($message);
            throw new ClientException($message);
        } finally {
            $log['response'] = (array)$response['object'];
            $this->customLogger->debug($log);
        }

        return $response;
    }

    /**
     * Process http request
     *
     * @since 1.0.0
     *
     * @param array $data
     * @return mixed
     */
    abstract protected function process(array $data);
}
