<?php

namespace App\Controller;
use App\Entity\Utilisateur;
use App\Form\ForgotPasswordType;
use App\Repository\UtilisateurRepository;
use Swift_Mailer;
use Swift_Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Doctrine\ORM\EntityManagerInterface;



class SecurityController extends AbstractController
{
    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $user = $this->getUser();
        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('app_utilisateur_index');
            } else {
                return $this->redirectToRoute('app_UserPage');
            }
        }
        // var_dump($request->request->get('password'));
       //  die();
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

   

 /**
     * @Route("/forgot", name="forgot")
     */
    public function forgotPassword(Request $request, UtilisateurRepository $utilisateurRepository,Swift_Mailer $mailer, TokenGeneratorInterface  $tokenGenerator)
    {


        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();
    
            $utilisateur = $utilisateurRepository->findOneBy(['adresse' => $donnees]);
            if (!$utilisateur) {
                $this->addFlash('danger', 'Cette adresse n\'existe pas');
                return $this->redirectToRoute("forgot");
            }
    
            $token = $tokenGenerator->generateToken();

            try{
                $utilisateur->setResetToken($token);
                $entityManager = $this->entityManager;
                $entityManager->persist($utilisateur);
                $entityManager->flush();




            }catch(\Exception $exception) {
                $this->addFlash('warning','une erreur est survenue :'.$exception->getMessage());
                return $this->redirectToRoute("app_login");


            }

            $url = $this->generateUrl('app_reset_password',array('token'=>$token),UrlGeneratorInterface::ABSOLUTE_URL);

            //BUNDLE MAILER
            $message = (new Swift_Message('Mot de password oublié'))
                ->setFrom('travolta.voyage@gmail.com')
                ->setTo($utilisateur->getAdresse())
                ->setBody("<p> Bonjour</p> unde demande de réinitialisation de mot de passe a été effectuée. Veuillez cliquer sur le lien suivant :".$url,
                "text/html");

            //send mail
            $mailer->send($message);
            $this->addFlash('message','E-mail  de réinitialisation du mp envoyé :');
        //    return $this->redirectToRoute("app_login");



        }

        return $this->render("security/forgotPassword.html.twig",['form'=>$form->createView()]);
    }

    /**
     * @Route("/resetpassword/{token}", name="app_reset_password")
     */
    public function resetpassword(Request $request,string $token, UserPasswordEncoderInterface  $passwordEncoder)
    {
        $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['reset_token'=>$token]);

        if($utilisateur == null ) {
            $this->addFlash('danger','TOKEN INCONNU');
            return $this->redirectToRoute("app_login");

        }

        if($request->isMethod('POST')) {
            $utilisateur->setResetToken(null);

            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur,$request->request->get('password')));
            $entityManager = $this->entityManager;
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->addFlash('message','Mot de passe mis à jour :');
            return $this->redirectToRoute("app_login");

        }
        else {
            return $this->render("security/resetPassword.html.twig",['token'=>$token]);

        }
    }
    
    
}
