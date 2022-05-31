<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Estate;
use App\classe\Filter;
use App\Form\FilterType;

class EstateController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine){
        $this->entityManager = $doctrine->getManager();
    }

    #[Route('/', name: 'app_estate')]
    public function index(Request $request): Response
    {

        $filter = new Filter();
        $form_filter = $this->createForm(FilterType::class, $filter);

        
        $offset = 0;
        $estate = new Estate();

        $form_filter->handleRequest($request);

        if($form_filter->isSubmitted() && $form_filter->isValid()){

            $estates = $this->entityManager->getRepository(Estate::class)->findByCity($filter);

        }else{
            $estates = $this->entityManager->getRepository(Estate::class)->getEstatePaginator($estate, $offset);
        }

        

        return $this->render('estate/index.html.twig', [
            'estates' => $estates,
            'form_filter' => $form_filter->createView()
        ]);
    }
}
