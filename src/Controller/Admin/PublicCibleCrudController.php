<?php

namespace App\Controller\Admin;

use App\Entity\PublicCible;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PublicCibleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PublicCible::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Public cible')
            ->setEntityLabelInPlural('Publics cibles')
            ->setDefaultSort(['ordre' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield IntegerField::new('ordre', "Ordre d'affichage");
        yield ImageField::new('photo', 'Photo')
            ->setBasePath(PublicCible::DOSSIER_IMAGES)
            ->setUploadDir('public/' . PublicCible::DOSSIER_IMAGES)
            ->setUploadedFileNamePattern('[slug]-[randomhash].[extension]')
            ->setRequired(Crud::PAGE_NEW === $pageName);
        yield TextField::new('nom', 'Nom')
            ->setHelp('Ex. : « Particuliers ».');
        yield TextField::new('photoAlt', 'Description de la photo (accessibilité)')->hideOnIndex();
        yield ImageField::new('icone', 'Petite image ronde')
            ->setBasePath(PublicCible::DOSSIER_IMAGES)
            ->setUploadDir('public/' . PublicCible::DOSSIER_IMAGES)
            ->setUploadedFileNamePattern('[slug]-[randomhash].[extension]')
            ->setRequired(Crud::PAGE_NEW === $pageName)
            ->hideOnIndex();
    }
}
