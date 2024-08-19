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

namespace StancerIntegration\Payments\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;

/**
 * Information to display to customer and administrator alike
 *
 * @since 1.0.0
 */
class Info extends ConfigurableInfo
{
    /**
     * Get the title of our module (stancer)
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getMethod()->getTitle();
    }

    /**
     * Returns label
     *
     * @since 1.0.0
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field)
    {
        return __($field);
    }
}
