<?php

namespace App\Controller;
use App\Entity\Reclamation;
use App\Form\ReclamationType;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/contact', name: 'app_Contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/contact', name: 'app_Contact', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('app_Contact', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Home/contact.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    


    #[Route('/UserPage', name: 'app_UserPage')]
    public function UserPage(): Response
    {
        return $this->render('home/user.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/Admin', name: 'app_AdminPage')]
    public function AdminPage(EntityManagerInterface $entityManager): Response
{
            $utilisateurs = $entityManager
            ->getRepository(Utilisateur::class)
            ->findAll();
    {
        return $this->render('home/admin.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }



}
}

