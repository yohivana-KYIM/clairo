# MultiStepBundle

Le **MultiStepBundle** est une implémentation Symfony conçue pour gérer des processus multi-étapes interactifs. Ce bundle est parfait pour les formulaires avancés, les workflows ou toute situation où des étapes successives nécessitent un suivi cohérent des données. Il est flexible et facilement extensible, permettant une gestion simplifiée des étapes, des validations et des transitions.

---

## Table des matières

- [Caractéristiques principales](#caractéristiques-principales)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
    - [Gestion des étapes](#gestion-des-étapes)
    - [Contrôleurs principaux](#contrôleurs-principaux)
    - [Templates personnalisés](#templates-personnalisés)
- [Entités principales](#entités-principales)
- [Extension et personnalisation](#extension-et-personnalisation)
- [Exemple](#exemple)
- [Contributions](#contributions)
- [Licence](#licence)

---

## Caractéristiques principales

- **Gestion des workflows multi-étapes** :
  Suivez les transitions, validations et données utilisateur en toute simplicité.

- **Support des formulaires Symfony** :
  Utilisez des types de formulaire Symfony pour la gestion des données étape par étape.

- **Personnalisable** :
  Chaque étape peut avoir ses propres paramètres, modèles, et validations.

- **Support de persistance** :
  Enregistrez les données des utilisateurs à chaque étape ou une fois le processus terminé.

- **Gestion du retour en arrière** :
  Permet aux utilisateurs de revenir à une étape précédente et de réviser les données fournies.

---

## Installation

1. Ajoutez le bundle dans votre projet Symfony via Composer :

   ```bash
   composer require app/multi-step-bundle
   ```

2. Assurez-vous d’enregistrer le bundle dans votre fichier de configuration Symfony, si ce n’est pas déjà fait.

3. Publiez les fichiers de configuration nécessaires si requis.

---

## Configuration

Une configuration minimale est fournie par défaut, mais vous pouvez la personnaliser en utilisant les fichiers de configuration Symfony.

### Exemple de configuration :

Ajoutez dans `config/packages/multi_step.yaml` :

```yaml
multi_step:
    workflow:
        steps:
            - { name: "Étape 1", form_type: App\Form\StepOneForm }
            - { name: "Étape 2", form_type: App\Form\StepTwoForm }
            - { name: "Review" }
        persist: true            # Indique si les données doivent être enregistrées
        templates:
            step_template: "@MultiStepBundle/step.html.twig"
            review_template: "@MultiStepBundle/review.html.twig"
```

---

## Utilisation

### Gestion des étapes

1. **Définir les étapes :**
   Chaque étape du workflow correspond à un formulaire ou à une action spécifique (comme *Review*).

2. **Créer les formulaires :**
   Créez des types de formulaire Symfony pour chaque étape, par exemple :

   ```php
   namespace App\Form;

   use Symfony\Component\Form\AbstractType;
   use Symfony\Component\Form\FormBuilderInterface;
   use Symfony\Component\OptionsResolver\OptionsResolver;

   class StepOneForm extends AbstractType
   {
       public function buildForm(FormBuilderInterface $builder, array $options)
       {
           $builder
               ->add('field_one')
               ->add('field_two');
       }

       public function configureOptions(OptionsResolver $resolver)
       {
           $resolver->setDefaults([]);
       }
   }
   ```

3. **Créer un service de gestion des workflows** :
   Implémentez la logique des transitions et la gestion des états des étapes.

### Contrôleurs principaux

`VehicleAccessRequestController` (par défaut) contient les actions principales du Bundle :

- `handle(Request $request)` : Gère l’affichage et la validation des étapes.
- `goBack()` : Permet de revenir à l’étape précédente dans le workflow.
- `review(Request $request)` : Gère la phase de révision où l’utilisateur peut revoir toutes les données envoyées.
- `persist(Request $request)` : Persiste les données du formulaire dans une base de données ou un stockage temporaire.

Ajoutez les routes nécessaires dans votre fichier routes YAML ou PHP. Exemple minimal :

```yaml
# config/routes.yaml
vehicle_access_request:
    path: /vehicle-access
    controller: App\MultiStepBundle\Controller\VehicleAccessRequestController::handle
```

### Templates personnalisés

Les vues par défaut peuvent être surchargées dans votre dossier `templates/`.

- **Vue pour une étape :** `@MultiStepBundle/step.html.twig`
- **Vue de révision :** `@MultiStepBundle/review.html.twig`

Voici un exemple de fichier Twig pour une étape (`step.html.twig`) :

```twig
{% extends 'base.html.twig' %}

{% block body %}
    <h1>Étape : {{ current_step }}</h1>

    {{ form_start(form) }}
        {{ form_widget(form) }}
        <button type="submit">Continuer</button>
    {{ form_end(form) }}
{% endblock %}
```

---

## Entités principales

### `VehicleAccessWorkflowService`

Service principal de gestion du workflow, utilisable pour :

- Déterminer l'étape actuelle (`getCurrentStep()`).
- Charger les données de session/stocker des données (`loadData()`, `saveData()`).
- Avancer ou reculer à l'intérieur du workflow (`advance()`, `goBack()`).
- Générer des boutons/indicateurs pour guider l'utilisateur.

---

## Extension et personnalisation

1. Ajoutez des étapes supplémentaires en modifiant la configuration du workflow.
2. Implémentez des validations spécifiques à chaque étape via les types de formulaire Symfony.
3. Personnalisez le comportement du workflow en modifiant les méthodes dans `VehicleAccessWorkflowService`.

---

## Exemple

Un scénario simple d’utilisation du MultiStepBundle :

1. Le client démarre une demande d'accès au véhicule.
2. Il entre ses informations personnelles à l'étape 1.
3. Renseigne les détails du véhicule à l'étape 2.
4. Revoit et valide ses informations.
5. Les données sont enregistrées en base lorsque le workflow est complété.

---

## Contributions

Les contributions au **MultiStepBundle** sont les bienvenues ! Veuillez soumettre une *Pull Request* ou signaler un problème via GitHub.

---

## Licence

Le bundle est publié sous licence [MIT](https://opensource.org/licenses/MIT). Vous êtes libre de l'utiliser et de le modifier selon vos besoins.