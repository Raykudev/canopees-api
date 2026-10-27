<?php

namespace App\Controller\Admin;

use App\Entity\Section;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * Le formulaire d'UNE section. Il n'a pas d'écran dans le menu :
 * il est affiché à l'intérieur du formulaire d'une page (voir PageCrudController).
 */
class SectionCrudController extends AbstractCrudController
{
    // Les types de section que le site React sait afficher : libellé pour Bob => valeur en base
    public const TYPES = [
        'Présentation (titre + texte en haut de page)' => 'presentation',
        'Paragraphe (pages légales)' => 'paragraphe',
        'Intro « Pour qui travaillons-nous ? »' => 'public-cible-intro',
        'Histoire de l\'entreprise' => 'histoire',
        'Intro « Nos valeurs »' => 'valeurs-intro',
        'Étape « Comment ça marche ? »' => 'etape',
        'Appel à l\'action (bas de page)' => 'appel-action',
    ];

    public static function getEntityFqcn(): string
    {
        return Section::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('type', 'Type de section')
            ->setChoices(self::TYPES)
            ->setHelp('Décide comment la section est affichée sur le site.');
        yield IntegerField::new('ordre', "Ordre d'affichage");
        yield TextField::new('titre', 'Titre');
        yield TextField::new('sousTitre', 'Sous-titre')
            ->setHelp("Ex. : la phrase sous « Canopées » en haut de l'accueil.");
        yield TextareaField::new('texte', 'Texte');
        yield ImageField::new('image', 'Image')
            ->setBasePath(Section::DOSSIER_IMAGES)
            ->setUploadDir('public/' . Section::DOSSIER_IMAGES)
            ->setUploadedFileNamePattern('[slug]-[randomhash].[extension]');
        yield TextField::new('alt', "Description de l'image (accessibilité)");
    }
}
