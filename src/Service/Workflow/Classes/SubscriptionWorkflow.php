<?php

namespace App\Service\Workflow\Classes;

class SubscriptionWorkflow
{
    // Workflow name
    public const NAME = 'subscription_workflow';

    // Places (States)
    public const PLACE_REQUEST = 'request';
    public const PLACE_INSCRIPTION = 'inscription';
    public const PLACE_VALIDATED_MAIL = 'validated_mail';
    public const PLACE_NOTIFICATION_UI = 'notification_ui';
    public const PLACE_NOTIFICATION_EMAIL = 'notification_email';
    public const PLACE_VALIDATED_USER = 'validated_user';
    public const PLACE_SUBSCRIBED = 'subscribed';

    // Transitions
    public const TRANSITION_BEGIN_SUBSCRIPTION = 'begin_subscription';
    public const TRANSITION_VALIDATION_MAIL_OK = 'validation_mail_ok';
    public const TRANSITION_VALIDATION_MAIL_KO = 'validation_mail_ko';
    public const TRANSITION_VALIDATION_RESP_OK = 'validation_resp_ok';
    public const TRANSITION_VALIDATION_RESP_KO = 'validation_resp_ko';
    public const TRANSITION_NOTIFICATION_EMAIL_ROLLBACK = 'notification_email_rollback';
    public const TRANSITION_NOTIFICATION_UI_ROLLBACK = 'notification_ui_rollback';
    public const TRANSITION_CONFIRMED_BY_REFSEC = 'subscription_confirmed_by_refsec';
    public const TRANSITION_CONFIRMED_BY_USER = 'subscription_confirmed_by_user';
}
