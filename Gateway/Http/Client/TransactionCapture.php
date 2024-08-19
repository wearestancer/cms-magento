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

use StancerIntegration\Payments\Gateway\Request\CaptureDataBuilder;
use StancerIntegration\Payments\Gateway\Request\PaymentDataBuilder;

class TransactionCapture extends AbstractTransaction
{
    /**
     * @inheritdoc
     */
    protected function process(array $data)
    {
        return $this->adapter->capture(
            $data[CaptureDataBuilder::TRANSACTION_ID],
            $data[PaymentDataBuilder::AMOUNT],
            $data
        );
    }
}
