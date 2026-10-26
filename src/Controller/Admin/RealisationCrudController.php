<?php

namespace App\Controller\Admin;

use App\Entity\Realisation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RealisationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Realisation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Réalisation')
            ->setEntityLabelInPlural('Réalisations')
            ->setDefaultSort(['dateRealisation' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        // Pour retrouver vite les photos d'une prestation, ou celles du carrousel
        return $filters
            ->add('prestation')
            ->add('accueil');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield ImageField::new('image', 'Photo')
            ->setBasePath(Realisation::DOSSIER_IMAGES)
            ->setUploadDir('public/' . Realisation::DOSSIER_IMAGES)
            ->setUploadedFileNamePattern('[slug]-[randomhash].[extension]')
            ->setRequired(Crud::PAGE_NEW === $pageName);
        yield AssociationField::new('prestation', 'Prestation');
        yield DateField::new('dateRealisation', 'Date de la réalisation');
        yield BooleanField::new('accueil', "Afficher dans le carrousel de l'accueil");
        yield TextField::new('alt', 'Description de la photo (accessibilité)')->hideOnIndex();
    }
}
