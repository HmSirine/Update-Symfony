<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;




#[Route('/utilisateur')]
class UtilisateurController extends AbstractController
{
    #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $utilisateurs = $entityManager
            ->getRepository(Utilisateur::class)
            ->findAll();

        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Set their role
           $utilisateur->setRole('ROLE_USER');
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }

        private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

     
   /**
     * @Route("/bloquer",name="bloquer")
     */
    public function bloquer(Request $request) {
        var_dump("La méthode bloquer est appelée !");
        die();
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $utilisateur = $em->getRepository("App\Entity\Utilisateur")->find($id);
            $utilisateur->setEnabled(false);
            $em->flush();
            $response = new JsonResponse();
        }
        return $response->setData(array('retour' => $id));
    }

    /**
     * @Route("/debloquer",name="debloquer")
     */
    public function debloquer(Request $request) {
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $utilisateur = $em->getRepository("App\Entity\Utilisateur")->find($id);
            $utilisateur->setEnabled(true);
            $em->flush();
            $response = new JsonResponse();
        }
        return $response->setData(array('retour' => $id));
    }
    
 
    
    #[Route('/', name:'app_utilisateur_index')]
        public function list_students(UtilisateurRepository $repo)
        {
            $utilisateurs =$repo->findByNom();
            return $this->render('utilisateur/index.html.twig',[
                'utilisateurs' => $utilisateurs,
            ]);
        }

       
        
}
