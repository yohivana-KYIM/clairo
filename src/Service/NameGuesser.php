<?php

namespace App\Service;

/**
 * Service permettant de deviner prénom, nom, initiales et autres infos
 * à partir d’une adresse email.
 */
class NameGuesser
{
    /**
     * Devine un Nom + Prénom à partir de l’email
     * Exemple : john.doe@example.com → "John Doe"
     */
    public function guessName(string $email): string
    {
        $words = $this->extractWordsFromEmail($email);
        if (empty($words)) {
            return '';
        }
        return mb_convert_case(implode(' ', $words), MB_CASE_TITLE, "UTF-8");
    }

    /**
     * Devine uniquement le prénom (premier mot de l’email)
     * Exemple : john.doe@example.com → "John"
     */
    public function guessFirstName(string $email): string
    {
        $words = $this->extractWordsFromEmail($email);
        return empty($words) ? '' : mb_convert_case($words[0], MB_CASE_TITLE, "UTF-8");
    }

    /**
     * Devine uniquement le nom (dernier mot de l’email)
     * Exemple : john.doe@example.com → "Doe"
     */
    public function guessLastName(string $email): string
    {
        $words = $this->extractWordsFromEmail($email);
        return count($words) < 2 ? '' : mb_convert_case(end($words), MB_CASE_TITLE, "UTF-8");
    }

    /**
     * Devine les initiales à partir de l’email
     * Exemple : john.doe@example.com → "J.D."
     */
    public function guessInitials(string $email): string
    {
        $words = $this->extractWordsFromEmail($email);
        if (empty($words)) {
            return '';
        }
        $initials = array_map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)), $words);
        return implode('.', $initials) . '.';
    }

    /**
     * Devine un nom complet formaté comme "NOM Prénom"
     * Exemple : john.doe@example.com → "DOE John"
     */
    public function guessFormalName(string $email): string
    {
        $first = $this->guessFirstName($email);
        $last  = $this->guessLastName($email);

        if (!$first && !$last) {
            return '';
        }
        return mb_strtoupper($last) . ' ' . $first;
    }

    /**
     * Devine uniquement le domaine de l’email
     * Exemple : john.doe@example.com → "example.com"
     */
    public function guessDomain(string $email): string
    {
        if (empty($email) || !str_contains($email, '@')) {
            return '';
        }
        return strtolower(substr(strrchr($email, "@"), 1));
    }

    /**
     * Méthode interne : transforme la partie locale de l’email
     * en liste de mots utilisables (nettoyage + séparation).
     */
    private function extractWordsFromEmail(string $email): array
    {
        if (empty($email) || !str_contains($email, '@')) {
            return [];
        }

        // Récupère la partie avant le @
        $localPart = strstr($email, '@', true);

        // 1️⃣ Remplace séparateurs typiques (., -, _, +) par des espaces
        $localPart = preg_replace('/[.\-_+]+/', ' ', $localPart);

        // 2️⃣ Coupe les chaînes en CamelCase (ex: JohnDoe → John Doe)
        $localPart = preg_replace('/([a-z])([A-Z])/', '$1 $2', $localPart);

        // 3️⃣ Nettoie les espaces multiples
        $localPart = preg_replace('/\s+/', ' ', trim($localPart));

        // 4️⃣ Découpe en mots
        $words = explode(' ', $localPart);
        $result = [];

        // Liste de préfixes autorisés (ne sont pas filtrés même si courts)
        $allowedPrefixes = ['m', 'mr', 'dr', 'mme', 'mlle', 'madame', 'monsieur'];

        foreach ($words as $word) {
            $word = trim($word);

            // Ignore mots trop courts sauf s’ils sont dans la liste des préfixes
            if (strlen($word) <= 1 && !in_array(strtolower($word), $allowedPrefixes, true)) {
                continue;
            }

            // Ignore purement numérique (ex: john123)
            if (ctype_digit($word)) {
                continue;
            }

            $result[] = $word;
        }

        return $result;
    }
}
