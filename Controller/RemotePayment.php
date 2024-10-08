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

namespace StancerIntegration\Payments\Controller;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\Method\Adapter;
use StancerIntegration\Payments\Gateway\Config\Config;
use StancerIntegration\Payments\Gateway\Response\PaymentDetailsHandler;
use StancerIntegration\Payments\Model\Ui\ConfigProvider;

abstract class RemotePayment extends Action
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var PaymentHelper
     */
    protected $_data;

    /**
     * @var RequestInterface
     */
    protected $_request;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var PaymentHelper
     */
    private $_paymentHelper;

    /**
     * Redirect construtor
     *
     * @since 1.0.0
     *
     * @param RequestInterface $request
     * @param Context $context
     * @param Session $checkoutSession
     * @param PaymentHelper $paymentHelper
     * @param Config $config
     */
    public function __construct(
        RequestInterface $request,
        Context          $context,
        Session          $checkoutSession,
        PaymentHelper    $paymentHelper,
        Config           $config
    ) {
        $this->_request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_paymentHelper = $paymentHelper;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * Get the Magento payment ID
     *
     * @since 1.0.0
     *
     * @return string
     * @throws \Magento\Framework\Validator\Exception
     */
    public function getPaymentId()
    {
        $paymentId = $this->getPaymentInformation(PaymentDetailsHandler::PAYMENT_ID);
        if (!$paymentId) {
            throw new \Magento\Framework\Validator\Exception(
                __('Payment is not initialized, please place your order first!')
            );
        }

        return $paymentId;
    }

    /**
     * Get the Magento payment URL
     *
     * @since 1.0.0
     *
     * @return string
     * @throws \Magento\Framework\Validator\Exception
     */
    public function getPaymentPageURL()
    {
        $paymentId = $this->getPaymentInformation(PaymentDetailsHandler::PAYMENT_PAGE_URL);
        if (!$paymentId) {
            throw new \Magento\Framework\Validator\Exception(
                __('Payment is not initialized, please place your order first!')
            );
        }

        return $paymentId;
    }

    /**
     * Get the Magento payment information
     *
     * @since 1.0.0
     * @param string $key
     * @return string|null
     */
    public function getPaymentInformation($key)
    {
        try {
            $order = $this->getCheckoutSession()->getLastRealOrder();
            if (!$order) {
                throw new \Magento\Framework\Validator\Exception(
                    __('Please place your order first!')
                );
            }

            $method = $order->getPayment()->getMethod();
            $methodInstance = $this->_paymentHelper->getMethodInstance($method);
            if ($methodInstance instanceof Adapter && $methodInstance->getCode() === ConfigProvider::CODE) {
                return $order->getPayment()->getAdditionalInformation()[$key] ?? null;
            }

            throw new \Magento\Framework\Validator\Exception(
                __('The selected Payment Method is Not Belonging to Stancer Payments.')
            );
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
            $this->getCheckoutSession()->restoreQuote();
        }

        return null;
    }

    /**
     * Return checkout session object
     *
     * @since 1.0.0
     *
     * @return Session
     */
    protected function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }
}
