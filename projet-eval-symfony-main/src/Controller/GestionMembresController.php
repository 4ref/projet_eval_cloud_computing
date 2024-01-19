<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\AddMembreType;
use App\Form\EditProfileType;
use App\Repository\CommandeRepository;
use App\Repository\MembreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class GestionMembresController extends AbstractController
{
    #[Route('/gestion/membres', name: 'gestion_membres')]
    public function gestion_membre(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager,
        MembreRepository            $membreRepository): Response
    {
        $membres = $membreRepository->findAll();

        # A new membre is automatically registered with DateTime Now.
        $membre = new Membre();
        $membre->setDateEnregistrement(new \DateTime());

        $formValue = $this->createForm(AddMembreType::class, $membre);
        $formValue->handleRequest($request);

        if ($formValue->isSubmitted() && $formValue->isValid()) {
            $membre->setMdp($passwordHasher->hashPassword($membre, $membre->getMdp()));

            $entityManager->persist($membre);
            $entityManager->flush();

            return $this->redirectToRoute('gestion_membres');
        }

        return $this->render('gestion_membres/index.html.twig', [
            'controller_name' => 'GestionMembresController',
            'membres' => $membres,
            'form_value' => $formValue,
        ]);
    }

    #[Route('/gestion/member/delete/{id}', name: 'member_delete')]
    public function deleteMembre(Membre             $membre,
                           MembreRepository   $membreRepository,
                           CommandeRepository $commandeRepository): Response
    {
        $commandeRepository->removeByMemberId($membre->getId());
        $membreRepository->remove($membre);

        // flash()->addSuccess("Membre" . ${$membre->getEmail()} . " à été supprimé(e).");

        return $this->redirectToRoute('gestion_membres');
    }

    #[Route('/gestion/membre/edit/{id}', name: 'edit_membre')]
    public function editMembre(Membre                 $membre,
                         EntityManagerInterface $entityManager,
                         Request                $request)
    {
        $formValue = $this->createForm(EditProfileType::class, $membre);
        $formValue->handleRequest($request);
        if ($formValue->isSubmitted() && $formValue->isValid()) {
            $entityManager->flush();

            flash()->addSuccess('Your changes were saved!');

            return $this->redirectToRoute('gestion_membres');
        }

        return $this->render(
            'gestion_membres/edit_membre.html.twig', [
                'controller_name' => 'GestionMembresController',
                'form_value' => $formValue,
                'membre' => $membre,
            ]
        );
    }
}
