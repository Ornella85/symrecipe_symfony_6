<?php

namespace App\DataFixtures;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Entity\Recipes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create ('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        // Ingredients
        $ingredients = [];
        for ($i = 0; $i < 50; $i++){
            $ingredient = new Ingredients();
            $ingredient->setName ($this->faker->word ())
                        ->setPrice (mt_rand (0, 100));

            $ingredients[] = $ingredient;
            $manager->persist ($ingredient);
        }

        // Recipes
        for ($j = 0; $j < 25; $j++)
        {
            $recipe = new Recipes();
            $recipe->setName ($this->faker->word ())
                ->setPrice (mt_rand (0, 100))
                ->setTime (mt_rand (0,1) == 1 ? mt_rand (1,1440) : null)
                ->setNbPeople (mt_rand (0,1) == 1 ? mt_rand (1,50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : 0)
                ->setDescription ($this->faker->word (300))
                ->setPrice (mt_rand (0,1) == 1 ? mt_rand (1,1000) : null)
                ->setIsFavorite (mt_rand (0,1) == 1 ? true : false);

            // pour ajouter plusieurs ingrédients dans une même recette
            for ($k = 0; $k < mt_rand (5,15); $k++)
            {
                $recipe->addIngredient ($ingredients[mt_rand (0, count($ingredients) - 1)]);
            }
            $manager->persist ($recipe);
        }

        $manager->flush();
    }
}
