<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;




class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('prenom', 'Prénom');
        yield TextField::new('nom', 'Nom');
        yield EmailField::new('email', 'Email de connexion');
        yield TextField::new('plainPassword', 'Nouveau mot de passe')
            ->setFormType(PasswordType::class)
            ->setRequired(false)
            ->setHelp('Laisser vide pour garder le mot de passe actuel.')
            ->onlyOnForms();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->displayIf(fn(User $user) => $user === $this->getUser());
            });
    }

    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        /** @var User $entityInstance */
        $nouveauMotDePasse = $entityInstance->getPlainPassword();

        if ($nouveauMotDePasse) {
            $entityInstance->setPassword($this->hasher->hashPassword($entityInstance, $nouveauMotDePasse));
            $entityInstance->setPlainPassword(null);
        }
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function edit(AdminContext $context): KeyValueStore|Response
    {
        if ($context->getEntity()->getInstance() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que votre propre compte.');
        }

        return parent::edit($context);
    }
}
