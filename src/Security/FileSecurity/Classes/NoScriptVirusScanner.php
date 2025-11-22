<?php

namespace App\Security\FileSecurity\Classes;

use App\Security\FileSecurity\Interfaces\VirusScannerInterface;

class NoScriptVirusScanner implements VirusScannerInterface
{

     // Liste des extensions de scripts à détecter
    private const SCRIPT_EXTENSIONS = [
        'php', 'js', 'html', 'htm', 'py', 'sh', 'bat', 'pl', 'rb', 'asp', 'jsp'
    ];

    // Liste des motifs de scripts à détecter dans le contenu
    private const SCRIPT_PATTERNS = [
        '/<\?php/i',                 // Code PHP
        '/<script\b[^>]*>/i',        // Balises <script>
        '/<\?=/i',                   // Code court PHP
        '/\bimport\b|\brequire\b/i', // Imports de modules (JS, Python)
        '/#!\/bin\/bash/i',          // Bash script
        '/#!\/usr\/bin\/env/i',      // Scripts Unix
        '/eval\(/i',                 // Fonction eval
        '/<%\s*@\s*page\b/i',        // Code JSP
        '/system\(|exec\(|shell_exec\(/i', // Exécution de commandes système en PHP
        '/<!--#include/i'             // Server Side Includes (SSI)
    ];

    public function scan(string $filePath): bool
    {
        // 1. Vérification de l'extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (in_array($extension, self::SCRIPT_EXTENSIONS)) {
            // L'extension est celle d'un script
            return false;
        }

        // 2. Vérification du contenu du fichier
        $fileContent = file_get_contents($filePath);
        foreach (self::SCRIPT_PATTERNS as $pattern) {
            if (preg_match($pattern, $fileContent)) {
                // Le contenu contient un script
                return false;
            }
        }

        // Aucune menace détectée
        return true;
    }
}