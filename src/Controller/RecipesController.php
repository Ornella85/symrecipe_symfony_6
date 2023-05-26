<?php

namespace App\Controller;

use App\Entity\Recipes;
use App\Form\RecipesType;
use App\Repository\RecipesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use symfony\Component\Cache\Adapter\FilesystemAdapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/recipes')]
class RecipesController extends AbstractController
{
    /**
     * @param RecipesRepository $recipesRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'recipes.index', methods: ['GET'])]
    public function index(RecipesRepository $recipesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $recipesRepository->findBy (['user' => $this->getUser ()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipes/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/community', 'recipes.community', methods: ['GET'])]
    public function indexPublic(
        RecipesRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $cache = new FilesystemAdapter();
        $data = $cache->get('recipes', function (ItemInterface $item) use ($repository) {
            $item->expiresAfter(15);
            return $repository->findPublicRecipe(null);
        });

        $recipes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipes/community.html.twig', [
            'recipes' => $recipes
        ]);
    }


    #[Route('/new', name: 'recipes.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipes();
        $form = $this -> createForm (RecipesType::class, $recipe);
        $form -> handleRequest ($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créé avec succès !'
            );

            return $this->redirectToRoute('recipes.index');
        }


        return $this->renderForm('pages/recipes/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Security("is_granted('ROLE_USER') and recipe.getIsPublic() === true")]
    #[Route('/{id}', name: 'recipes.show', methods: ['GET'])]
    public function show(Recipes $recipe): Response
    {
        return $this->render('pages/recipes/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/{id}/edit', name: 'recipes.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipes $recipe, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipesType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès !'
            );

            return $this->redirectToRoute('recipes.index');
        }

        return $this->renderForm('pages/recipes/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    /**
     * Une méthode différente avec un messages de confirmation avant delete
     * @param Request $request
     * @param Recipes $recipe
     * @param RecipesRepository $recipesRepository
     * @return Response
     */
    #[Route('/{id}', name: 'recipes.delete', methods: ['POST'])]
    public function delete(Request $request, Recipes $recipe, RecipesRepository $recipesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $recipesRepository->remove($recipe, true);
        }

        return $this->redirectToRoute('recipes.index', [], Response::HTTP_SEE_OTHER);
    }
}
