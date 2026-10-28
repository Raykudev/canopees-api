<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\HttpFoundation\Response;

class MessageCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Message::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Message')
            ->setEntityLabelInPlural('Messages reçus')
            ->setDefaultSort(['dateEnvoi' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Un bouton « Consulter » sur chaque ligne de la liste
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // Les messages viennent du site : on ne les crée ni ne les modifie ici
            ->disable(Action::NEW, Action::EDIT);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('lu')
            ->add('prestation')
            ->add('dateEnvoi');
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('dateEnvoi', 'Reçu le')->setFormat('dd/MM/yyyy HH:mm');
        yield BooleanField::new('lu', 'Lu')->renderAsSwitch(false);
        yield TextField::new('prenom', 'Prénom');
        yield TextField::new('nom', 'Nom');
        yield AssociationField::new('prestation', 'Prestation demandée');
        yield TelephoneField::new('telephone', 'Téléphone');
        yield EmailField::new('mail', 'Email')->hideOnIndex();
        yield TextField::new('adresse', 'Adresse')->hideOnIndex();
        yield TextField::new('codePostal', 'Code postal')->hideOnIndex();
        yield TextField::new('ville', 'Ville');
        yield TextareaField::new('message', 'Message')->onlyOnDetail();
        yield BooleanField::new('rgpd', 'Consentement RGPD')->renderAsSwitch(false)->onlyOnDetail();
    }

    /**
     * Quand l'administrateur ouvre un message, il passe automatiquement en « lu ».
     */
    public function detail(AdminContext $context): KeyValueStore|Response
    {
        /** @var Message $message */
        $message = $context->getEntity()->getInstance();

        if (!$message->isLu()) {
            $message->setLu(true);
            $this->entityManager->flush();
        }

        return parent::detail($context);
    }
}
