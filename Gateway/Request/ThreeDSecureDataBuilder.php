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

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use StancerIntegration\Payments\Gateway\Config\Config;
use StancerIntegration\Payments\Gateway\Helper\SubjectReader;

class ThreeDSecureDataBuilder implements BuilderInterface
{
    use Formatter;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * Construct ThreeDSecureDataBuilder Class
     *
     * @since 1.0.0
     *
     * @param Config $config
     * @param SubjectReader $subjectReader
     */
    public function __construct(Config $config, SubjectReader $subjectReader)
    {
        $this->config = $config;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $result = [];
        $amount = $this->formatPrice($this->subjectReader->readAmount($buildSubject));

        if ($this->is3DSecureEnabled($amount)) {
            $result['options']['3dSecure'] = ['required' => true];
        }

        return $result;
    }

    /**
     * Check if 3d secure is enabled
     *
     * @since 1.0.0
     *
     * @param float $amount
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    protected function is3DSecureEnabled($amount): bool
    {
        if ($this->config->is3DSecureActive()
            && ($this->config->is3DSecureAlwaysRequired()
                || $amount >= $this->config->get3DSecureThresholdAmount())) {
            return true;
        }

        return false;
    }
}
