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
use StancerIntegration\Payments\Gateway\Helper\SubjectReader;

class AddressDataBuilder implements BuilderInterface
{
    /**
     * ShippingAddress block name
     */
    public const SHIPPING_ADDRESS = 'shipping';

    /**
     * BillingAddress block name
     */
    public const BILLING_ADDRESS = 'billing';

    /**
     * The first name value must be less than or equal to 255 characters.
     */
    public const FIRST_NAME = 'firstName';

    /**
     * The last name value must be less than or equal to 255 characters.
     */
    public const LAST_NAME = 'lastName';

    /**
     * The customer’s company. 255 character maximum.
     */
    public const COMPANY = 'company';

    /**
     * The street address. Maximum 255 characters, and must contain at least 1 digit.
     * Required when AVS rules are configured to require street address.
     */
    public const STREET_ADDRESS = 'streetAddress';

    /**
     * The extended address information such as apartment or suite number. 255 character maximum.
     */
    public const EXTENDED_ADDRESS = 'extendedAddress';

    /**
     * The locality/city. 255 character maximum.
     */
    public const LOCALITY = 'locality';

    /**
     * The state or province.
     */
    public const REGION = 'region';

    /**
     * The postal code. Postal code must be a string of 5 or 9 alphanumeric digits,
     * optionally separated by a dash or a space. Spaces, hyphens,
     * and all other special characters are ignored.
     */
    public const POSTAL_CODE = 'postalCode';

    /**
     * The ISO 3166-1 alpha-2 country code specified in an address.
     */
    public const COUNTRY_CODE = 'countryCodeAlpha2';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Construct AdressDataBuilder Class
     *
     * @since 1.0.0
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $result = [];

        $billingAddress = $order->getBillingAddress();
        if ($billingAddress) {

            $result[self::BILLING_ADDRESS] = [
                self::FIRST_NAME => $billingAddress->getFirstname(),
                self::LAST_NAME => $billingAddress->getLastname(),
                self::COMPANY => $billingAddress->getCompany(),
                self::STREET_ADDRESS => $billingAddress->getStreetLine1(),
                self::EXTENDED_ADDRESS => $billingAddress->getStreetLine2(),
                self::LOCALITY => $billingAddress->getCity(),
                self::REGION => $billingAddress->getRegionCode(),
                self::POSTAL_CODE => $billingAddress->getPostcode(),
                self::COUNTRY_CODE => $billingAddress->getCountryId()
            ];
        }

        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {

            $result[self::SHIPPING_ADDRESS] = [
                self::FIRST_NAME => $shippingAddress->getFirstname(),
                self::LAST_NAME => $shippingAddress->getLastname(),
                self::COMPANY => $shippingAddress->getCompany(),
                self::STREET_ADDRESS =>  $shippingAddress->getStreetLine1(),
                self::EXTENDED_ADDRESS => $shippingAddress->getStreetLine1(),
                self::LOCALITY => $shippingAddress->getCity(),
                self::REGION => $shippingAddress->getRegionCode(),
                self::POSTAL_CODE => $shippingAddress->getPostcode(),
                self::COUNTRY_CODE => $shippingAddress->getCountryId()
            ];
        }

        return $result;
    }
}
