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
use App\Form\EstateType;
use Symfony\Component\String\Slugger\SluggerInterface;

class EstateController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine){
        $this->entityManager = $doctrine->getManager();
    }

    #[Route('/', name: 'app_estate')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {

        $estate = new Estate();
        $form_add = $this->createForm(EstateType::class, $estate);

        $form_add->handleRequest($request);

        if($form_add->isSubmitted() && $form_add->isValid()){ // Si on esaie d'ajouter un bien immo
            
            $estateImage = $form_add->get('image')->getData();

            if($estateImage){ // Si une image a été chargée

                $originalFileName = pathinfo($estateImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFilename = $safeFileName.'-'.uniqId().'-'.$estateImage->guessExtension();

                try {
                    $estateImage->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch(FileException $e){
                    dd('Erreur');
                }

                $estate->setImage($newFilename);

            }else{ // Si on a pas chargé d'image
                $estate->setImage('img-default.jpg');
            }
            $this->entityManager->persist($estate);
            $this->entityManager->flush();

        }

        if($request->request->get('delete-estate')){
            $id_estate = $request->request->get('delete-estate');
            $estate = $this->entityManager->getRepository(Estate::class)->find($id_estate);
            $this->entityManager->remove($estate);
            $this->entityManager->flush();
        }

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
            'form_filter' => $form_filter->createView(),
            'form_add' => $form_add->createView()
        ]);
    }
}
