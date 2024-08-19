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

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use StancerIntegration\Payments\Gateway\Helper\SubjectReader;

class PaymentDataBuilder implements BuilderInterface
{
    use Formatter;

    /**
     * The billing amount of the request. This value must be greater than 0,
     * and must match the currency format of the merchant account.
     */
    public const AMOUNT = 'amount';

    /**
     * One-time-use token that references a payment method provided by your customer.
     *
     * The nonce serves as proof that the user has authorized payment.
     */
    public const NONCE = 'nonce';

    /**
     * Order ID Key
     */
    public const ORDER_ID = 'orderId';

    /**
     * Order Currency Key
     */
    public const CURRENCY = 'currency';

    /**
     * @var SubjectReader $subjectReader
     */
    private $subjectReader;

    /**
     * Construct PaymentDataBuilder Class.
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
    public function build(array $buildSubject): array
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $order = $paymentDO->getOrder();

        return [
            self::AMOUNT => $this->formatPrice($this->subjectReader->readAmount($buildSubject)),
            self::CURRENCY => $order->getCurrencyCode(),
            self::ORDER_ID => $order->getOrderIncrementId(),
            self::NONCE => bin2hex(random_bytes(16))
        ];
    }
}
