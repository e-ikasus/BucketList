<?php

namespace App\Controller;

use App\Entity\Wish;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_index")
     * @return Response
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $wishes = $entityManager->getRepository(Wish::class)->findAll();

        return $this->render('wish/list.html.twig', ["wishes" => $wishes]);
    }

    /**
     * @Route("/about", name="main_about")
     * @return Response
     */
    public function about()
    {
        $json = file_get_contents("../data/creators.json");
        $jsond = json_decode($json, true);

        return $this->render('main/about.html.twig', ["jsond" => $jsond]);
    }
}
