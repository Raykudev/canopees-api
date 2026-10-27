<?php

namespace App\Controller\Admin;

use App\Entity\Valeur;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ValeurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Valeur::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Valeur')
            ->setEntityLabelInPlural('Nos valeurs')
            ->setDefaultSort(['ordre' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield IntegerField::new('ordre', "Ordre d'affichage")
            ->setHelp('La couleur de la pastille dépend de la position sur le site.');
        yield TextField::new('titre', 'Valeur');
        yield TextareaField::new('texte', 'Description');
    }
}
