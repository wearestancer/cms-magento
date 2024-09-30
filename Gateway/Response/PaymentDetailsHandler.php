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

namespace StancerIntegration\Payments\Gateway\Response;

use Magento\Framework\App\State;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use StancerIntegration\Payments\Gateway\Helper\SubjectReader;
use StancerIntegration\Payments\Gateway\Request\PaymentDataBuilder;
use StancerIntegration\Payments\Gateway\Result\Sale;

class PaymentDetailsHandler implements HandlerInterface
{
    public const PAYMENT_ID = 'paymentId';

    /**
     * Order Payment Page URL
     */
    public const PAYMENT_PAGE_URL = 'paymentPageURL';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Construct PaymentDetailsHandler Class
     *
     * @since 1.0.0
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        /** @var Sale $saleResult */
        $saleResult = $this->subjectReader->readSaleTransaction($response);

        /** @var OrderPaymentInterface $payment */
        $payment = $paymentDO->getPayment();

        $payment->setAdditionalInformation(PaymentDetailsHandler::PAYMENT_ID, $saleResult->getID());
        $payment->setAdditionalInformation(PaymentDataBuilder::NONCE, $saleResult->getNonce());
        $payment->setAdditionalInformation(PaymentDetailsHandler::PAYMENT_PAGE_URL, $saleResult->getPaymentPageURL());
        $payment->setIsTransactionClosed(false);
        $payment->setIsTransactionPending(true);
        $payment->getOrder()->setState(Order::STATE_PENDING_PAYMENT);
        $payment->getOrder()->setStatus(Order::STATE_PENDING_PAYMENT);
        $payment->getOrder()->save();
    }
}
