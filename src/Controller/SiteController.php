<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;

// En production, le site React construit (npm run build) est copié dans public/.
// Toute adresse qui n'est ni l'API, ni l'administration, ni la connexion renvoie sa page index.html :
// c'est ensuite React Router qui affiche la bonne page (/tarifs, /contact…).
class SiteController extends AbstractController
{
    #[Route(
        '/{chemin}',
        name: 'site_react',
        requirements: ['chemin' => '(?!api|admin|login|logout|_).*'],
        defaults: ['chemin' => ''],
        methods: ['GET'],
        priority: -100,
    )]
    public function __invoke(): BinaryFileResponse
    {
        $page = $this->getParameter('kernel.project_dir').'/public/index.html';

        // En développement, le site React tourne à part (npm run dev) : il n'y a pas de index.html ici
        if (!is_file($page)) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($page);
    }
}
