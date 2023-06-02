<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UsersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Users::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPageTitle ('index', 'Symrecipe - Administration des utilisateurs')
            ->setPaginatorPageSize (10)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('fullName', 'Nom'),
            TextField::new('pseudo', 'Pseudo'),
            TextField::new('email', 'Email')->setFormTypeOption ('disabled', 'disabled'),
            ArrayField::new('roles'),
            DateTimeField::new('createdAt', 'Date')->setFormat('dd.MM.yyyy HH:mm:ss')
                ->setTimezone('Europe/Paris')
                ->hideOnForm (),
            DateTimeField::new('updatedAt', 'Date')->setFormat('dd.MM.yyyy HH:mm:ss')
                ->setTimezone('Europe/Paris')
                ->hideOnForm (),
        ];
    }
}
