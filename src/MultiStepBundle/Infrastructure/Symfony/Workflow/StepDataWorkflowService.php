<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Workflow;

use App\Entity\User;
use App\MultiStepBundle\Application\Enum\StepDataStatus;
use App\MultiStepBundle\Entity\StepData;
use App\MultiStepBundle\Infrastructure\Symfony\WorkflowMethodEvent;
use App\Service\NameGuesser;
use App\Service\SettingsService;
use App\Service\Workflow\Classes\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class StepDataWorkflowService
{

    public const TRANSITION_SUBMIT = 'submit';
    public const TRANSITION_AWAIT_REFERENCE = 'await_reference';
    public const TRANSITION_START_REVIEW = 'start_review';
    public const TRANSITION_MARK_BAD_FIRM = 'mark_bad_firm';
    public const TRANSITION_REQUEST_MORE_INFO = 'request_more_info';
    public const TRANSITION_PROVIDE_TEMP_ACCESS = 'provide_temp_access';
    public const TRANSITION_REQUEST_INFO = 'request_info';
    public const TRANSITION_PROVISION_ACCESS = 'provision_access';

    public const TRANSITION_APPROVE = 'approve';
    public const TRANSITION_REOPEN_CASE = 'reopen_case';
    public const TRANSITION_NEEDS_CLARIFICATION = 'needs_clarification';
    public const TRANSITION_REJECT = 'reject';
    public const TRANSITION_PREPARE_TECHNICAL_RECORD = 'prepare_technical_record';
    public const TRANSITION_MICROCESAME_ERROR = 'microcesame_error';
    public const TRANSITION_CORRECT_MICROCESAME = 'correct_microcesame';
    public const TRANSITION_LAUNCH_PRELIMINARY_INVESTIGATION = 'launch_preliminary_investigation';
    public const TRANSITION_REEDIT_CARD = 'reedit_card';
    public const TRANSITION_INVESTIGATION_REJECTED = 'investigation_rejected';
    public const TRANSITION_RELAUNCH_INVESTIGATION = 'relaunch_investigation';
    public const TRANSITION_VALIDATE_INVESTIGATION = 'validate_investigation';
    public const TRANSITION_CERBERE_SYNC = 'cerbere_sync';
    public const TRANSITION_CERBERE_FAILURE = 'cerbere_failure';
    public const TRANSITION_RETRY_CERBERE = 'retry_cerbere';
    public const TRANSITION_CERBERE_RETURN = 'cerbere_return';
    public const TRANSITION_REQUEST_PAYMENT = 'request_payment';
    public const TRANSITION_PAYMENT_DOC_FAILED = 'payment_doc_failed';
    public const TRANSITION_CONFIRM_PAYMENT = 'confirm_payment';
    public const TRANSITION_EDIT_CARD = 'edit_card';
    public const TRANSITION_DELIVER_CARD = 'deliver_card';
    public const TRANSITION_AWAITING_REFERENCE = 'await_reference';
    public const RESET_INVESTIGATION = 'reset_investigation';

    public function __construct(
        #[Autowire(service: 'state_machine.step_workflow')]
        private readonly WorkflowInterface $workflow,
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private WorkflowMethodEvent $workflowMethodEvent,
        private readonly NotificationService $notificationService,
        private readonly SettingsService $settingsService,
        private readonly NameGuesser $nameGuesser,
    ) {}

    private function dispatchBefore(string $method, array $params = []): void
    {
        $this->workflowMethodEvent = new WorkflowMethodEvent();
        $this->workflowMethodEvent->setMethodName($method);
        $this->workflowMethodEvent->setParams($params);
        if (array_key_exists('transition', $params)) {
            $this->eventDispatcher->dispatch($this->workflowMethodEvent, "step_data_workflow.$method.before.{$params['transition']}");
        } else {
            $this->eventDispatcher->dispatch($this->workflowMethodEvent, "step_data_workflow.$method.before");
        }
    }

    private function dispatchAfter(string $method, array $params = [], mixed $result = null): void
    {
        $this->workflowMethodEvent = new WorkflowMethodEvent();
        $this->workflowMethodEvent->setMethodName($method);
        $this->workflowMethodEvent->setParams($params);
        if (array_key_exists('transition', $params)) {
            $this->eventDispatcher->dispatch($this->workflowMethodEvent, "step_data_workflow.$method.after.{$params['transition']}");
        } else {
            $this->eventDispatcher->dispatch($this->workflowMethodEvent, "step_data_workflow.$method.after");
        }
    }

    public function advanceToDeposit(StepData $demande): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        $this->applyTransition($demande, self::TRANSITION_SUBMIT);
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande]);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws TransportExceptionInterface
     */
    public function awaitReference(StepData $demande): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        $this->applyTransition($demande, self::TRANSITION_AWAITING_REFERENCE);
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande]);
    }

    public function startInstruction(StepData $demande): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        $this->applyTransition($demande, self::TRANSITION_START_REVIEW);
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande]);
    }

    public function requestAdditionalInfo(StepData $demande): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        $this->applyTransition($demande, self::TRANSITION_REQUEST_INFO);
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande]);
    }

    public function provisionTemporaryAccess(StepData $demande): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        $this->applyTransition($demande, self::TRANSITION_PROVISION_ACCESS);
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande]);
    }

    public function approve(StepData $demande): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        $this->applyTransition($demande, self::TRANSITION_APPROVE);
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande]);
    }

    public function reject(StepData $demande): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        $this->applyTransition($demande, self::TRANSITION_REJECT);
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande]);
    }

    public function editCard(StepData $demande): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        $this->applyTransition($demande, self::TRANSITION_EDIT_CARD);
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function deliverCard(StepData $demande): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        $this->applyTransition($demande, self::TRANSITION_DELIVER_CARD);
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \DateMalformedStringException
     */
    public function applyTransition(StepData $demande, string $transition): void
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande, 'transition' => $transition]);

        if ($this->workflowMethodEvent->isPropagationStopped()) {
            return;
        }
        if (!$this->workflow->can($demande, $transition)) {
            $this->dispatchAfter(__FUNCTION__, ['demande' => $demande, 'transition' => $transition], 'rejected');
            return;
        }

        $this->workflow->apply($demande, $transition);
        $this->entityManager->persist($demande);
        $this->entityManager->flush();

        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande, 'transition' => $transition], 'applied');
        $this->sendTransitionNotification($demande);
    }

    public function getCurrentStatus(StepData $demande): ?string
    {
        $this->dispatchBefore(__FUNCTION__, ['demande' => $demande]);
        $status = $demande->getStatus();
        $this->dispatchAfter(__FUNCTION__, ['demande' => $demande], $status);
        return $status;
    }

    public function getAvailableTransitions(StepData $step): array
    {
        $this->dispatchBefore(__FUNCTION__, ['step' => $step]);
        $transitions = $this->workflow->getEnabledTransitions($step);
        $this->dispatchAfter(__FUNCTION__, ['step' => $step], $transitions);
        return $transitions;
    }

    public function initIfNull(?StepData $stepData = null): void
    {
        $this->dispatchBefore(__FUNCTION__, ['stepData' => $stepData]);

        if ($stepData) {
            if (empty($stepData?->getStatus())) {
                $stepData->setStatus(StepDataStatus::DRAFT);
                $this->entityManager->persist($stepData);
                $this->entityManager->flush();
            }
        }

        $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData]);
    }

    public function isWritable(?StepData $stepData, User $user): bool
    {
        $this->dispatchBefore(__FUNCTION__, ['stepData' => $stepData, 'user' => $user]);

        if ($stepData === null) {
            $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData, 'user' => $user], true);
            return true;
        }

        $status = $stepData->getStatus();
        $roles = $user->getRoles();

