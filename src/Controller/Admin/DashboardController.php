<?php

namespace App\Controller\Admin;

use App\Repository\MessageRepository;
use App\Repository\PrestationRepository;
use App\Repository\RealisationRepository;
use App\Repository\SlideRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    // Symfony nous donne les repositories automatiquement (injection de dépendances)
    public function __construct(
        private MessageRepository $messageRepository,
        private PrestationRepository $prestationRepository,
        private RealisationRepository $realisationRepository,
        private SlideRepository $slideRepository,
    ) {
    }

    public function index(): Response
    {
        // Les chiffres affichés en haut du tableau de bord : un lien « Gérer » par carte quand l'écran existe
        $cartes = [
            ['libelle' => 'Messages non lus', 'icone' => 'fas fa-envelope', 'couleur' => 'couleur-rose', 'nombre' => $this->messageRepository->count(['lu' => false]), 'route' => 'admin_message_index'],
            ['libelle' => 'Prestations', 'icone' => 'fas fa-leaf', 'couleur' => 'couleur-vert', 'nombre' => $this->prestationRepository->count(), 'route' => 'admin_prestation_index'],
            ['libelle' => 'Réalisations', 'icone' => 'fas fa-camera', 'couleur' => 'couleur-mauve', 'nombre' => $this->realisationRepository->count(), 'route' => 'admin_realisation_index'],
            ['libelle' => 'Photos du slider', 'icone' => 'fas fa-images', 'couleur' => 'couleur-orange', 'nombre' => $this->slideRepository->count(), 'route' => 'admin_slide_index'],
        ];

        return $this->render('admin/dashboard.html.twig', [
            'cartes' => $cartes,
            'derniersMessages' => $this->messageRepository->findBy([], ['dateEnvoi' => 'DESC'], 5),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Canopées - Administration');
    }

    public function configureAssets(): Assets
    {
        // Notre feuille de style, ajoutée à toutes les pages de l'admin (public/css/admin.css)
        return Assets::new()->addCssFile('css/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fas fa-house')->setCssClass('couleur-bleu');
        yield MenuItem::linkTo(MessageCrudController::class, 'Messages reçus', 'fas fa-envelope')->setCssClass('couleur-rose');
        yield MenuItem::linkTo(PrestationCrudController::class, 'Prestations & tarifs', 'fas fa-leaf')->setCssClass('couleur-vert');
        yield MenuItem::linkTo(RealisationCrudController::class, 'Réalisations', 'fas fa-camera')->setCssClass('couleur-mauve');
        yield MenuItem::linkTo(SlideCrudController::class, 'Slider de l\'accueil', 'fas fa-images')->setCssClass('couleur-orange');

        // Les contenus des pages « Accueil » et « Qui sommes-nous »
        yield MenuItem::section('Contenu des pages');
        yield MenuItem::linkTo(PageCrudController::class, 'Textes des pages', 'fas fa-file-lines')->setCssClass('couleur-mauve');
        yield MenuItem::linkTo(FondateurCrudController::class, 'Fondateurs', 'fas fa-user-tie')->setCssClass('couleur-vert');
        yield MenuItem::linkTo(PublicCibleCrudController::class, 'Publics cibles', 'fas fa-users')->setCssClass('couleur-bleu');
        yield MenuItem::linkTo(ValeurCrudController::class, 'Nos valeurs', 'fas fa-heart')->setCssClass('couleur-rose');
    }
}
