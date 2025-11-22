Install sur serveur accueilSecu

wget https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.focal_amd64.deb

sudo apt install ./wkhtmltox_0.12.6-1.focal_amd64.deb

À partir de l’adresse <https://computingforgeeks.com/install-wkhtmltopdf-on-ubuntu-debian-linux/>

On contrôle la version :

wkhtmltopdf -V

wkhtmltopdf 0.12.6 (with patched qt) qt est le moteur de rendu webkit

Test de l'impression

wkhtmltopdf http://google.com google.pdf

Les options possible
https://wkhtmltopdf.org/usage/wkhtmltopdf.txt

À partir de l’adresse <https://wkhtmltopdf.org/>

Probleme librairie  libssl1.1
wget http://archive.ubuntu.com/ubuntu/pool/main/o/openssl/libssl1.1_1.1.1f-1ubuntu2_amd64.deb

sudo dpkg -i libssl1.1_1.1.1f-1ubuntu2_amd64.deb

À partir de l’adresse <https://askubuntu.com/questions/1403619/mongodb-install-fails-on-ubuntu-22-04-depends-on-libssl1-1-but-it-is-not-insta>

---

## Préparation à la montée de version vers Symfony 6.4

Pour garantir que le projet reste fonctionnel après une mise à jour de Symfony 6.1.11 vers Symfony 6.4, voici les étapes nécessaires :

### 1. Vérifiez les déprecations
Exécutez la commande suivante pour identifier les fonctionnalités dépréciées utilisées dans votre projet :
```bash
php bin/console debug:deprecations
```
Corrigez toutes les dépréciations signalées avant de monter en version.

---

### 2. Mettez à jour vos dépendances
Exécutez la commande suivante pour mettre à jour toutes les dépendances compatibles avec Symfony 6.4 :
```bash
composer update --with-all-dependencies
```
Vérifiez la compatibilité des bibliothèques suivantes :
- **Twig** : Assurez-vous d'utiliser `twig/twig:^3.7`.
- **Doctrine** : Mettez à jour `doctrine/orm`, `doctrine/migrations`, et autres extensions Doctrine.
- **Monolog** : Vérifiez que `monolog/monolog:^3` est compatible.

---

### 3. Validez vos fichiers de configuration
Effectuez une vérification syntaxique des fichiers YAML :
```bash
php bin/console lint:yaml config/
```

Assurez-vous que vos fichiers de configuration ne contiennent pas d'options ou de paramètres obsolètes.

---

### 4. Vérifiez vos routes
Exécutez la commande suivante pour détecter tout problème dans vos définitions de routes :
```bash
php bin/console debug:router
```

Assurez-vous que toutes les annotations de routes (ou définitions YAML/PHP) suivent les bonnes pratiques actuelles.

---

### 5. Formulaires Symfony
Assurez-vous que tous vos types de formulaire respectent les évolutions des classes et méthodes de Symfony 6.4. Attention particulièrement aux méthodes comme `buildForm` et `getParent`.

---

### 6. Tests unitaires
Si vous utilisez PHPUnit, mettez-le à jour vers une version compatible PHP 8.1 et Symfony 6.4 :
```bash
composer require --dev phpunit/phpunit:^10
```
Révisez vos tests pour éviter d'utiliser des fonctionnalités obsolètes qui pourraient causer des échecs après la mise à jour.

---

### 7. Validation des traductions
Si vous utilisez des fichiers de traduction, exécutez l'une des commandes suivantes pour identifier d'éventuelles erreurs :
```bash
php bin/console lint:xliff translations/
php bin/console lint:yaml translations/
```

---

### 8. Sécurité et services
- Revoyez votre fichier `security.yaml` et les services personnalisés pour vous assurer qu'ils respectent les évolutions de Symfony 6.4.
- Vérifiez les signatures des méthodes dans vos abonnements aux événements (`EventSubscriberInterface`) et commandes Console.

---

### Mise à jour finale :
Lorsque tout est prêt, procédez à la mise à jour :
```bash
composer require symfony/symfony:^6.4 --with-all-dependencies
```
Testez soigneusement votre application après la mise à jour.

---

## Remarques
Il est recommandé d'utiliser un environnement de test ou de staging pour effectuer cette mise à jour avant de la déployer en production. Cela permettra de détecter toute régression ou problème lié à des modifications majeures introduites dans la version 6.4.

```sql
ALTER TABLE step_data
ADD COLUMN user_id INT NOT NULL;
ALTER TABLE step_data ADD CONSTRAINT fk_step_user FOREIGN KEY (user_id) REFERENCES user(id);
```# clairo
