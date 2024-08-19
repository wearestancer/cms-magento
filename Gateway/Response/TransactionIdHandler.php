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

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use StancerIntegration\Payments\Gateway\Helper\SubjectReader;
use StancerIntegration\Payments\Gateway\Result\Sale;

class TransactionIdHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Construct TransactionIdHandler Class.
     *
     * @since 1.0.0
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader
    )
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles response
     *
     * @since 1.0.0
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        if ($paymentDO->getPayment() instanceof Payment) {
            /** @var Sale $saleResult */
            $saleResult = $this->subjectReader->readSaleTransaction($response);

            /** @var Payment $orderPayment */
            $orderPayment = $paymentDO->getPayment();
            $this->setTransactionId(
                $orderPayment,
                $saleResult
            );

            $orderPayment->setIsTransactionClosed($this->shouldCloseTransaction());
            $orderPayment->setIsTransactionPending(true);
            $closed = $this->shouldCloseParentTransaction($orderPayment);
            $orderPayment->setShouldCloseParentTransaction($closed);
        }
    }

    /**
     * Set transaction Id
     *
     * @since 1.0.0
     *
     * @param Payment $orderPayment
     * @param \Stancer\Payment $transaction
     * @return void
     */
    protected function setTransactionId(Payment $orderPayment, Sale $saleResult)
    {
        $orderPayment->setTransactionId($saleResult->getID());
    }

    /**
     * Whether transaction should be closed
     *
     * @since 1.0.0
     *
     * @return bool
     */
    protected function shouldCloseTransaction(): bool
    {
        return false;
    }

    /**
     * Whether parent transaction should be closed
     *
     * @since 1.0.0
     *
     * @param Payment $orderPayment
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function shouldCloseParentTransaction(Payment $orderPayment): bool
    {
        return false;
    }
}
