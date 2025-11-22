<?php

namespace App\Security\FileSecurity\Classes;

use App\Security\FileSecurity\Interfaces\FileSecurityCheckerInterface;
use App\Security\FileSecurity\Interfaces\FileSecurityConfigInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Security\FileSecurity\Exception\FileSecurityException as Exception;

class FileSecurityChecker implements FileSecurityCheckerInterface
{

    public function __construct(private readonly FileSecurityConfigInterface $config, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * @throws Exception
     */
    public function validate(array $file): bool
    {
        // 1. Vérification de la taille du fichier
        if ($file['size'] > $this->config->getMaxFileSize()) {
            throw new Exception($this->translator->trans(id: 'files.file_oversize', parameters: ['%filename%' => $file['name']], domain: 'security'));
        }

        // 2. Vérification du type de fichier
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $this->config->getAllowedTypes())) {
            throw new Exception($this->translator->trans(
                id: 'files.unauthorized_file_types',
                parameters: [
                    '%filename%' => $file['name'],
                    '%filetype%' => $fileType,
                    '%types%' => implode(', ', $this->config->getAllowedTypes())
                ],
                domain: 'security'
            ));
        }

        // 3. Vérification des extensions multiples
        if (preg_match('/\.\w+\.\w+$/', (string) $file['name'])) {
            throw new Exception($this->translator->trans(id: 'files.multiples_extensions_denied', parameters: ['%filename%' => $file['name']], domain: 'security'));
        }

        // 4. Analyse antivirus (si configurée)
        $virusScanner = $this->config->getVirusScanner();
        if ($virusScanner && !$virusScanner->scan($file['tmp_name'])) {
            throw new Exception($this->translator->trans(id: 'files.security_issues', parameters: ['%filename%' => $file['name']], domain: 'security'));
        }

        return true;
    }
}