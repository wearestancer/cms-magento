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

namespace StancerIntegration\Payments\Helper;

use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;

/**
 * Checkout workflow helper
 */
class Checkout
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * Construct Checkout Class.
     *
     * @since 1.0.0
     *
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * Cancel last placed order with specified comment message
     *
     * @since 1.0.0
     *
     * @param string $comment Comment appended to order history
     * @return bool True if order cancelled, false otherwise
     */
    public function cancelCurrentOrder($comment)
    {
        $order = $this->session->getLastRealOrder();
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }

    /**
     * Restores quote
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function restoreQuote()
    {
        return $this->session->restoreQuote();
    }
}
