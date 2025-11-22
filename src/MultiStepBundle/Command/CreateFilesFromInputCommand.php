<?php

namespace App\MultiStepBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

#[AsCommand(
    name: 'app:create-files-from-input',
    description: 'Creates files from an input file containing filenames and contents in a specified destination folder.'
)]
class CreateFilesFromInputCommand extends Command
{

    protected function configure(): void
    {
        $this
            ->addArgument('inputFile', InputArgument::REQUIRED, 'The path to the input file containing filenames and contents.')
            ->addArgument('destinationFolder', InputArgument::REQUIRED, 'The destination folder where files should be created.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();
        $inputFilePath = $input->getArgument('inputFile');
        $destinationFolder = rtrim($input->getArgument('destinationFolder'), '/') . '/';

        if (!file_exists($inputFilePath)) {
            $output->writeln('<error>Input file does not exist: ' . $inputFilePath . '</error>');
            return Command::FAILURE;
        }

        if (!$filesystem->exists($destinationFolder)) {
            $filesystem->mkdir($destinationFolder);
            $output->writeln('<info>Destination folder created: ' . $destinationFolder . '</info>');
        }

        $fileContents = file_get_contents($inputFilePath);
        $files = explode("// ", $fileContents);

        foreach ($files as $file) {
            if (trim($file) === '') {
                continue;
            }

            // Split filename and content
            [$filename, $content] = preg_split("/\n/", $file, 2);

            $filename = trim($filename);
            $content = trim($content);

            $fullPath = $destinationFolder . $filename;

            try {
                $filesystem->dumpFile($fullPath, $content);
                $output->writeln("<info>File created: $fullPath</info>");
            } catch (IOExceptionInterface $exception) {
                $output->writeln("<error>Failed to create file: $fullPath</error>");
            }
        }

        $output->writeln('<info>All files processed successfully!</info>');
        return Command::SUCCESS;
    }
}
