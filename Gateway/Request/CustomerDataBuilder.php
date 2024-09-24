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
use StancerIntegration\Payments\Helper\PhoneFormatter;

class CustomerDataBuilder implements BuilderInterface
{
    /**
     * Customer block name
     */
    public const CUSTOMER = 'customer';

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
     * The customer’s email address, comprised of ASCII characters.
     */
    public const EMAIL = 'email';

    /**
     * Phone number.
     *
     * Phone must be 10-14 characters and can only contain numbers, dashes, parentheses, plus sign and periods.
     * It must be internationalized (e.g +33123456789)
     */
    public const PHONE = 'phone';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var PhoneFormatter
     * @since 1.0.2
     */
    private $phoneFormatter;

    /**
     * Construct CustomerDataBuilder Class
     *
     * @since 1.0.0
     * @since 1.0.2 Add PhoneFormatter
     *
     * @param SubjectReader $subjectReader
     * @param PhoneFormatter $phoneFormatter
     */
    public function __construct(SubjectReader $subjectReader, PhoneFormatter $phoneFormatter)
    {
        $this->subjectReader = $subjectReader;
        $this->phoneFormatter = $phoneFormatter;
    }

    /**
     * @inheritdoc
     *
     * @since 1.0.2 Phone format
     */
    public function build(array $buildSubject): array
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $billingAddress = $order->getBillingAddress();

        $phone = $this->phoneFormatter->updatePhoneNumber(
            $billingAddress->getTelephone(),
            $billingAddress->getCountryId()
        );

        return [
            self::CUSTOMER => [
                self::FIRST_NAME => $billingAddress->getFirstname(),
                self::LAST_NAME => $billingAddress->getLastname(),
                self::COMPANY => $billingAddress->getCompany(),
                self::PHONE => $phone,
                self::EMAIL => $billingAddress->getEmail(),
            ]
        ];
    }
}
