<?php

namespace App\Controller\Admin;

use App\Entity\Prestation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class PrestationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Prestation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Prestation')
            ->setEntityLabelInPlural('Prestations & tarifs')
            ->setDefaultSort(['ordre' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield IntegerField::new('ordre', "Ordre d'affichage");
        yield TextField::new('titre', 'Titre');
        yield TextareaField::new('texte', 'Description')->hideOnIndex();
        yield MoneyField::new('prix', 'Prix « à partir de »')
            ->setCurrency('EUR')
            ->setStoredAsCents()
            ->setHelp('Laisser vide si la prestation est sur devis uniquement.');
        yield TextareaField::new('texteTarif', 'Texte spécifique à la page Tarifs')
            ->hideOnIndex()
            ->setHelp('Facultatif : remplace la description sur la page Tarifs.');
        yield ImageField::new('image', 'Image')
            ->setBasePath(Prestation::DOSSIER_IMAGES)
            ->setUploadDir('public/' . Prestation::DOSSIER_IMAGES)
            ->setUploadedFileNamePattern('[slug]-[randomhash].[extension]')
            ->setRequired(Crud::PAGE_NEW === $pageName);
        yield TextField::new('alt', "Description de l'image (accessibilité)")->hideOnIndex();
    }
}
