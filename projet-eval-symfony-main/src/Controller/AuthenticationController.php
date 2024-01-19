<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\AuthenticationType;
use App\Form\SignupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    #[Route('/connexion', name: 'connexion', methods: ['GET', 'POST'])]
    public function connexion(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        if($error != null){
            flash()->addError("Pseudo ou Mot de passe incorrect.");
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('authentication/index.html.twig', [
            'controller_name' => 'AuthenticationController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/signup', name: 'signup')]
    public function signup(Request                     $request,
                           UserPasswordHasherInterface $passwordHasher,
                           EntityManagerInterface      $entityManager): Response
    {
        # A new membre is automatically registered with DateTime Now.
        $membre = new Membre();
        $membre->setDateEnregistrement(new \DateTime());

        $formValue = $this->createForm(SignupType::class, $membre);
        $formValue->handleRequest($request);

        if ($formValue->isSubmitted() && $formValue->isValid()) {
            $membre->setMdp($passwordHasher->hashPassword($membre, $membre->getMdp()));

            $entityManager->persist($membre);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('sign_up/index.html.twig', [
            'controller_name' => 'AuthenticationController',
            'form_value' => $formValue,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception("Don't forget to activate logout in security.yaml");
    }
}
