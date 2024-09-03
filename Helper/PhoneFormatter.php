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

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;

/**
 * Checkout workflow helper
 */
class PhoneFormatter
{

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function updatePhoneNumbers(array $data): array
	{
		$billingCountry = $data['billing']['countryCodeAlpha2'];
		$shippingCountry = $data['shipping']['countryCodeAlpha2'];

		if (isset($data['customer']['phone'])) {
			$data['customer']['phone'] = $this->updatePhoneNumber($data['customer']['phone'], $billingCountry);
		}
		if (isset($data['customer']['mobile'])) {
			$data['customer']['mobile'] = $this->updatePhoneNumber($data['customer']['mobile'], $billingCountry);
		}
		if (isset($data['billing']['phone'])) {
			$data['billing']['mobile'] = $this->updatePhoneNumber($data['customer']['mobile'], $billingCountry);
		}
		if (isset($data['shipping']['phone'])) {
			$data['shipping']['mobile'] = $this->updatePhoneNumber($data['customer']['mobile'], $shippingCountry);
		}

		return $data;
	}

	/**
	 * @param string $phoneNumber
	 * @param string $countryCode
	 *
	 * @return string
	 */
	public function updatePhoneNumber(string $phoneNumber, string $countryCode): string
	{
		$phoneNumberUtil = PhoneNumberUtil::getInstance();
		$phoneNumberObject = $phoneNumberUtil->parse($phoneNumber, $countryCode);
		return $phoneNumberUtil->format($phoneNumberObject, PhoneNumberFormat::E164);
	}
}
