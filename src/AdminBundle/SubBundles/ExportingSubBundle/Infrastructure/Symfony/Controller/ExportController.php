<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Infrastructure\Symfony\Controller;

use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Message\BulkExportMessage;
use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Message\GeneratePdfMessage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\ExportManager;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/admin')]
class ExportController extends AbstractController
{
    public function __construct(private readonly ExportManager $exportManager, private readonly SerializerInterface $serializer, private readonly ManagerRegistry $doctrine) {}

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    #[Route('/export/{entity}/{format}', name: 'admin_export', methods: ['GET'])]
    public function exportData(
        string $entity,
        string $format,
        Request $request,
        EntityManagerInterface $em,
        MessageBusInterface $bus
    ): Response {
        /**
         * @var array<string, mixed> $options Options dynamiques passées dans l’URL (ex: ?columns[]=name&title=Export)
         */
        $options = $request->query->all();

        // 1. Construire le nom complet de la classe d'entité
        $entityClass = $this->resolveEntityClass($entity);

        if (!class_exists($entityClass)) {
            throw new NotFoundHttpException("L'entité \"$entity\" est introuvable.");
        }

        // 2. Récupérer le repository correspondant
        $repository = $em->getRepository($entityClass);

        if (!method_exists($repository, 'findAll')) {
            throw new RuntimeException("Le repository de $entity ne supporte pas la méthode findAll.");
        }

        // 3. Charger les données
        $entities = $repository->findAll();
        $data = array_map([$this, 'normalizeEntity'], $entities);

        /** @var User $user */
        $user = $this->getUser();
        $bus->dispatch(new GeneratePdfMessage($entity, $data, $options, $user));

        if ($request->query->get('bulk')) {
            $bus->dispatch(new BulkExportMessage($entity, $format, $user, $data));
        }

        // 4. Export via ExportManager
        return $this->exportManager->export($entity, $format, $data, $options);
    }

    /**
     * Convertit une entité Doctrine en tableau associatif (exploitable par les formatters).
     *
     * @param object $entity Instance d'une entité Doctrine.
     * @return array<string, mixed> Données normalisées.
     */
    private function normalizeEntity(object $entity): array
    {
        return $this->serializer->normalize($entity, null, ['groups' => ['export']]);
    }

    /**
     * Résout dynamiquement la classe FQCN d’une entité à partir de son alias URL.
     *
     * @param string $entityAlias Nom court de l'entité (ex: "user" => "App\Entity\User").
     * @return class-string<object>
     * @throws ReflectionException
     */
    private function resolveEntityClass(string $entityAlias): string
    {
        $entityManagers = $this->doctrine->getManagers();

        foreach ($entityManagers as $em) {
            $metadataFactory = $em->getMetadataFactory();
            foreach ($metadataFactory->getAllMetadata() as $metadata) {
                $shortName = (new ReflectionClass($metadata->getName()))->getShortName();
                if (strtolower($shortName) === strtolower($entityAlias)) {
                    return $metadata->getName(); // FQCN
                }
            }
        }

        throw new NotFoundHttpException("Aucune entité trouvée correspondant à : $entityAlias");
    }
}
