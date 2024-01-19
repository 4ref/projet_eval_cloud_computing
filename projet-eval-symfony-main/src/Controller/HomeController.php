<?php

namespace App\Controller;

use App\Form\HomeType;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(Request            $request,
                         VehiculeRepository $vehiculeRepository): Response
    {
        $formValue = $this->createform(HomeType::class);
        $formValue->handleRequest($request);

        if ($formValue->isSubmitted() && $formValue->isValid()) {
            $dateStart = $formValue->get('date_heure_depart')->getData();
            $dateEnd = $formValue->get('date_heure_fin')->getData();

            // Verify that dateEnd is after dateStart
            if ($dateEnd <= $dateStart) {
                flash()->addError('End date must be after start date.');

                return $this->render('home/index.html.twig', [
                    'controller_name' => 'HomeController',
                    'form_value' => $formValue,
                    'dateStart' => null,
                    'dateEnd' => null,
                ]);
            }

            $vehicules = $vehiculeRepository->findByCommand($dateStart, $dateEnd);

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'form_value' => $formValue,
                'vehicules' => $vehicules,
                'dateStart' => $dateStart,
                'dateEnd' => $dateEnd,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form_value' => $formValue,
            'dateStart' => null,
            'dateEnd' => null,
        ]);
    }
}
