<?php

namespace App\MultiStepBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

#[AsCommand(
    name: 'app:create-multi-step-workflow-command',
    description: 'Generates a new multi-step workflow command class dynamically.'
)]
class CreateMultiStepWorkflowCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setHelp('This command generates a multi-step workflow class structure dynamically for your Symfony project.')
            ->addArgument('workflow-name', InputArgument::REQUIRED, 'The name of the multi-step workflow to be created.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The path where the workflow should be created', 'src/');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflowName = $input->getArgument('workflow-name');
        $path = rtrim($input->getOption('path'), '/') . '/' . $workflowName;

        $filesystem = new Filesystem();

        try {
            // Create the directory structure
            $filesystem->mkdir([$path . '/Controller', $path . '/Form', $path . '/Domain', $path . '/Application']);

            // Generate essential files
            $this->createFile($filesystem, $path . '/Application/' . $workflowName . 'WorkflowService.php', $this->getWorkflowServiceContent($workflowName));
            $this->createFile($filesystem, $path . '/Controller/' . $workflowName . 'Controller.php', $this->getControllerContent($workflowName));

            $output->writeln('<info>Multi-step workflow created successfully!</info>');
        } catch (IOExceptionInterface $exception) {
            $output->writeln('<error>Failed to create workflow: ' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function createFile(Filesystem $filesystem, string $filePath, string $content): void
    {
        $filesystem->dumpFile($filePath, $content);
    }

    private function getWorkflowServiceContent(string $workflowName): string
    {
        return "<?php\n\nnamespace App\\$workflowName\\Application;\n\nuse App\\MultiStepBundle\\Default\\DefaultWorkflowService;\nuse App\\MultiStepBundle\\Default\\DefaultSessionStateManager;\n\nclass {$workflowName}WorkflowService extends DefaultWorkflowService\n{\n    public function __construct(DefaultSessionStateManager \$stateManager, array \$steps)\n    {\n        parent::__construct(\$stateManager, \$steps);\n    }\n}\n";
    }

    private function getControllerContent(string $workflowName): string
    {
        return "<?php\n\nnamespace App\\$workflowName\\Controller;\n\nuse Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController;\nuse Symfony\\Component\\HttpFoundation\\Request;\nuse Symfony\\Component\\HttpFoundation\\Response;\nuse App\\$workflowName\\Application\\{$workflowName}WorkflowService;\n\nclass {$workflowName}Controller extends AbstractController\n{\n    private {$workflowName}WorkflowService \$workflowService;\n\n    public function __construct({$workflowName}WorkflowService \$workflowService)\n    {\n        \$this->workflowService = \$workflowService;\n    }\n\n    public function handle(Request \$request): Response\n    {\n        \$currentStep = \$this->workflowService->getCurrentStep();\n        \$form = \$this->createForm(\$currentStep->getFormType());\n        \$form->handleRequest(\$request);\n\n        if (\$form->isSubmitted() && \$form->isValid()) {\n            \$currentStep->process(\$form);\n            \$this->workflowService->advance();\n\n            if (\$this->workflowService->isComplete()) {\n                return \$this->redirectToRoute('vehicle_access_review');\n            }\n\n            return \$this->redirectToRoute('workflow_handle');\n        }\n\n        return \$this->render('multi_step/step.html.twig', [\n            'form' => \$form->createView(),\n        ]);\n    }\n\n    public function review(Request \$request): Response\n    {\n        \$allData = \$this->workflowService->getAllData();\n\n        return \$this->render('multi_step/review.html.twig', [\n            'all_data' => \$allData,\n        ]);\n    }\n}\n";
    }
}
