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
namespace StancerIntegration\Payments\Controller\Callback;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;
use StancerIntegration\Payments\Gateway\Request\PaymentDataBuilder;
use StancerIntegration\Payments\Gateway\Response\PaymentDetailsHandler;
use StancerIntegration\Payments\Helper\Checkout;
use StancerIntegration\Payments\Model\Adapter\StancerAdapter;
use StancerIntegration\Payments\Model\Ui\ConfigProvider;

/**
 * Stancer Payments ReturnUrl controller class
 *
 * @since 1.0.0
 */
class ReturnUrl extends Action implements CsrfAwareActionInterface, HttpGetActionInterface
{
    /**
     * Payment method code
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $allowedPaymentMethodCodes = [
        ConfigProvider::CODE
    ];

    /**
     * State allowed
     *
     * @since 1.0.0
     *
     * @var array of allowed order states on frontend
     */
    protected $allowedOrderStates = [
        Order::STATE_PROCESSING,
        Order::STATE_PAYMENT_REVIEW
    ];

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Checkout
     */
    protected $_checkoutHelper;

    /**
     * @var StancerAdapter
     */
    protected $_stancerAdapter;

    /**
     * Construct ReturnUrl Class
     *
     * @since 1.0.0
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderFactory $orderFactory
     * @param Checkout $checkoutHelper
     * @param StancerAdapter $stancerAdapter
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context         $context,
        Session         $checkoutSession,
        OrderFactory    $orderFactory,
        Checkout        $checkoutHelper,
        StancerAdapter  $stancerAdapter,
        LoggerInterface $logger
    )
    {
        parent::__construct($context);

        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $logger;
        $this->_checkoutHelper = $checkoutHelper;
        $this->_stancerAdapter = $stancerAdapter;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * When a customer return to website from gateway.
     *
     * @since 1.0.0
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $order = $this->getOrderFromRequest();
        if ($order && $this->checkOrderState($order)) {
            $payment = $order->getPayment();
            $gatewayPayment = $this->_stancerAdapter->getPayment($payment->getAdditionalInformation(PaymentDetailsHandler::PAYMENT_ID));
            if ($gatewayPayment && $gatewayPayment->isSuccess()) {
                $payment->registerAuthorizationNotification($order->getBaseTotalDue());
                $payment->capture();
                $order->save();
                return $this->_redirect('checkout/onepage/success');
            }
        }

        $this->_checkoutHelper->restoreQuote();
        return $this->_redirect('checkout/onepage/failure');
    }

    /**
     * Returns an order from request.
     *
     * @since 1.0.0
     *
     * @return Order|null
     */
    private function getOrderFromRequest(): ?Order
    {
        $orderId = $this->getRequest()->getParam('order');
        if (!$orderId) {
            return null;
        }

        $order = $this->_orderFactory->create()->loadByIncrementId($orderId);
        $storedHash = (string)$order->getPayment()->getAdditionalInformation(PaymentDataBuilder::NONCE);
        $requestHash = (string)$this->getRequest()->getParam('hash');
        if (empty($storedHash) || empty($requestHash) || !hash_equals($storedHash, $requestHash)) {
            return null;
        }

        $this->_checkoutSession->setLastRealOrderId($orderId);

        return $order;
    }

    /**
     * Check order state
     *
     * @since 1.0.0
     *
     * @param Order $order
     * @return bool
     */
    protected function checkOrderState(Order $order)
    {
        return in_array($order->getState(), $this->allowedOrderStates);
    }

    /**
     * Check requested payment method
     *
     * @since 1.0.0
     *
     * @param Order $order
     * @return bool
     */
    protected function checkPaymentMethod(Order $order)
    {
        $payment = $order->getPayment();
        return in_array($payment->getMethod(), $this->allowedPaymentMethodCodes);
    }
}
