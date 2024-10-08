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

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Stancer\Payment\Status;
use StancerIntegration\Payments\Gateway\Helper\SubjectReader;
use StancerIntegration\Payments\Gateway\Response\PaymentDetailsHandler;
use StancerIntegration\Payments\Model\Adapter\StancerAdapter;

class CaptureDataBuilder implements BuilderInterface
{
    use Formatter;

    public const TRANSACTION_ID = 'transaction_id';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var StancerAdapter
     */
    private $stancerAdapter;

    /**
     * Construct CaptureDataBuilder Class.
     *
     * @since 1.0.0
     *
     * @param SubjectReader $subjectReader
     * @param StancerAdapter $stancerAdapter
     */
    public function __construct(SubjectReader $subjectReader, StancerAdapter $stancerAdapter)
    {
        $this->subjectReader = $subjectReader;
        $this->stancerAdapter = $stancerAdapter;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function build(array $buildSubject): array
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();

        $paymentId = $payment->getAdditionalInformation(PaymentDetailsHandler::PAYMENT_ID);
        if (!$paymentId) {
            throw new LocalizedException(
                __('No authorization transaction to proceed capture.')
            );
        }

        $stancerPayment = $this->stancerAdapter->getPayment($paymentId);
        if (!$stancerPayment || $stancerPayment->getStatus() !== Status::AUTHORIZED) {
            throw new LocalizedException(
                __('Capture is not allowed due to the transaction is not in authorized status.')
            );
        }

        return [
            self::TRANSACTION_ID => $paymentId,
            PaymentDataBuilder::AMOUNT => $this->formatPrice($this->subjectReader->readAmount($buildSubject)),
            PaymentDataBuilder::CURRENCY => $paymentDO->getOrder()->getCurrencyCode(),
            PaymentDataBuilder::ORDER_ID => $paymentDO->getOrder()->getOrderIncrementId()
        ];
    }
}
