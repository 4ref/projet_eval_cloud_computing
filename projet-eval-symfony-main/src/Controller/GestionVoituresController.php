<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\AddVehiculeType;
use App\Form\EditVehiculeType;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class GestionVoituresController extends AbstractController
{
    #[Route('/gestion/vehicules', name: 'gestion_vehicules')]
    public function gestion_voitures(Request                $request,
                                     EntityManagerInterface $entityManager,
                                     VehiculeRepository     $vehiculeRepository,
                                     SluggerInterface       $slugger): Response
    {
        $vehicules = $vehiculeRepository->findAll();

        $vehicule = new Vehicule();
        $vehicule->setDateEnregistrement(new \DateTime());

        $formValue = $this->createForm(AddVehiculeType::class, $vehicule);
        $formValue->handleRequest($request);

        if ($formValue->isSubmitted() && $formValue->isValid()) {

            $file = $formValue->get('photo')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('vehicules_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $vehicule->setPhoto($newFilename);
            }

            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('gestion_vehicules');
        }

        return $this->render('gestion_vehicules/index.html.twig', [
            'controller_name' => 'GestionVehiculesController',
            'vehicules' => $vehicules,
            'form_value' => $formValue,
        ]);
    }

    #[Route('/gestion/vehicule/delete/{id}', name: 'vehicule_delete')]
    public function deleteVehicule(Vehicule           $vehicule,
                                   VehiculeRepository $vehiculeRepository,
                                   CommandeRepository $commandeRepository): Response
    {
        $commandeRepository->removeByVehiculeId($vehicule->getId());
        $vehiculeRepository->remove($vehicule);

        return $this->redirectToRoute('gestion_vehicules');
    }

    #[Route('/gestion/vehicule/edit/{id}', name: 'edit_vehicule')]
    public function editVehicule(Vehicule               $vehicule,
                                 EntityManagerInterface $entityManager,
                                 Request                $request,
                                 SluggerInterface       $slugger)
    {
        $formValue = $this->createForm(EditVehiculeType::class, $vehicule);
        $formValue->handleRequest($request);
        if ($formValue->isSubmitted() && $formValue->isValid()) {
            $file = $formValue->get('photo')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('vehicules_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $vehicule->setPhoto($newFilename);
            }

            $entityManager->flush();

            flash()->addSuccess('Your changes were saved!');

            return $this->redirectToRoute('gestion_vehicules');
        }

        return $this->render(
            'gestion_vehicules/edit_vehicule.html.twig', [
                'controller_name' => 'GestionVehiculeController',
                'form_value' => $formValue,
                'vehicule' => $vehicule,
            ]
        );
    }
}
