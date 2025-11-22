<?php

namespace App\AdminBundle\Infrastructure\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

#[Route('/admin/crud')]
class CrudController extends AbstractController
{
    #[Route('/{entity}', name: 'admin_crud_list', methods: ['GET'])]
    public function list(string $entity, EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository('App\\Entity\\' . ucfirst($entity));
        $items = $repository->findAll();

        return $this->render('@Admin/crud/list.html.twig', [
            'items' => $items,
            'entity' => $entity,
        ]);
    }

    #[Route('/{entity}/new', name: 'admin_crud_new', methods: ['GET', 'POST'])]
    public function new(string $entity, Request $request, EntityManagerInterface $em): Response
    {
        $class = 'App\\Entity\\' . ucfirst($entity);
        if (!class_exists($class)) {
            throw $this->createNotFoundException("Entity '$entity' not found.");
        }

        $object = new $class();
        $form = $this->createForm($class::getFormType(), $object);
        $form->add('save', SubmitType::class, ['label' => 'Create']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($object);
            $em->flush();
            return $this->redirectToRoute('admin_crud_list', ['entity' => $entity]);
        }

        return $this->render('@Admin/crud/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    #[Route('/{entity}/{id}/edit', name: 'admin_crud_edit', methods: ['GET', 'POST'])]
    public function edit(string $entity, int $id, Request $request, EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository('App\\Entity\\' . ucfirst($entity));
        $object = $repository->find($id);

        if (!$object) {
            throw $this->createNotFoundException("$entity with ID $id not found.");
        }

        $form = $this->createForm(get_class($object)::getFormType(), $object);
        $form->add('save', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('admin_crud_list', ['entity' => $entity]);
        }

        return $this->render('@Admin/crud/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    #[Route('/{entity}/{id}/delete', name: 'admin_crud_delete', methods: ['POST'])]
    public function delete(string $entity, int $id, EntityManagerInterface $em, Request $request): Response
    {
        $repository = $em->getRepository('App\\Entity\\' . ucfirst($entity));
        $object = $repository->find($id);

        if (!$object) {
            throw $this->createNotFoundException("$entity with ID $id not found.");
        }

        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            $em->remove($object);
            $em->flush();
        }

        return $this->redirectToRoute('admin_crud_list', ['entity' => $entity]);
    }
}
