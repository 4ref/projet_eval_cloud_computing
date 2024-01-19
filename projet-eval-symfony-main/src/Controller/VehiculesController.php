<?php

namespace App\Controller;

use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehiculesController extends AbstractController
{
    #[Route('/vehicules', name: 'vehicules')]
    public function index(\DateTime              $dateStart,
                          \DateTime              $dateEnd,
                          VehiculeRepository     $vehiculeRepository,
                          ): Response
    {
        $vehicules = $vehiculeRepository->findAll();

        return $this->render('vehicules/index.html.twig', [
            'controller_name' => 'VehiculesController',
            'vehicules' => $vehicules,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
        ]);
    }
}
