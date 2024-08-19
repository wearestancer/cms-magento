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
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use StancerIntegration\Payments\Controller\RemotePayment;

class Index extends RemotePayment
{
    /**
     * Default method called when we call this controller.
     *
     * @since 1.0.0
     *
     * @return ResponseInterface|ResultInterface
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $paymentId = $this->getPaymentId();
        if (!$paymentId) {
            $this->_redirect('checkout/cart', ['_current' => true]);
        }

        return $this->_redirect($this->getPaymentPageURL());
    }
}
