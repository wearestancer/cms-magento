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

namespace StancerIntegration\Payments\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use StancerIntegration\Payments\Model\Adminhtml\Source\Mode;
use StancerIntegration\Payments\Model\StoreConfigResolver;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    public const KEY_MODE = 'mode';
    public const KEY_ACTIVE = 'active';
    public const KEY_PUBLIC_KEY = 'public_key';
    public const KEY_PRIVATE_KEY = 'private_key';
    public const KEY_TEST_MODE_PUBLIC_KEY = 'test_public_key';
    public const KEY_TEST_MODE_PRIVATE_KEY = 'test_private_key';
    public const KEY_PAYMENT_FLOW = 'payment_flow';
    public const KEY_3D_SECURE_ENABLED = 'three_ds_enabled';
    public const KEY_3D_SECURE_ALWAYS_REQUEST = 'three_ds_always_request';
    public const KEY_3D_SECURE_THRESHOLD_AMOUNT = 'three_ds_threshold_amount';
    public const KEY_PAYMENT_PAGE_URL = 'payment_page_url';
    public const KEY_PAYMENT_DESCRIPTION = 'payment_description';

    /**
     * @var StoreConfigResolver
     */
    private $storeConfigResolver;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreConfigResolver $storeConfigResolver
     * @param string|null $methodCode
     * @param string $pathPattern
     * @param Json|null $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreConfigResolver  $storeConfigResolver,
        string               $methodCode = null,
        string               $pathPattern = self::DEFAULT_PATH_PATTERN,
        Json                 $serializer = null
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->storeConfigResolver = $storeConfigResolver;
    }

    /**
     * Get Payment configuration status
     *
     * @since 1.0.0
     *
     * @param int|null $storeId
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function isActive(int $storeId = null): bool
    {
        return (bool)$this->getValue(
            self::KEY_ACTIVE,
            $storeId ?? $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Get Public Key
     *
     * @since 1.0.0
     *
     * @return string
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getPublicKey(): string
    {
        return $this->getValue(
            $this->getMode() === Mode::LIVE
                ? self::KEY_PUBLIC_KEY
                : self::KEY_TEST_MODE_PUBLIC_KEY,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Get environment
     *
     * @since 1.0.0
     *
     * @return string
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getMode(): string
    {
        return $this->getValue(
            self::KEY_MODE,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Get Private Key
     *
     * @since 1.0.0
     *
     * @return string
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getPrivateKey(): string
    {
        return $this->getValue(
            $this->getMode() === Mode::LIVE
                ? self::KEY_PRIVATE_KEY
                : self::KEY_TEST_MODE_PRIVATE_KEY,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Get Payment flow configuration
     *
     * @since 1.0.0
     *
     * @param int|null $storeId
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function paymentFlow(int $storeId = null): bool
    {
        return $this->getValue(
            self::KEY_PAYMENT_FLOW,
            $storeId ?? $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Get 3D Secure Status
     *
     * @since 1.0.0
     *
     * @param int|null $storeId
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function is3DSecureActive(int $storeId = null): bool
    {
        return (bool)$this->getValue(
            self::KEY_3D_SECURE_ENABLED,
            $storeId ?? $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Get is 3D Secure Always Required?
     *
     * @since 1.0.0
     *
     * @param int|null $storeId
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function is3DSecureAlwaysRequired(int $storeId = null): bool
    {
        return (bool)$this->getValue(
            self::KEY_3D_SECURE_ALWAYS_REQUEST,
            $storeId ?? $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Get 3D Secure Threshold Amount
     *
     * @since 1.0.0
     *
     * @param int|null $storeId
     * @return double
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function get3DSecureThresholdAmount(int $storeId = null)
    {
        return (double)$this->getValue(
            self::KEY_3D_SECURE_THRESHOLD_AMOUNT,
            $storeId ?? $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Get custom payment description
     *
     * @since 1.0.0
     *
     * @param int|null $storeId
     * @return string
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getPaymentDescription(int $storeId = null)
    {
        return (string)$this->getValue(
            self::KEY_PAYMENT_DESCRIPTION,
            $storeId ?? $this->storeConfigResolver->getStoreId()
        );
    }
}
