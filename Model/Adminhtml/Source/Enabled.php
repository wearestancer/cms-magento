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

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Enabled extends AbstractSource
{
    /**
     * Get All the options
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

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
                'value' => 0,
                'label' => __('Disabled'),
            ],
            [
                'value' => 1,
                'label' => __('Enabled'),
            ],
        ];
    }
}
