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

namespace StancerIntegration\Payments\Gateway\Request;

use InvalidArgumentException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Sales\Model\Order\Payment;
use Psr\Log\LoggerInterface;
use StancerIntegration\Payments\Gateway\Helper\SubjectReader;
use StancerIntegration\Payments\Gateway\Response\PaymentDetailsHandler;

class RefundDataBuilder implements BuilderInterface
{
    use Formatter;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Construct RefundDataBuilder Class
     *
     * @since 1.0.0
     *
     * @param SubjectReader $subjectReader
     * @param LoggerInterface $logger
     */
    public function __construct(
        SubjectReader   $subjectReader,
        LoggerInterface $logger
    )
    {
        $this->subjectReader = $subjectReader;
        $this->logger = $logger;
    }

    /**
     * Builds ENV request
     *
     * @since 1.0.0
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject): array
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        $amount = null;

        try {
            $amount = $this->formatPrice($this->subjectReader->readAmount($buildSubject));
        } catch (InvalidArgumentException $e) {
            $this->logger->critical($e->getMessage());
        }

        return [
            'transaction_id' => $payment->getParentTransactionId(),
            PaymentDetailsHandler::PAYMENT_ID => $payment->getAdditionalInformation(PaymentDetailsHandler::PAYMENT_ID),
            PaymentDataBuilder::AMOUNT => $amount
        ];
    }
}
