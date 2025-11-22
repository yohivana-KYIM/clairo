<?php

namespace App\Service\Workflow\Classes;

use App\Entity\User;
use App\Service\Workflow\Interfaces\SubscriptionWorkflowInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Workflow\WorkflowInterface;

class SubscriptionWorkflowService implements SubscriptionWorkflowInterface
{

    public function __construct(
        #[Autowire(service: 'state_machine.subscription_workflow')]
        private readonly WorkflowInterface $subscriptionWorkflowStateMachine
    )
    {
    }

    public function startSubscription(User $user): void
    {
        $this->applyTransition($user, SubscriptionWorkflow::TRANSITION_BEGIN_SUBSCRIPTION);
    }

    public function validateMail(User $user, bool $isValid): void
    {
        $transition = $isValid ? SubscriptionWorkflow::TRANSITION_VALIDATION_MAIL_OK : SubscriptionWorkflow::TRANSITION_VALIDATION_MAIL_KO;
        $this->applyTransition($user, $transition);
    }

    public function validateUser(User $user, bool $isValid): void
    {
        $transition = $isValid ? SubscriptionWorkflow::TRANSITION_VALIDATION_RESP_OK: SubscriptionWorkflow::TRANSITION_VALIDATION_RESP_KO;
        $this->applyTransition($user, $transition);
    }

    public function confirmSubscription(User $user, bool $byUser): void
    {
        $transition = $byUser ? SubscriptionWorkflow::TRANSITION_CONFIRMED_BY_USER : SubscriptionWorkflow::TRANSITION_CONFIRMED_BY_REFSEC;
        $this->applyTransition($user, $transition);
    }

    private function applyTransition(User $user, string $transition): void
    {
        if ($this->subscriptionWorkflowStateMachine->can($user, $transition)) {
            $this->subscriptionWorkflowStateMachine->apply($user, $transition);
        }
    }
}