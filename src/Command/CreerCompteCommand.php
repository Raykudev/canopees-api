<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Crée un compte administrateur depuis le terminal (il n'y a pas de création de compte dans l'admin).
// Utilisation : php bin/console app:creer-compte
#[AsCommand(name: 'app:creer-compte', description: 'Crée un compte administrateur pour le back-office')]
class CreerCompteCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $hasher,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(SymfonyStyle $io): int
    {
        $email = $io->ask('Email de connexion');
        if ($this->userRepository->findOneBy(['email' => $email])) {
            $io->error('Un compte existe déjà avec cet email.');

            return Command::FAILURE;
        }

        $prenom = $io->ask('Prénom');
        $nom = $io->ask('Nom');
        // askHidden : le mot de passe ne s'affiche pas pendant qu'on le tape
        $motDePasse = $io->askHidden('Mot de passe (8 caractères minimum)');
        if (strlen((string) $motDePasse) < 8) {
            $io->error('Le mot de passe doit faire au moins 8 caractères.');

            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email)
            ->setPrenom($prenom)
            ->setNom($nom)
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($user, $motDePasse));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Compte créé pour %s (%s).', $prenom, $email));

        return Command::SUCCESS;
    }
}
