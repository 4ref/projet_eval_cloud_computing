<?php

namespace App\Controller;

use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request                $request,
                          EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $user = $this->getUser();
        $formValue = $this->createform(EditProfileType::class, $user);
        $formValue->handleRequest($request);

        if ($formValue->isSubmitted() && $formValue->isValid()) {
            $entityManager->flush();

            flash()->addSuccess('Your changes were saved!');

            return $this->redirectToRoute('home');
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'form_value' => $formValue
        ]);
    }
}
