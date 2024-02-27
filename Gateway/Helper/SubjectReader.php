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

namespace StancerIntegration\Payments\Gateway\Helper;

use InvalidArgumentException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper;
use Stancer\Payment;
use Stancer\Exceptions;
use StancerIntegration\Payments\Gateway\Result\Sale;

class SubjectReader
{
    /**
     * Reads response object from subject
     *
     * @since 1.0.0
     *
     * @param array $subject
     * @return object
     */
    public function readResponseObject(array $subject)
    {
        $response = Helper\SubjectReader::readResponse($subject);
        if (!isset($response['object']) || !is_object($response['object'])) {
            throw new InvalidArgumentException('Response object does not exist responseObject');
        }

        return $response['object'];
    }

    /**
     * Reads payment from subject
     *
     * @since 1.0.0
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject): PaymentDataObjectInterface
    {
        return Helper\SubjectReader::readPayment($subject);
    }

    /**
     * Reads transaction from subject as a Sale result
     *
     * @since 1.0.0
     *
     * @param array $subject
     */
    public function readSaleTransaction(array $subject)
    {
        if (!isset($subject['object']) || !is_object($subject['object'])) {
            throw new InvalidArgumentException('Response object does not exist');
        }

        if (!($subject['object'] instanceof Sale)) {
            throw new InvalidArgumentException(
                'The object is not a class StancerIntegration\Payments\Gateway\Result\Sale.'
            );
        }

        return $subject['object'];
    }

    /**
     * Reads transaction from subject as a \Stancer\Payment
     *
     * @since 1.0.0
     *
     * @param array $subject
     */
    public function readTransaction(array $subject)
    {
        if (!isset($subject['object']) || !is_object($subject['object'])) {
            throw new InvalidArgumentException('Response object does not exist');
        }

        if (!($subject['object'] instanceof Payment)) {
            throw new InvalidArgumentException('The object is not a class \Stancer\Payment');
        }

        return $subject['object'];
    }

    /**
     * Reads refund transaction
     *
     * @since unreleased
     *
     * @param array $subject
     * @return void
     */
    public function readRefund(array $subject)
    {
        if (!isset($subject['object']) || !is_object($subject['object'])) {
            throw new LocalizedException(__('An unexpected Error has occured.'));
        }
        if ($subject['object'] instanceof Exceptions\ConflictException) {
            throw new LocalizedException(
                __('Refund is not available, this is probably because your payment isn\'t captured yet')
            );
        }

        if (!($subject['object'] instanceof Payment)) {
            throw new InvalidArgumentException('The object is not a class \Stancer\Payment');
        }

        return $subject['object'];
    }

    /**
     * Reads amount from subject
     *
     * @since 1.0.0
     *
     * @param array $subject
     * @return mixed
     */
    public function readAmount(array $subject)
    {
        return Helper\SubjectReader::readAmount($subject);
    }

    /**
     * Reads customer id from subject
     *
     * @since 1.0.0
     *
     * @param array $subject
     * @return int
     */
    public function readCustomerId(array $subject): int
    {
        if (!isset($subject['customer_id'])) {
            throw new InvalidArgumentException('The "customerId" field does not exists');
        }

        return (int)$subject['customer_id'];
    }
}
