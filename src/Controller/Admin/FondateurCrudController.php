<?php

namespace App\Controller\Admin;

use App\Entity\Fondateur;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FondateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fondateur::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Fondateur')
            ->setEntityLabelInPlural('Fondateurs')
            ->setDefaultSort(['ordre' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield IntegerField::new('ordre', "Ordre d'affichage");
        yield ImageField::new('photo', 'Photo')
            ->setBasePath(Fondateur::DOSSIER_IMAGES)
            ->setUploadDir('public/' . Fondateur::DOSSIER_IMAGES)
            ->setUploadedFileNamePattern('[slug]-[randomhash].[extension]')
            ->setRequired(Crud::PAGE_NEW === $pageName);
        yield TextField::new('prenom', 'Prénom');
        yield TextField::new('role', 'Rôle')
            ->setHelp('Affiché dans le badge de la carte, ex. : « Fondateur ».');
        yield TextareaField::new('bio', 'Biographie')->hideOnIndex();
        yield TextField::new('photoAlt', 'Description de la photo (accessibilité)')->hideOnIndex();
        yield ImageField::new('avatar', 'Petite photo ronde')
            ->setBasePath(Fondateur::DOSSIER_IMAGES)
            ->setUploadDir('public/' . Fondateur::DOSSIER_IMAGES)
            ->setUploadedFileNamePattern('[slug]-[randomhash].[extension]')
            ->setRequired(Crud::PAGE_NEW === $pageName)
            ->hideOnIndex();
    }
}
