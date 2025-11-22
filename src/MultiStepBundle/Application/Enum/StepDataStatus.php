<?php

namespace App\MultiStepBundle\Application\Enum;

class StepDataStatus
{
    public const DRAFT = 'draft';
    public const DEPOSIT = 'deposit';
    public const AWAITING_REFERENCE = 'awaiting_reference';
    public const PENDING = 'pending';
    public const AWAITING_INFO = 'awaiting_info';
    public const PROVISIONED = 'provisioned';
    public const APPROVED = 'approved';
    public const REFUSED = 'refused';
    public const AWAITING_PAYMENT = 'awaiting_payment';
    public const PAID = 'paid';
    public const BAD_FIRM = 'bad_firm';
    public const CARD_EDITED = 'card_edited';
    public const CARD_DELIVERED = 'card_delivered';
}