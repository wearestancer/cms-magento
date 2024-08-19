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

class Mode
{
    const TEST = 'test';
    const LIVE = 'live';

    public function toOptionArray()
    {
     /**
     * Get the options in a form of an array
     *
     * @since 1.0.0
     *
     * @return array[]
     */
        return [
            [
                'value' => Mode::TEST,
                'label' => __('Test')
            ],
            [
                'value' => Mode::LIVE,
                'label' => __('Live')
            ],
        ];
    }
}
