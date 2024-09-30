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

namespace StancerIntegration\Payments\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Stancer\Payment;
use StancerIntegration\Payments\Gateway\Helper\SubjectReader;

class ResponseCodeValidator extends AbstractValidator
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Construct ResponseCodeValidator
     *
     * @since 1.0.0
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        SubjectReader          $subjectReader
    ) {
        parent::__construct($resultFactory);
        $this->subjectReader = $subjectReader;
    }

    /**
     * Performs validation of result code
     *
     * @since 1.0.0
     *
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        /** @var Payment $response */
        $response = $this->subjectReader->readTransaction($validationSubject['response']);
        if (!empty($response->getId())) {
            return $this->createResult(true, []);
        } else {
            return $this->createResult(
                false,
                [__('Gateway rejected the transaction.')]
            );
        }
    }
}
