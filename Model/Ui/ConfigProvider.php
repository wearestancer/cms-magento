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

namespace StancerIntegration\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use StancerIntegration\Payments\Gateway\Config\Config;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'stancer_payments';
    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var Config
     */
    private $config;

    /**
     * Construct ConfigProvider Class.
     *
     * @since 1.0.0
     *
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Config        $config,
        PaymentHelper $paymentHelper,
        UrlInterface  $urlBuilder
    )
    {
        $this->config = $config;
        $this->paymentHelper = $paymentHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @since 1.0.O
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive(),
                    'title' => $this->config->getValue('title'),
                    'mode' => $this->config->getMode(),
                    'flow' => $this->config->getValue('payment_flow'),
                ],
            ]
        ];
    }
}
