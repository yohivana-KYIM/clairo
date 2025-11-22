<?php

namespace App\MultiStepBundle\Application;

use App\MultiStepBundle\Default\DefaultWorkflowService;
use App\MultiStepBundle\Default\DefaultSessionStateManager;

class MultiStepBundleWorkflowService extends DefaultWorkflowService
{
    public function __construct(DefaultSessionStateManager $stateManager, ?array $steps = [])
    {
        parent::__construct($stateManager, $steps);
    }
}