/**
        if (in_array('ROLE_ADMIN', $roles, true)) {
            $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData, 'user' => $user], true);
            return true;
        }

        if (array_intersect($roles, ['ROLE_REFSECU', 'ROLE_SDRI'])) {
            $result = in_array($status, [
                StepDataStatus::AWAITING_REFERENCE,
                StepDataStatus::PENDING,
                StepDataStatus::PROVISIONED,
            ], true);
            $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData, 'user' => $user], $result);
            return $result;
        }
*/
        if (in_array('ROLE_USER', $roles, true)) {
            $isOwner = $stepData->getUser()?->getId() === $user->getId();
            $result = $isOwner && in_array($status, [
                    StepDataStatus::DRAFT,
                    StepDataStatus::AWAITING_INFO,
                ], true);
            $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData, 'user' => $user], $result);
            return $result;
        }

        $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData, 'user' => $user], false);
        return false;
    }

    public function isReadable(?StepData $stepData, User $user): bool
    {
        $this->dispatchBefore(__FUNCTION__, ['stepData' => $stepData, 'user' => $user]);

        if ($stepData === null) {
            $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData, 'user' => $user], false);
            return false;
        }

        $status = $stepData->getStatus();
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true) || array_intersect($roles, ['ROLE_REFSECU', 'ROLE_SDRI'])) {
            $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData, 'user' => $user], true);
            return true;
        }

        if (in_array('ROLE_USER', $roles, true)) {
            $readableStatuses = [
                StepDataStatus::DRAFT,
                StepDataStatus::DEPOSIT,
                StepDataStatus::AWAITING_INFO,
                StepDataStatus::PENDING,
                StepDataStatus::APPROVED,
                StepDataStatus::REFUSED,
                StepDataStatus::AWAITING_PAYMENT,
                StepDataStatus::PAID,
                StepDataStatus::CARD_EDITED,
                StepDataStatus::CARD_DELIVERED,
            ];

            $isOwner = $stepData->getUser()?->getId() === $user->getId();
            $result = $isOwner && in_array($status, $readableStatuses, true);

            $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData, 'user' => $user], $result);
            return $result;
        }

        $this->dispatchAfter(__FUNCTION__, ['stepData' => $stepData, 'user' => $user], false);
        return false;
    }

    public function getWorkflowMethodEvent(): WorkflowMethodEvent
    {
        return $this->workflowMethodEvent;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws TransportExceptionInterface
     */
    private function sendTransitionNotification(StepData $demande): void
    {
        $sdriToEmails = null;
        if ($this->settingsService->get('sdri_receive_refsec_email')) {
            $sdriToEmails = explode(',', $this->settingsService->get('sdri_team_emails'));
            $sdriToEmails = reset($sdriToEmails);
        }

        if ($demande->getStepType() !== 'person') return;
        $user = $demande->getUser();
        $employeeEmail = $demande->getInternalData('person_step_two', 'employee_email');
        $referentEmail = $sdriToEmails ?? $demande->getInternalData('person_step_one', 'security_officer_email');

        $entreprise = $user->getEntreprise();
        $suppleant1 = $entreprise?->getSuppleant1();
        $suppleant2 = $entreprise?->getSuppleant2();

        $from = $this->settingsService->get('system_email');
        $stepNumber = $demande->getStepNumber();
        $accessDate = $demande->getInternalData('person_step_one', 'request_date');
        $accessDateFormatted = $accessDate ? (new \DateTime($accessDate))->format('d/m/Y') : '';

        $subjectUser = sprintf('[Demande #%s] - Mise à jour du %s', $stepNumber, $accessDateFormatted);
        $subjectReferent = sprintf('[Demande #%s] - Nouvelle action du %s', $stepNumber, $accessDateFormatted);

        $templateUser = 'emails/person_access_request_user.html.twig';
        $templateReferent = 'emails/person_access_request_referent.html.twig';

        $templateVars = [
            'user' => $user,
            'stepData' => $demande,
            'nameGuesser' =>  $this->nameGuesser
        ];

        if ($employeeEmail) {
            $this->notificationService->sendTemplatedEmail(
                from: $from,
                to: $employeeEmail,
                subject: $subjectUser,
                template: $templateUser,
                templateVars: $templateVars
            );
        }

        if ($referentEmail) {
            $cc = null;
            if ($suppleant1 && $suppleant2) {
                $cc = [$suppleant1, $suppleant2];
            } elseif ($suppleant1) {
                $cc = $suppleant1;
            } elseif ($suppleant2) {
                $cc = $suppleant2;
            }

            $this->notificationService->sendTemplatedEmail(
                from: $from,
                to: $referentEmail,
                subject: $subjectReferent,
                cc: $cc,
                template: $templateReferent,
                templateVars: $templateVars
            );
        }
    }
}
