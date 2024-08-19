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

namespace StancerIntegration\Payments\Gateway\Result;

class Sale
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $nonce;

    /**
     * @var string
     */
    private $paymentPageURL;

    /**
     * Construct Sale Class.
     *
     * @since 1.0.0
     *
     * @param string $id The ID of a sale.
     * @param string $nonce The nonce of a sale.
     * @param string $paymentPageURL The payment page URL to be redirected.
     */
    public function __construct(string $id, string $nonce, string $paymentPageURL)
    {
        $this->id = $id;
        $this->nonce = $nonce;
        $this->paymentPageURL = $paymentPageURL;
    }

    /**
     * Get the ID of a sale
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * Get the nonce of this sale
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function getNonce(): string
    {
        return $this->nonce;
    }

    /**
     * Get the payment page url
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function getPaymentPageURL(): string
    {
        return $this->paymentPageURL;
    }
}
