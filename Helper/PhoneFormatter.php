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

/**
 * Checkout workflow helper
 */
class PhoneFormatter
{

	/**
	 * Format a phone number following E164 standard.
	 * @param string $phoneNumber A phone number with an unknown format.
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
