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
    name: 'app:create-multi-step-bundle',
    description: 'Generates a new multi-step bundle structure.'
)]
class CreateMultiStepBundleCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setHelp('This command helps you to create a multi-step form workflow bundle dynamically with all necessary files and structure.')
            ->addArgument('bundle-name', InputArgument::REQUIRED, 'The name of the bundle to be created.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The path where the bundle should be created', 'src/');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bundleName = $input->getArgument('bundle-name');
        $path = rtrim($input->getOption('path'), '/') . '/' . $bundleName;

        $filesystem = new Filesystem();

        try {
            $filesystem->mkdir([$path . '/Controller', $path . '/Command', $path . '/DependencyInjection', $path . '/Resources/config', $path . '/Resources/views', $path . '/Default', $path . '/Domain', $path . '/Application']);

            // Create essential files
            $this->createFile($filesystem, $path . '/MultiStepBundle.php', "<?php\n\nnamespace App\\$bundleName;\n\nuse Symfony\\Component\\HttpKernel\\Bundle\\Bundle;\n\nclass MultiStepBundle extends Bundle\n{\n}");

            $this->createFile($filesystem, $path . '/Command/CreateMultiStepWorkflowCommand.php', "<?php\n\nnamespace App\\$bundleName\\Command;\n\nuse Symfony\\Component\\Console\\Command\\Command;\nuse Symfony\\Component\\Console\\Input\\InputInterface;\nuse Symfony\\Component\\Console\\Output\\OutputInterface;\n\nclass CreateMultiStepWorkflowCommand extends Command\n{\n    protected static \$defaultName = 'app:create-multi-step-workflow';\n\n    protected function configure(): void\n    {\n        \$this\n            ->setDescription('Creates a new multi-step workflow')\n            ->setHelp('This command allows you to create a multi-step workflow dynamically.');\n    }\n\n    protected function execute(InputInterface \$input, OutputInterface \$output): int\n    {\n        \$output->writeln('Multi-step workflow created successfully.');\n        return Command::SUCCESS;\n    }\n}");

            $output->writeln('<info>Multi-step bundle created successfully!</info>');
        } catch (IOExceptionInterface $exception) {
            $output->writeln('<error>Failed to create bundle: ' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function createFile(Filesystem $filesystem, string $filePath, string $content): void
    {
        $filesystem->dumpFile($filePath, $content);
    }
}