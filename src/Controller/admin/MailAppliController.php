<?php

namespace App\Controller\admin;

use App\Entity\MailAppli;
use App\Form\MailAppliType;
use App\Repository\MailAppliRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mailappli')]
class MailAppliController extends AbstractController
{
    #[Route('/', name: 'app_mail_appli_index', methods: ['GET'])]
    public function index(MailAppliRepository $mailAppliRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/mail_appli/index.html.twig', [
            'mail_applis' => $mailAppliRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_mail_appli_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $mailAppli = new MailAppli();
        $form = $this->createForm(MailAppliType::class, $mailAppli);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mailAppli);
            $entityManager->flush();

            return $this->redirectToRoute('app_mail_appli_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/mail_appli/new.html.twig', [
            'mail_appli' => $mailAppli,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mail_appli_show', methods: ['GET'])]
    public function show(MailAppli $mailAppli): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/mail_appli/show.html.twig', [
            'mail_appli' => $mailAppli,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mail_appli_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MailAppli $mailAppli, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(MailAppliType::class, $mailAppli);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_mail_appli_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/mail_appli/edit.html.twig', [
            'mail_appli' => $mailAppli,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mail_appli_delete', methods: ['POST'])]
    public function delete(Request $request, MailAppli $mailAppli, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if ($this->isCsrfTokenValid('delete'.$mailAppli->getId(), $request->request->get('_token'))) {
            $entityManager->remove($mailAppli);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mail_appli_index', [], Response::HTTP_SEE_OTHER);
    }
}
