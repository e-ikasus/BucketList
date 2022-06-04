<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Services\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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
		$wishes = $entityManager->getRepository(Wish::class)->findAllFull();

		return $this->render('wish/list.html.twig', ['wishes' => $wishes,]);
	}

	/**
	 * @Route("/new", name="wish_new", methods={"GET", "POST"})
	 */
	public function new(LoggerInterface $log, Censurator $censurator, Request $request, EntityManagerInterface $entityManager): Response
	{
		$wish = new Wish();
		$wish->setIsPublished(true);
		$wish->setDateCreated(new \DateTime());

		$form = $this->createForm(WishType::class, $wish);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$wish->setDescription($censurator->purify($wish->getDescription()));

			try
			{
				$entityManager->persist($wish);
				$entityManager->flush();
			}
			catch (\Doctrine\DBAL\Exception $e)
			{

			}
			$this->addFlash("info", "New wish added successfully");

			return $this->redirectToRoute('wish_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('wish/new.html.twig', ['wish' => $wish, 'form' => $form,]);
	}

	/**
	 * @Route("/{id}/show", name="wish_show", methods={"GET"})
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

		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager->flush();

			return $this->redirectToRoute('wish_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('wish/edit.html.twig', ['wish' => $wish, 'form' => $form,]);
	}

	/**
	 * @Route("/{id}/delete", name="wish_delete", methods={"POST"})
	 */
	public function delete(Request $request, Wish $wish, EntityManagerInterface $entityManager): Response
	{
		if ($this->isCsrfTokenValid('delete' . $wish->getId(), $request->request->get('_token')))
		{
			$entityManager->remove($wish);
			$entityManager->flush();

			$this->addFlash("info", "Wish successfully deleted");
		}

		return $this->redirectToRoute('wish_index', [], Response::HTTP_SEE_OTHER);
	}
}
