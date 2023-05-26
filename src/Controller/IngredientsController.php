<?php

namespace App\Controller;

use App\Entity\Ingredients;
use App\Form\IngredientsFormType;
use App\Repository\IngredientsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
    #[IsGranted('ROLE_USER')]
    public function index(IngredientsRepository $ingredientsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientsRepository->findBy (['user' => $this->getUser ()]),
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
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredients();
        $form = $this->createForm(IngredientsFormType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success', 'Votre ingrédient a été créé avec succès !');

            return $this->redirectToRoute('ingredients.index');
        }

        return $this->render('pages/ingredients/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    #[Route('/ingredients/edit/{id}', 'ingredients.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredients $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(IngredientsFormType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            return $this->redirectToRoute('ingredients.index');
        }

        return $this->render('pages/ingredients/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredients/delete/{id}', 'ingredients.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager,Ingredients $ingredient): Response {
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès !'
        );

        return $this->redirectToRoute('ingredients.index');
    }

}
