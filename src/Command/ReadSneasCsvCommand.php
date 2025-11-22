<?php

namespace App\Command;

use App\MultiStepBundle\Entity\PersonFlattenedStepData;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:read-sneas-csv',
    description: 'Lit un fichier CSV de criblage SNEAS et met à jour les StepData associés.',
)]
class ReadSneasCsvCommand extends Command
{
    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('file', null, InputOption::VALUE_OPTIONAL, 'Chemin absolu ou relatif vers le fichier CSV à traiter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $customFile = $input->getOption('file');
        $dir = $this->settingsService->get('sneas_data_dir');

        if (!$dir) {
            $io->error('Le paramètre "sneas_data_dir" est introuvable ou vide.');
            return Command::FAILURE;
        }

        $filePath = $customFile
            ? $customFile
            : rtrim($dir, '/') . '/CRIBLAGE_SNEAS.csv';

        if (!file_exists($filePath)) {
            $io->error("Fichier non trouvé : $filePath");

            // Afficher les fichiers disponibles pour aider
            $io->writeln("Fichiers disponibles dans $dir :");
            foreach (scandir($dir) as $f) {
                if (!in_array($f, ['.', '..'])) {
                    $io->writeln(" - $f");
                }
            }

            return Command::FAILURE;
        }

        $io->success("Lecture du fichier : $filePath\n");

        if (($handle = fopen($filePath, 'r')) === false) {
            $io->error('Impossible d’ouvrir le fichier.');
            return Command::FAILURE;
        }

        $lineNumber = 0;
        $updatedCount = 0;
        $notFoundCount = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $lineNumber++;

            // Ignorer les 3 premières lignes
            if ($lineNumber <= 3 || empty(array_filter($row))) {
                continue;
            }

            $employeeFirstName = trim($row[10]);
            $employeeLastName = trim($row[12]);
            $ligneCsv = implode(';', $row);
            $numeroCezar = trim($row[0]);

            if (empty($employeeFirstName) && empty($employeeLastName)) {
                $io->warning("Ligne $lineNumber : Nom et prenom vide. Ignorée.");
                continue;
            }

            $flattenedStepData = $this->em->getRepository(PersonFlattenedStepData::class)->findOneBy([
                'employeeFirstName' => $employeeFirstName,
                'employeeLastName' => $employeeLastName,
            ]);

            if ($flattenedStepData) {
                $stepData = $flattenedStepData->getStepData();
                if (empty($stepData->getCesarStepId())) {
                    $stepData->setCesarStepId($numeroCezar);
                    $stepData->setCesarStepLine($ligneCsv);
                    $this->em->persist($stepData);
                }
                $updatedCount++;
                $io->writeln("✅ StepData mis à jour : $numeroCezar (ligne $ligneCsv)");
            } else {
                $notFoundCount++;
                $io->writeln("⚠️  Aucun StepData trouvé pour : $numeroCezar (ligne $ligneCsv)");
            }
        }

        fclose($handle);
        $this->em->flush();

        $io->success("Mise à jour terminée : $updatedCount modifiés, $notFoundCount introuvables.");
        return Command::SUCCESS;
    }
}
