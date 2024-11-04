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

namespace StancerIntegration\Payments\Model;

class Config
{
    public const MODULE_VERSION = '1.1.0';

    /**
     * Payments flows
     */
    public const PAYMENT_FLOW_REDIRECT = 'redirect';

    public const PAYMENT_FLOW_IFRAME = 'iframe';
}
