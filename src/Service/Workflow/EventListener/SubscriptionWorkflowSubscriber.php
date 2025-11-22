<?php

namespace App\Service\Workflow\EventListener;

use App\Entity\User;
use App\Service\Workflow\Classes\SubscriptionWorkflow;
use App\Service\Workflow\Interfaces\NotificationServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\LeaveEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\WorkflowEvents;

class SubscriptionWorkflowSubscriber implements EventSubscriberInterface
{
    private readonly NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkflowEvents::ENTER => 'onEnter',
            WorkflowEvents::LEAVE => 'onLeave',
            WorkflowEvents::TRANSITION => 'onTransition',
        ];
    }

    public function onEnter(EnterEvent $event): void
    {
        /**
         * @var User $subject
         */
        $subject = $event->getSubject();
        if ($subject instanceof User) {
            if ($event->getWorkflowName() === SubscriptionWorkflow::NAME
                && in_array(SubscriptionWorkflow::PLACE_VALIDATED_MAIL, array_keys($event->getMarking()->getPlaces()), true)) {

                $this->notificationService->sendUINotification($subject);
            }
        }
    }

    public function onLeave(LeaveEvent $event): void
    {
        $subject = $event->getSubject();
        if ($subject instanceof User) {
            if ($event->getWorkflowName() === SubscriptionWorkflow::NAME) {
                // Handle logic on leaving a state, if necessary
            }
        }
    }

    public function onTransition(TransitionEvent $event): void
    {
        $subject = $event->getSubject();
        if ($subject instanceof User) {
            if ($event->getWorkflowName() === SubscriptionWorkflow::NAME) {
                $transitionName = $event->getTransition()->getName();

                // Perform actions on specific transitions
                if ($transitionName === SubscriptionWorkflow::TRANSITION_VALIDATION_MAIL_KO) {
                    $this->notificationService->sendEmailNotification($subject);
                }
            }
        }
    }
}
