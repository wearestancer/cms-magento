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

namespace StancerIntegration\Payments\Model\Adapter;

use Exception;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;
use Stancer\Config as StancerSdkConfig;
use Stancer\Customer;
use Stancer\Payment;
use StancerIntegration\Payments\Gateway\Config\Config;
use StancerIntegration\Payments\Gateway\Request\CustomerDataBuilder;
use StancerIntegration\Payments\Gateway\Request\PaymentDataBuilder;
use StancerIntegration\Payments\Gateway\Request\StoreDataBuilder;
use StancerIntegration\Payments\Gateway\Result\Sale;
use StancerIntegration\Payments\Model\Adminhtml\Source\Mode;
use StancerIntegration\Payments\Model\StoreConfigResolver;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StancerAdapter
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreConfigResolver
     */
    private $storeConfigResolver;

    /** @var UrlInterface */
    private $urlBuilder;

    /**
     * @var ProductMetadata
     */
    private $productMetadata;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var string */
    private $environment;

    /**
     * Construct StancerAdapter Class.
     *
     * @since 1.0.0
     *
     * @param Config $config
     * @param StoreConfigResolver $storeConfigResolver
     * @param UrlInterface $urlBuilder
     * @param ProductMetadata $productMetadata
     * @param LoggerInterface $logger
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function __construct(
        Config              $config,
        StoreConfigResolver $storeConfigResolver,
        UrlInterface        $urlBuilder,
        ProductMetadata     $productMetadata,
        LoggerInterface     $logger
    ) {
        $this->config = $config;
        $this->storeConfigResolver = $storeConfigResolver;
        $this->urlBuilder = $urlBuilder;
        $this->productMetadata = $productMetadata;
        $this->logger = $logger;

        $this->initCredentials();
    }

    /**
     * Initializes credentials.
     *
     * @since 1.0.0
     *
     * @return void
     * @throws InputException
     * @throws NoSuchEntityException
     */
    protected function initCredentials()
    {
        $storeId = $this->storeConfigResolver->getStoreId();
        $environmentIdentifier = $this->config->getValue(Config::KEY_MODE, $storeId);
        $this->setEnvironment(Mode::TEST);
        if ($environmentIdentifier === Mode::LIVE) {
            $this->setEnvironment(Mode::LIVE);
        }

        $apiConfig = StancerSdkConfig::init([
            $this->config->getValue(Config::KEY_TEST_MODE_PUBLIC_KEY, $storeId),
            $this->config->getValue(Config::KEY_TEST_MODE_PRIVATE_KEY, $storeId),
            $this->config->getValue(Config::KEY_PUBLIC_KEY, $storeId),
            $this->config->getValue(Config::KEY_PRIVATE_KEY, $storeId),
        ]);
        $apiConfig->setMode($this->isLiveMode() ? StancerSdkConfig::LIVE_MODE : StancerSdkConfig::TEST_MODE);
        $apiConfig->addAppData('libstancer-magento', \StancerIntegration\Payments\Model\Config::MODULE_VERSION);
        $apiConfig->addAppData('magento', $this->productMetadata->getVersion());
        $apiConfig->setLogger($this->logger);
    }

    /**
     * Set the value of the environment.
     *
     * @param string $value
     * @return void
     */
    private function setEnvironment($value)
    {
        $this->environment = $value;
    }

    /**
     * Is the environment live.
     *
     * @return boolean
     */
    public function isLiveMode(): bool
    {
        return $this->environment === Mode::LIVE;
    }

    /**
     * Submit Transaction for authorize
     *
     * @since 1.0.0
     * @since 1.0.1 Mobile phone is temporary removed
     * @since 1.0.2 Mobile phone is back
     *
     * @param array $attributes
     * @return Payment|null
     */
    public function authorize(array $attributes): ?Sale
    {
        $orderId = $attributes[PaymentDataBuilder::ORDER_ID];
        $amount = $attributes[PaymentDataBuilder::AMOUNT];
        $nonce = $attributes[PaymentDataBuilder::NONCE];
        $ret = $this->urlBuilder->getUrl('stancer/callback/returnurl', [
            'hash' => $nonce,
            'order' => $orderId
        ]);

        $customer = new Customer();
        $customer->setEmail($attributes[CustomerDataBuilder::CUSTOMER][CustomerDataBuilder::EMAIL]);
        $customer->setMobile($attributes[CustomerDataBuilder::CUSTOMER][CustomerDataBuilder::PHONE]);
        $customer->setName(implode(' ', [
            $attributes[CustomerDataBuilder::CUSTOMER][CustomerDataBuilder::FIRST_NAME],
            $attributes[CustomerDataBuilder::CUSTOMER][CustomerDataBuilder::LAST_NAME],
        ]));

        $payment = new Payment();
        $payment->setAmount($amount * 100);
        $payment->setCurrency(strtolower($attributes[PaymentDataBuilder::CURRENCY]));
        $payment->setOrderId($orderId);
        $payment->setReturnUrl($ret);
        $payment->setCapture(false);
        $payment->setDescription(
            $this->getPaymentDescription($orderId, $amount, $attributes[StoreDataBuilder::STORE_NAME])
        );
        $payment->setCustomer($customer);

        if ($attributes['options']['3dSecure']['required'] ?? false) {
            $payment->setAuth(true);
        }

        return $this->send($payment) ? new Sale($payment->getId(), $nonce, $payment->getPaymentPageUrl()) : null;
    }

    /**
     * Send request to Stancer API server
     *
     * @since 1.0.0
     *
     * @param Payment $instance
     * @return Payment|null
     */
    private function send(Payment $instance): ?Payment
    {
        try {
            return $instance->send();
        } catch (Exception $exception) {
            $this->log($exception->getMessage());
        }

        return null;
    }

    /**
     * Get a Stancer Payment.
     *
     * If no transactionId is null we will get a new payment.
     *
     * @since 1.0.0
     *
     * @param string|null $transactionId The Stancer\Payment Id.
     * @return null|Stancer\Payment
     */
    public function getPayment($transactionId): ?Payment
    {
        try {
            return new Payment($transactionId);
        } catch (Exception $exception) {
            $this->log($exception->getMessage());
        }

        return null;
    }

    /**
     * Submit transaction for capture
     *
     * @since 1.0.0
     *
     * @param string $transactionId
     * @param float $amount
     * @param array $attributes
     * @return ?Stancer\Payment
     */
    public function capture(string $transactionId, $amount, $attributes = []): ?Payment
    {
        $payment = new Payment($transactionId);
        $payment->setStatus(Payment\Status::CAPTURE);
        $payment->setAmount($amount * 100);
        $payment->setCurrency($attributes[PaymentDataBuilder::CURRENCY]);
        return $this->send($payment);
    }

    /**
     * Refund transaction
     *
     * @since 1.0.0
     *
     * @param string $transactionId
     * @param null|float $amount
     * @return ?Stancer\Payment
     */
    public function refund(string $transactionId, $amount = null): ?Payment
    {
        try {
            $payment = new Payment($transactionId);
            return $payment->refund($amount ? $amount * 100 : null);
        } catch (Exception $exception) {
            $this->log($exception->getMessage());
        }

        return null;
    }

    /**
     * Log an Error (Critical)
     *
     * @since 1.0.0
     *
     * @param string $message
     */
    private function log($message)
    {
        $this->logger->critical($message);
    }

    /**
     * Get the description of a payment
     *
     * @since 1.0.0.
     *
     * @param string $orderId The ID of the Magento order.
     * @param string|int $amount The amount of the payment.
     * @param string $storeName The name of the store which the payment has be done.
     * @return string The Description of the payment.
     */
    private function getPaymentDescription($orderId, $amount, $storeName)
    {
        $pattern = '%order%, %amount%, %store%';
        $customPattern = $this->config->getValue(
            Config::KEY_PAYMENT_DESCRIPTION,
            $this->storeConfigResolver->getStoreId()
        );

        if (!empty($customPattern)) {
            $pattern = $customPattern;
        }

        return str_replace(
            ['%order%', '%amount%', '%store%'],
            [$orderId, $amount, $storeName],
            $pattern
        );
    }
}
