<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Ingredients;
use App\Entity\Marks;
use App\Entity\Recipes;
use App\Entity\Users;
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
        // Users
        $users = [];

        $admin = new Users();
        $admin->setFullName ('Administrateur de Symrecipe')
        ->setPseudo (null)
        ->setEmail ('admin@symrecipe.fr')
        ->setRoles (['ROLE_USER', 'ROLE_ADMIN'])
        ->setPlainPassword ('password');

        $users[] = $admin;
        $manager->persist ($admin);

        for ($k = 0; $k < 10; $k++)
        {
            $user = new Users();
            $user->setFullName ($this->faker->name ())
                ->setPseudo (mt_rand (0,1) === 1 ? $this->faker->firstName() : null)
                ->setEmail ($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword ('password');

            /*           $hashPassword = $this->hasher->hashPassword ($user, 'password');
                       $user->setPassword ($hashPassword);*/

            $users[] = $user;
            $manager->persist ($user);
        }

        // Ingredients
        $ingredients = [];
        for ($i = 0; $i < 50; $i++) {
            $ingredient = new Ingredients();
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0, 100))
                ->setUser($users[mt_rand(0, count($users) - 1)]);

            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recipes
        $recipes = [];
        for ($j = 0; $j < 25; $j++) {
            $recipe = new Recipes();
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : 0)
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : 0)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : 0)
                ->setUser($users[mt_rand(0, count($users) - 1)]);

            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }

            $recipes[] = $recipe;
            $manager->persist($recipe);
        }

        //Marks
        foreach ($recipes as $recipe){
            for ($l = 0; $l < mt_rand (0,4); $l++)
            {
                $mark = new Marks();
                $mark->setMark (mt_rand(0,5))
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recipe);
                $manager->persist($mark);
            }
        }

        //Contact
            for ($m = 0; $m < 5; $m++)
            {
                $contact = new Contact();
                $contact->setFullName ($this->faker->name())
                    ->setEmail ($this->faker->email())
                    ->setSubject ('demande n°' . ($m +1))
                    ->setMessage ($this->faker->text())
                ;
                $manager->persist($contact);
            }

        $manager->flush();
    }
}
