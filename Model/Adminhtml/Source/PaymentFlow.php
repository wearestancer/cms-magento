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

namespace StancerIntegration\Payments\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;
use StancerIntegration\Payments\Model\Config;

class PaymentFlow implements OptionSourceInterface
{
     /**
      * Get the options in a form of an array
      *
      * @since 1.0.0
      *
      * @return array[]
      */
    public function toOptionArray()
    {
        return [
            [
                'value' => Config::PAYMENT_FLOW_REDIRECT,
                'label' => __('Redirect customers to Stancer website.')
            ],
            [
                'value' => Config::PAYMENT_FLOW_IFRAME,
                'label' => __('Embed payment form into the native flow.')
            ],
        ];
    }
}
