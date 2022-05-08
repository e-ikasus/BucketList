<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wish")
 */
class WishController extends AbstractController
{
    /**
     * @Route("/", name="wish_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $wishes = $entityManager
            ->getRepository(Wish::class)
            ->findAll();

        return $this->render('wish/index.html.twig', [
            'wishes' => $wishes,
        ]);
    }

    /**
     * @Route("/new", name="wish_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $wish = new Wish();
        $wish->setIsPublished(true);
        $wish->setDateCreated(new \DateTime());
        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash("info", "New wish added successfully");

            return $this->redirectToRoute('wish_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('wish/new.html.twig', [
            'wish' => $wish,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="wish_show", methods={"GET"})
     */
    public function show(Wish $wish): Response
    {
        return $this->render('wish/show.html.twig', [
            'wish' => $wish,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="wish_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Wish $wish, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('wish_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('wish/edit.html.twig', [
            'wish' => $wish,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="wish_delete", methods={"POST"})
     */
    public function delete(Request $request, Wish $wish, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wish->getId(), $request->request->get('_token'))) {
            $entityManager->remove($wish);
            $entityManager->flush();

            $this->addFlash("info", "Wish successfully deleted");
        }

        return $this->redirectToRoute('wish_index', [], Response::HTTP_SEE_OTHER);
    }
}
