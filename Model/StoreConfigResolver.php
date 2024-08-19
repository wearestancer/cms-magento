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

use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\StoreManagerInterface;

class StoreConfigResolver
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RequestHttp
     */
    protected $request;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var SessionQuote
     */
    protected $sessionQuote;

    /**
     * Construct StoreConfigResolver Class.
     *
     * @since 1.0.0
     *
     * @param StoreManagerInterface $storeManager StoreManager
     * @param RequestHttp $request HTTP request
     * @param OrderRepository $orderRepository Order repository
     * @param SessionQuote $sessionQuote Session quote
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        RequestHttp           $request,
        OrderRepository       $orderRepository,
        SessionQuote          $sessionQuote
    )
    {
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->sessionQuote = $sessionQuote;
    }

    /**
     * Get store id for config values
     *
     * @since 1.0.0
     *
     * @return int|null
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        $currentStoreId = null;
        $currentStoreIdInAdmin = $this->sessionQuote->getStoreId();
        if (!$currentStoreIdInAdmin) {
            $currentStoreId = $this->storeManager->getStore()->getId();
        }
        $dataParams = $this->request->getParams();
        if (isset($dataParams['order_id'])) {
            $order = $this->orderRepository->get($dataParams['order_id']);
            if ($order->getEntityId()) {
                return $order->getStoreId();
            }
        }

        return $currentStoreId ?: $currentStoreIdInAdmin;
    }
}
