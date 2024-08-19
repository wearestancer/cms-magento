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

namespace StancerIntegration\Payments\Controller\Redirect;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use StancerIntegration\Payments\Controller\RemotePayment;

class Iframe extends RemotePayment
{
    /**
     * Method that is called when the controller is called.
     *
     * @since 1.0.0
     *
     * @return ResponseInterface|Json|ResultInterface
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $paymentId = $this->getPaymentId();
        if (!$paymentId) {
            $resultJson->setData([
                'error' => __('Failed to load the payment page, please try again and make sure the order placed.'),
            ]);
            $resultJson->setStatusHeader(400);
        } else {
            $resultJson->setData([
                'url' => $this->getPaymentPageURL(),
            ]);
        }

        return $resultJson;
    }
}
