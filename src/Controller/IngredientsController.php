<?php

namespace App\Controller;

use App\Entity\Ingredients;
use App\Form\IngredientsFormType;
use App\Repository\IngredientsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IngredientsController extends AbstractController
{
    /**
     * This function display all ingredients
     * @param IngredientsRepository $ingredientsRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */

    #[Route('/ingredients', name: 'ingredients.index', methods:['GET'])]
    public function index(IngredientsRepository $ingredientsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientsRepository->findAll (),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/ingredients/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredients/new', name: 'ingredients.new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredients();
        $form = $this->createForm(IngredientsFormType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success', 'Votre ingrédient a été créé avec succès !');

            return $this->redirectToRoute('ingredients.index');
        }

        return $this->render('pages/ingredients/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
