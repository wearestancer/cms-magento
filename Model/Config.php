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
    const MODULE_VERSION = '1.0.2';

    /**
     * Payments flows
     */
    const PAYMENT_FLOW_REDIRECT = 'redirect';

    const PAYMENT_FLOW_IFRAME = 'iframe';
}
