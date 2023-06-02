<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande de contact')
            ->setEntityLabelInPlural('Demandes de contact')
            ->setPageTitle ('index', 'Symrecipe - Administration des contacts')
            ->setPaginatorPageSize (10)
            ->addFormTheme ('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('fullName', 'Nom'),
            TextField::new('email', 'Email')->setFormTypeOption ('disabled', 'disabled'),
            TextField::new('subject', 'Objet'),
            TextareaField::new('message')
                ->setFormType (CKEditorType::class)
                ->hideOnIndex (),
            DateTimeField::new('createdAt', 'Date')->setFormat('dd.MM.yyyy')
                ->hideOnForm (),
        ];
    }
}
