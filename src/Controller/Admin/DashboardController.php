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
            ['libelle' => 'Messages non lus', 'icone' => 'fas fa-envelope', 'couleur' => 'couleur-rose', 'nombre' => $this->messageRepository->count(['lu' => false]), 'route' => null],
            ['libelle' => 'Prestations', 'icone' => 'fas fa-leaf', 'couleur' => 'couleur-vert', 'nombre' => $this->prestationRepository->count(), 'route' => 'admin_prestation_index'],
            ['libelle' => 'Réalisations', 'icone' => 'fas fa-camera', 'couleur' => 'couleur-mauve', 'nombre' => $this->realisationRepository->count(), 'route' => 'admin_realisation_index'],
            ['libelle' => 'Photos du slider', 'icone' => 'fas fa-images', 'couleur' => 'couleur-orange', 'nombre' => $this->slideRepository->count(), 'route' => null],
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
        yield MenuItem::linkTo(PrestationCrudController::class, 'Prestations & tarifs', 'fas fa-leaf')->setCssClass('couleur-vert');
        yield MenuItem::linkTo(RealisationCrudController::class, 'Réalisations', 'fas fa-camera')->setCssClass('couleur-mauve');
    }
}
