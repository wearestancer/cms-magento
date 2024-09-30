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

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Store\Model\StoreManagerInterface;
use StancerIntegration\Payments\Gateway\Helper\SubjectReader;

class StoreDataBuilder implements BuilderInterface
{
    use Formatter;

    /**
     * Order Store ID Key
     */
    public const STORE_ID = 'storeId';

    /**
     * Order Store Name Key
     */
    public const STORE_NAME = 'storeName';
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var SubjectReader $subjectReader
     */
    private $subjectReader;

    /**
     * Refund StoreDataBuilder Class
     *
     * @since 1.0.0
     *
     * @param SubjectReader $subjectReader
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        SubjectReader         $subjectReader,
        StoreManagerInterface $storeManager
    ) {
        $this->subjectReader = $subjectReader;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $order = $paymentDO->getOrder();

        return [
            self::STORE_ID => $order->getStoreId(),
            self::STORE_NAME => $this->storeManager->getStore($order->getStoreId())->getName(),
        ];
    }
}
