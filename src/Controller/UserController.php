<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    /**
     * @param Users $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(
        Users $choosenUser,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response
    {
  /*      if(!$this->getUser ()){
            return $this->redirectToRoute ('security.login');
        }
        if ($this->getUser () !== $user){
            return $this->redirectToRoute ('recipes.index');
        }*/

        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
         if($hasher->isPasswordValid ($choosenUser, $form->getData()->getPlainPassword()))
             {
                 $user = $form->getData ();
                 $manager->persist($user);
                 $manager->flush ();

                 $this->addFlash ('success', 'les informations de votre compte ont bien été modifiées');

                 return $this->redirectToRoute ('recipes.index');
             }else{
                 $this->addFlash ('warning', 'le mot de passe renseigné n\'est pas valide');
             }

        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Users $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edition-mot-de-passe/{id}', 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(Users $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher) : Response
    {
        $form = $this->createForm (UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($hasher->isPasswordValid ($choosenUser, $form->getData()['plainPassword']))
            {
                $choosenUser->setUpdatedAt(new \DateTimeImmutable());
                $choosenUser->setPlainPassword(
                    $form->getData()['newPassword']
                );

                $manager->persist($choosenUser);
                $manager->flush();

                $this->addFlash ('success', 'Le mot de passe a été modifié');
                return $this->redirectToRoute ('recipes.index');
            }else{
                $this->addFlash ('warning', 'le mot de passe renseigné n\'est pas valide');
            }
        }
        return $this->render ('pages/user/edit_password.html.twig', [
            'form' => $form->createView ()
        ]);
    }
}
