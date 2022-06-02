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

        $current_page = 1;
        $prev_page = 1;
        $next_page = 2;

        if($request->request->get('prev')){ // Si on a cliqué sur le bouton de pagination prev
            $current_page = (int)$request->request->get('prev');
            $prev_page = ($current_page - 1 == 0) ? 1 : $current_page - 1;
            $next_page = $current_page + 1;
        }
        if($request->request->get('next')){ // Si on a cliqué sur le bouton de pagination next
            $current_page = (int)$request->request->get('next');
            $prev_page = $current_page - 1;
            $next_page = $current_page + 1;
        }
        // dump($request->request->get('delete-estate'));
        if($request->request->get('delete-estate')){ // Si on a cliqué sur un bouton supprimer
            $id_estate = (int)$request->request->get('delete-estate');
            $estate = $this->entityManager->getRepository(Estate::class)->find($id_estate);
            // dd($estate);
            $this->entityManager->remove($estate);
            $this->entityManager->flush();
        }

        $offset = 20 * ($current_page - 1);

        $estate = new Estate();
        
        $form_add = $this->createForm(EstateType::class, $estate);

        $form_add->handleRequest($request);
        if($form_add->isSubmitted() && $form_add->isValid()){ // Si on essaie d'ajouter un bien immo
            $estateImage = $form_add->get('image')->getData();

            if ($estateImage) {

                $originalFilename = pathinfo($estateImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$estateImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $estateImage->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $estate->setImage($newFilename);

            }else{
                $estate->setImage('img-default.jpg');
            }
            $this->entityManager->persist($estate);
            $this->entityManager->flush();

        }
        // dd('hello3');
        $filter = new Filter();
        $form_filter = $this->createForm(FilterType::class, $filter);

        $form_filter->handleRequest($request);
        if($form_filter->isSubmitted() && $form_filter->isValid()){
            $estates = $this->entityManager->getRepository(Estate::class)->findByCity($filter);
        }else{
            $estates = $this->entityManager->getRepository(Estate::class)->getEstatePaginator($estate, $offset);
        }

        return $this->render('estate/index.html.twig', [
            'estates' => $estates,
            'form_filter' => $form_filter->createView(),
            'form_add' => $form_add->createView(),
            'prev_page' => $prev_page,
            'next_page' => $next_page,
            'current_page' => $current_page
        ]);
    }

    #[Route('/edit/{id}', name: 'app_estate_edit')]
    public function edit($id, Request $request, SluggerInterface $slugger): Response
    {

        $estate = $this->entityManager->getRepository(Estate::class)->findOneById($id);

        $image = $estate->getImage();

        $form_edit = $this->createForm(EstateType::class, $estate);

        $estate->setImage($image);

        $form_edit->handleRequest($request);
        if($form_edit->isSubmitted() && $form_edit->isValid()){ // Si on essaie d'ajouter un bien immo
            $estateImage = $form_edit->get('image')->getData();

            if ($estateImage) {

                $originalFilename = pathinfo($estateImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$estateImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $estateImage->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $estate->setImage($newFilename);

            }else{
                $estate->setImage($image);
            }

            $this->entityManager->flush();

        }




        return $this->render('estate/edit.html.twig', [
            'estate' => $estate,
            'form_edit' => $form_edit->createView()
        ]);
    }
}
