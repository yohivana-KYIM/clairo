<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Transliterator;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $plain = 'Passw0rd!';

        $names = [
            ['Amine','Bensaid'], ['Clara','Rossi'], ['Jonas','Schneider'], ['Lina','Haddad'],
            ['Mateo','Fernandez'], ['Salma','Elbaz'], ['Noah','Dubois'], ['Aya','Benali'],
            ['Victor','Moreau'], ['Yara','Rahman'], ['Ethan','Martin'], ['Sofia','Pereira'],
            ['Hugo','Lefevre'], ['Maya','Gonzalez'], ['Rayan','Bakhti'], ['Emma','Carvalho'],
            ['Idris','Ndiaye'], ['Ana','Silva'], ['Nora','Kumar'], ['Louis','Bernard'],
            ['Farah','Saidi'], ['Leo','Fontaine'], ['Milan','Ricci'], ['Camille','Renaud'],
            ['Ibrahim','Traoré'], ['Amina','Soumah'], ['Arthur','Lambert'], ['Mariam','Diallo'],
            ['Quentin','Girard'], ['Stella','Marin'], ['Kylian','Poirier'], ['Imane','Boulahdour'],
            ['Bastien','Roche'], ['Zahra','Alaoui'], ['Yanis','Guerrero'], ['Chiara','Bianchi'],
            ['Ousmane','Sow'], ['Lucie','Gauthier'], ['Nabil','Cherif'], ['Alice','Renard'],
        ];
        $nameIdx = 0;
        $nextName = function() use (&$names, &$nameIdx): array {
            if ($nameIdx >= count($names)) {
                throw new RuntimeException('Add more names in $names to cover all users.');
            }
            return $names[$nameIdx++];
        };

        // ---- Target role sets (same coverage as before) ----
        $roleSets = [
            ['ROLE_USER'],
            ['ROLE_REFSECU'],
            ['ROLE_SDRI'],
            ['ROLE_ADMIN'],
            ['ROLE_GARDIEN'],
            ['ROLE_USER','ROLE_REFSECU'],
            ['ROLE_USER','ROLE_SDRI'],
            ['ROLE_SDRI','ROLE_ADMIN'],
            ['ROLE_USER','ROLE_REFSECU','ROLE_SDRI','ROLE_ADMIN','ROLE_GARDIEN'],
        ];
        // Bulk: 5 per single role
        foreach (['ROLE_USER','ROLE_REFSECU','ROLE_SDRI','ROLE_ADMIN','ROLE_GARDIEN'] as $r) {
            for ($i = 1; $i <= 5; $i++) {
                $roleSets[] = [$r];
            }
        }

        $usedEmails = [];
        foreach ($roleSets as $roles) {
            [$first, $last] = $nextName();
            $email = $this->uniqueEmail($first, $last, $usedEmails);
            $usedEmails[$email] = true;

            $manager->persist($this->makeUser($email, $roles, $plain, $first, $last));
        }

        $manager->flush();
    }

    private function makeUser(string $email, array $roles, string $plain, string $first, string $last): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plain));

        // Names: try usual setters, fallback to setName
        if (method_exists($user, 'setFirstName')) $user->setFirstName($first);
        if (method_exists($user, 'setFirstname')) $user->setFirstname($first);
        if (method_exists($user, 'setLastName'))  $user->setLastName($last);
        if (method_exists($user, 'setLastname'))  $user->setLastname($last);
        if (
            !method_exists($user, 'setFirstName') && !method_exists($user, 'setFirstname') &&
            (method_exists($user, 'setName'))
        ) {
            $user->setName($first.' '.$last);
        }

        if (method_exists($user, 'setIsVerified'))         $user->setIsVerified(true);
        if (method_exists($user, 'setIsReferentVerified')) $user->setIsReferentVerified(true);
        if (method_exists($user, 'setCreatedAt'))          $user->setCreatedAt(new DateTimeImmutable());
        if (method_exists($user, 'setStatus'))             $user->setStatus('inscription');

        return $user;
    }

    private function uniqueEmail(string $first, string $last, array $used): string
    {
        $base = strtolower($this->slug($first) . '.' . $this->slug($last));
        $email = $base . '@gmail.com';
        $n = 2;
        while (isset($used[$email])) {
            $email = $base . $n . '@gmail.com';
            $n++;
        }
        return $email;
    }

    private function slug(string $s): string
    {
        // Try intl transliterator first (best for accents)
        if (class_exists(Transliterator::class)) {
            $t = Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC');
            $s = $t->transliterate($s);
        } else {
            // Fallback using iconv
            $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        }
        // Keep letters/digits only, collapse spaces/dashes, then trim dots
        $s = preg_replace('~[^a-zA-Z0-9]+~', '-', $s);
        $s = trim($s, '-');
        return strtolower($s);
    }

    public static function getGroups(): array
    {
        return ['system-data', 'dev', 'demo'];
    }
}
