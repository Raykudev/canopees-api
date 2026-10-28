<?php

namespace App\Controller\Admin;

use App\Entity\Entreprise;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class EntrepriseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Entreprise::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', "Nom de l'entreprise");
        yield TextareaField::new('description', 'Présentation (pied de page)')
            ->setHelp('Le petit texte affiché dans le pied de page du site.');
        yield TextField::new('adresse', 'Adresse');
        yield TextField::new('codePostal', 'Code Postal');
        /* Je n'utilise pas integerField car un code postal commencant par 0 verrait le 0 supprimer,
        pareil pour un numero de telephone. si on ne calcule pas un nombre c'est alors du texte. */
        yield TextField::new('ville','Ville');
        yield TelephoneField::new('telephone','Téléphone');
        yield EmailField::new('email','Email de contact');
        yield TextField::new('horaires', "Horaire d'ouverture")
            ->setHelp('Ex. : «Du lundi au vendredi, 08h00-18h00. Affiché sur la page Contact');

    }
}   
