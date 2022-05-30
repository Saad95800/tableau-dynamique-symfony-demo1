<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Estate;

class EstateController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine){
        $this->entityManager = $doctrine->getManager();
    }

    #[Route('/', name: 'app_estate')]
    public function index(): Response
    {

        $estates = $this->entityManager->getRepository(Estate::class)->findAll();

        return $this->render('estate/index.html.twig', [
            'estates' => $estates,
        ]);
    }
}
