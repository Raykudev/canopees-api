<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Entreprise;
use App\Entity\Message;
use App\Entity\Page;
use App\Entity\Prestation;
use App\Entity\Realisation;
use App\Entity\Section;
use App\Entity\Slide;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const LOREM = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam vehicula ligula in pharetra placerat. Integer eros lectus, scelerisque a lectus a, lacinia hendrerit erat.';

    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $prestations = $this->chargerPrestations($manager);
        $this->chargerRealisations($manager, $prestations);
        $this->chargerSlides($manager);
        $this->chargerEntreprise($manager);
        $this->chargerPages($manager);
        $this->chargerMessages($manager, $prestations);
        $this->chargerAdmins($manager);

        $manager->flush();
    }

    /**
     * @return array<string, Prestation> les prestations, indexées par une clé courte
     */
    private function chargerPrestations(ObjectManager $manager): array
    {
        // clé => [titre, texte, image, alt, prix en centimes, texte spécifique à la page Tarifs]
        $donnees = [
            'elagage' => ["Elagage et abattage d'arbres", self::LOREM, '/images/prestations/imageDur/elagage-canopees.jpg', 'Un élagueur de Canopees en action sur un arbre', 59900, null],
            'conception' => ["Conception et réalisation d'espaces verts", self::LOREM, '/images/prestations/imageDur/vert.jpg', 'Espace vert fleuri fraîchement aménagé', 89900, null],
            'entretien' => ['Entretien des espaces verts', self::LOREM, '/images/prestations/imageDur/entretien-canopees.jpg', 'Espace vert entretenu avec pelouse tondue', 29900, null],
            'haies' => ['Taille des haies', self::LOREM, '/images/prestations/imageDur/haie.jpg', "Taille d'une haie au sécateur", 39900, null],
            'eco' => [
                'Valorisation des déchets verts ( compostage )',
                "Pour le respect de l'environnement, nous collectons les déchets verts, afin de les réutiliser pour du compostage.",
                '/images/prestations/imageDur/eco.jpg',
                'Illustration du recyclage des déchets verts',
                null,
                'La collecte des déchets verts est incluse dans chaque prestation. Pour une demande de collecte seule, contactez-nous directement pour étudier votre besoin.',
            ],
        ];

        $prestations = [];
        $ordre = 1;
        foreach ($donnees as $cle => [$titre, $texte, $image, $alt, $prix, $texteTarif]) {
            $prestation = (new Prestation())
                ->setTitre($titre)
                ->setTexte($texte)
                ->setImage($image)
                ->setAlt($alt)
                ->setPrix($prix)
                ->setTexteTarif($texteTarif)
                ->setOrdre($ordre++);

            $manager->persist($prestation);
            $prestations[$cle] = $prestation;
        }

        return $prestations;
    }

    /**
     * @param array<string, Prestation> $prestations
     */
    private function chargerRealisations(ObjectManager $manager, array $prestations): void
    {
        // Les photos de la modale de chaque prestation : clé de la prestation => [image, alt]
        $photos = [
            'elagage' => [
                ['/images/prestations/elagage/elagage-1.jpg', "Réalisation d'élagage"],
                ['/images/prestations/elagage/elagage-2.jpg', "Réalisation d'élagage"],
                ['/images/prestations/elagage/elagage-3.jpg', "Réalisation d'élagage"],
                ['/images/prestations/elagage/elagage-4.jpg', "Réalisation d'élagage"],
                ['/images/prestations/elagage/elagage-5.jpg', "Réalisation d'élagage"],
                ['/images/prestations/elagage/elagage-6.jpg', "Réalisation d'élagage"],
            ],
            'conception' => [
                ['/images/prestations/conception/conception-1.jpg', "Réalisation d'une conception d'espace vert conçue par Canopees"],
                ['/images/prestations/conception/conception-2.jpg', "Réalisation d'une conception d'espace vert conçue par Canopees"],
                ['/images/prestations/conception/conception-3.jpg', "Réalisation d'une conception d'espace vert conçue par Canopees"],
                ['/images/prestations/conception/conception-4.jpg', "Réalisation d'une conception d'espace vert conçue par Canopees"],
                ['/images/prestations/conception/conception-5.jpg', "Réalisation d'une conception d'espace vert conçue par Canopees"],
                ['/images/prestations/conception/conception-6.jpg', "Réalisation d'une conception d'espace vert conçue par Canopees"],
            ],
            'entretien' => [
                ['/images/prestations/entretien/entretien1.jpg', "Jardin fleuri avec des arbres entourés d'une bordure fleuri"],
                ['/images/prestations/entretien/entretien2.jpg', 'Grand espaces fleuri, avec de nombreuses fleurs de toutes les couleurs'],
                ['/images/prestations/entretien/entretien3.jpg', 'Parc avec des arbres taillés et une pelouse vertes fraichement entretenu'],
                ['/images/prestations/entretien/entretien4.jpg', 'Une personne tondant la pelouse, une autre personne nettoyant la bordure du gazon et des arbres'],
                ['/images/prestations/entretien/entretien5.jpg', "Une personnes soufflant le reste d'herbes coupé"],
                ['/images/prestations/entretien/entretien6.jpg', "Un immense parc, avec de magnifique arbres, une pelouse vertes fraichement entretenu, et un chemin en terre pour se balader a l'interieur"],
            ],
            'haies' => [
                ['/images/prestations/taille/taille1.jpg', 'Haie taillé en forme de sapin'],
                ['/images/prestations/taille/taille2.jpg', '3 Haie taillé en boules'],
                ['/images/prestations/taille/taille3.jpg', 'Espace vert entretenu avec des haies sculptées et un jardin paysager soigné'],
                ['/images/prestations/taille/taille4.jpg', 'chemin bordé de haies, avec une haie en arche a la fin'],
                ['/images/prestations/taille/taille5.jpg', 'Espace vert soigneusement entretenu devant un bâtiment moderne avec haies taillées et pelouse'],
                ['/images/prestations/taille/taille6.jpg', 'Végétation soigneusement taillée formant une arche naturelle dans un jardin paysager'],
            ],
            'eco' => [
                ['/images/prestations/eco/eco1.jpg', "Camion collectant les branches et l'herbes coupées"],
                ['/images/prestations/eco/eco2.jpg', 'Dépôt de déchets verts pour compostage'],
                ['/images/prestations/eco/eco3.jpg', 'Gros tas de compost devenu du compost mûr'],
                ['/images/prestations/eco/eco4.jpg', "2 agents de l'entreprise Canopées qui collecte les déchets verts d'un particulier"],
                ['/images/prestations/eco/eco5.jpg', "2 agents de l'entreprise Canopées qui collecte les déchets verts d'un particulier"],
                ['/images/prestations/eco/eco6.jpg', 'Machine faisant de longue rangées de compost'],
            ],
        ];

        // Des dates étalées dans le passé, pour que le tri « les plus récentes d'abord » ait un sens
        $date = new \DateTimeImmutable('2026-06-01');
        foreach ($photos as $cle => $liste) {
            foreach ($liste as [$image, $alt]) {
                $manager->persist($this->creerRealisation($prestations[$cle], $image, $alt, $date, false));
                $date = $date->modify('-9 days');
            }
        }

        // Les photos du carrousel de l'accueil : les réalisations les plus récentes, cochées « accueil »
        $carrousel = [
            ['entretien', '/images/realisations/realisation-1.jpg', 'Jardin avec une belle pelouse verte devant une maison moderne', '2026-10-12'],
            ['conception', '/images/realisations/realisation-2.jpg', 'Allée en gravillons serpentant dans un jardin tropical luxuriant', '2026-10-01'],
            ['conception', '/images/realisations/realisation-3.jpg', "Aménagement paysager d'un patio moderne avec bancs en bois et plantes tropicales", '2026-09-18'],
            ['entretien', '/images/realisations/realisation-4.jpg', "Jardin fleuri et arbustes taillés devant une maison blanche, avec un chemin en pas japonais", '2026-09-05'],
            ['conception', '/images/realisations/realisation-5.jpg', 'Massifs de palmiers et de plantes tropicales le long d\'une allée pavée', '2026-08-22'],
            ['conception', '/images/realisations/realisation-6.jpg', "Jardinières en pierre plantées d'arbres et d'arbustes au pied d'un immeuble", '2026-08-08'],
        ];

        foreach ($carrousel as [$cle, $image, $alt, $jour]) {
            $manager->persist($this->creerRealisation($prestations[$cle], $image, $alt, new \DateTimeImmutable($jour), true));
        }
    }

    private function creerRealisation(Prestation $prestation, string $image, string $alt, \DateTimeImmutable $date, bool $accueil): Realisation
    {
        return (new Realisation())
            ->setPrestation($prestation)
            ->setImage($image)
            ->setAlt($alt)
            ->setDateRealisation($date)
            ->setAccueil($accueil);
    }

    private function chargerSlides(ObjectManager $manager): void
    {
        $slides = [
            ['Jardin', '/images/slider/jardin-vert.jpg', 'Grand jardin avec une magnifique pelouse verte.'],
            ['Palmier', '/images/slider/plantation-palmier.jpg', 'Jolie coin de plantation de palmier et yuka.'],
            ['elagage-arbre', '/images/slider/elagage-arbre.jpg', 'Personne qui elague un arbre.'],
            ['elagage-palmier', '/images/slider/elagage-palmier.jpg', 'Personne qui elague un palmier.'],
        ];

        foreach ($slides as $index => [$nom, $image, $alt]) {
            $manager->persist(
                (new Slide())->setNom($nom)->setImage($image)->setAlt($alt)->setOrdre($index + 1)
            );
        }
    }

    private function chargerEntreprise(ObjectManager $manager): void
    {
        $manager->persist(
            (new Entreprise())
                ->setNom('Canopées')
                ->setDescription("Société spécialisée dans la création et l'entretien d'espaces verts pour les particuliers, professionnels et les collectivités territoriales depuis 2020.")
                ->setAdresse('La belle adresse')
                ->setCodePostal('34000')
                ->setVille('Montpellier')
                ->setTelephone('00 11 22 33 44')
                ->setEmail('contact@canopees.fr')
                ->setHoraires('Du lundi au vendredi, 08h00-18h00')
        );
    }

    private function chargerPages(ObjectManager $manager): void
    {
        // slug => [titre de la page, liste des sections]
        // Chaque section : type + les champs utiles (titre, sousTitre, texte, image, alt, icone)
        $pages = [
            'accueil' => ["Canopées – Création et entretien d'espaces verts", [
                ['type' => 'presentation', 'titre' => 'Canopées', 'sousTitre' => "Société spécialisée dans la création et l'entretien d'espaces verts.", 'image' => '/images/presentation/garden.jpg', 'alt' => 'Grand jardin remplie de fleurs de toutes les couleurs'],
                ['type' => 'public-cible-intro', 'titre' => 'Pour qui travaillons-nous ?', 'texte' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Obcaecati, assumenda minus! Voluptate vel rerum accusantium laborum provident obcaecati, quis expedita.'],
                ['type' => 'public-cible', 'titre' => 'Particuliers', 'image' => '/images/PublicCible/particulier.jpg', 'alt' => 'Jardin de particuliers', 'icone' => '/images/PublicCible/particulierCircle.jpg'],
                ['type' => 'public-cible', 'titre' => 'Professionnels', 'image' => '/images/PublicCible/entreprise.jpg', 'alt' => 'Espace vert professionnel', 'icone' => '/images/PublicCible/entrepriseCircle.jpg'],
                ['type' => 'public-cible', 'titre' => 'Collectivités', 'image' => '/images/PublicCible/collectivite.jpg', 'alt' => 'Espace vert collectivité', 'icone' => '/images/PublicCible/collectiviteCircle.jpg'],
            ]],
            'qui-sommes-nous' => ['Qui sommes-nous – Canopées', [
                ['type' => 'presentation', 'titre' => 'Qui sommes-nous ?', 'texte' => "Canopées est une entreprise spécialisée dans la création et l'entretien d'espaces verts, pour les particuliers, professionnels et collectivités.\nLorem ipsum dolor sit amet consectetur adipisicing elit. Obcaecati, assumenda minus! Voluptate vel rerum accusantium laborum provident obcaecati, quis expedita."],
                ['type' => 'fondateur', 'titre' => 'Bob', 'sousTitre' => 'Fondateur', 'texte' => 'Passionné de nature depuis toujours, Bob a fondé Canopées pour partager son amour des espaces verts.', 'image' => '/images/fondateurs/image-1.jpg', 'alt' => 'Photo de profil de Bob, fondateur de Canopées', 'icone' => '/images/fondateurs/profil-1.jpg'],
                ['type' => 'fondateur', 'titre' => 'Tom', 'sousTitre' => 'Co-Fondateur', 'texte' => 'Expert en aménagement paysager, Tom apporte son savoir-faire technique à chaque projet Canopées.', 'image' => '/images/fondateurs/image-2.jpg', 'alt' => 'Photo de profil de Tom, cofondateur de Canopées', 'icone' => '/images/fondateurs/profil-2.jpg'],
                ['type' => 'histoire', 'titre' => "Histoire de l'entreprise", 'texte' => 'La société Canopées a été fondée en 2020 par Bob et Tom, deux amoureux de la nature. Notre mission est de créer et entretenir des espaces naturels qui améliorent la qualité de vie des personnes.', 'image' => '/images/histoire/mountain.jpg', 'alt' => "Photo d'une montagne"],
                ['type' => 'valeurs-intro', 'titre' => 'Nos valeurs', 'image' => '/images/histoire/arbre.webp', 'alt' => 'Plante en pot'],
                ['type' => 'valeur', 'titre' => "Respect de l'environnement", 'texte' => 'Nous privilégions des pratiques durables et respectueuses de la biodiversité sur chaque chantier.'],
                ['type' => 'valeur', 'titre' => 'Savoir-faire', 'texte' => "Notre équipe s'appuie sur une expertise technique éprouvée pour garantir des réalisations durables."],
                ['type' => 'valeur', 'titre' => 'Proximité client', 'texte' => "Nous restons à l'écoute de chaque client pour construire un projet qui correspond vraiment à ses besoins."],
                ['type' => 'valeur', 'titre' => 'Qualité et personnalisation', 'texte' => 'Nous misons sur la qualité des matériaux utilisés et la personnalisation des projets en fonction de vos goûts.'],
                ['type' => 'valeur', 'titre' => 'Fiabilité', 'texte' => 'Nous respectons nos engagements de délais et de budget sur chacun de nos chantiers.'],
                ['type' => 'valeur', 'titre' => 'Passion', 'texte' => 'Chaque projet est mené avec le même goût du travail bien fait, du premier croquis à la dernière plantation.'],
            ]],
            'prestations' => ['Nos prestations – Canopées', [
                ['type' => 'presentation', 'titre' => 'Découvrez nos réalisations à Montpellier et alentours', 'texte' => 'Découvrez notre savoir-faire en images !'],
            ]],
            'tarifs' => ['Nos tarifs – Canopées', [
                ['type' => 'presentation', 'titre' => 'Nos tarifs', 'texte' => 'Nos tarifs varient selon la surface, la complexité du terrain et vos besoins spécifiques. Voici un aperçu de nos prestations, contactez-nous pour un devis personnalisé et gratuit.'],
                ['type' => 'etape', 'texte' => 'Vous nous contactez'],
                ['type' => 'etape', 'texte' => 'Nous étudions votre besoin'],
                ['type' => 'etape', 'texte' => 'Vous recevez un devis gratuit sous 48h'],
                ['type' => 'appel-action', 'titre' => "Un projet en tête ? Contactez-nous aujourd'hui :"],
            ]],
            'contact' => ['Contact – Canopées', [
                ['type' => 'presentation', 'titre' => 'Contactez-nous', 'texte' => 'Nous vous répondrons avec grand plaisir !'],
            ]],
            'cgu' => ["Conditions Générales d'Utilisation", [
                ['type' => 'paragraphe', 'titre' => '1. Objet', 'texte' => "Les présentes conditions générales d'utilisation régissent l'accès et l'utilisation du site internet de Canopées. En naviguant sur ce site, l'utilisateur accepte sans réserve les présentes conditions."],
                ['type' => 'paragraphe', 'titre' => '2. Accès au site', 'texte' => "Le site est accessible gratuitement à tout utilisateur disposant d'un accès à internet. Canopées met tout en œuvre pour assurer un accès continu au site, mais ne peut être tenue responsable en cas d'interruption pour maintenance ou pour toute cause indépendante de sa volonté."],
                ['type' => 'paragraphe', 'titre' => '3. Propriété intellectuelle', 'texte' => "L'ensemble des contenus présents sur ce site (textes, images, logo, mise en page) est la propriété exclusive de Canopées, sauf mention contraire. Toute reproduction, totale ou partielle, est interdite sans autorisation préalable."],
                ['type' => 'paragraphe', 'titre' => '4. Responsabilité', 'texte' => "Canopées s'efforce de fournir des informations exactes et à jour, mais ne saurait garantir l'exactitude, la complétude ou l'actualité de l'ensemble des contenus diffusés sur le site."],
                ['type' => 'paragraphe', 'titre' => '5. Données personnelles', 'texte' => "Les informations transmises via le formulaire de contact sont utilisées uniquement pour répondre aux demandes des utilisateurs. Conformément à la réglementation en vigueur, vous disposez d'un droit d'accès, de rectification et de suppression de vos données."],
                ['type' => 'paragraphe', 'titre' => '6. Modification des CGU', 'texte' => "Canopées se réserve le droit de modifier les présentes conditions à tout moment. Il est conseillé à l'utilisateur de consulter régulièrement cette page."],
            ]],
            'cgv' => ['Conditions Générales de Vente', [
                ['type' => 'paragraphe', 'titre' => '1. Objet', 'texte' => "Les présentes conditions générales de vente s'appliquent à toutes les prestations proposées par Canopées : conception et réalisation d'espaces verts, entretien, taille de haies, élagage et abattage d'arbres, valorisation des déchets verts."],
                ['type' => 'paragraphe', 'titre' => '2. Devis et tarifs', 'texte' => 'Les tarifs affichés sur le site sont indicatifs et donnés à titre de base de départ. Un devis personnalisé et gratuit est systématiquement établi avant toute intervention, en fonction de la surface, de la complexité du terrain et des besoins spécifiques du client.'],
                ['type' => 'paragraphe', 'titre' => '3. Commande', 'texte' => "Toute demande de devis effectuée via le site ou le formulaire de contact ne constitue pas un engagement contractuel. La prestation n'est confirmée qu'après acceptation du devis par le client."],
                ['type' => 'paragraphe', 'titre' => '4. Modalités de paiement', 'texte' => 'Le règlement s\'effectue selon les modalités précisées sur le devis, généralement à réception de la prestation ou selon un échéancier convenu pour les chantiers de plus grande ampleur.'],
                ['type' => 'paragraphe', 'titre' => "5. Délais d'exécution", 'texte' => "Les délais d'intervention sont communiqués à titre indicatif lors de l'établissement du devis. Ils peuvent varier selon les conditions météorologiques ou la disponibilité des équipes."],
                ['type' => 'paragraphe', 'titre' => '6. Droit de rétractation', 'texte' => "Conformément à la réglementation, le client particulier dispose d'un délai de rétractation de 14 jours à compter de l'acceptation du devis, sauf en cas d'exécution immédiate expressément demandée."],
                ['type' => 'paragraphe', 'titre' => '7. Litiges', 'texte' => "En cas de litige, une solution amiable sera recherchée en priorité. À défaut d'accord, les tribunaux compétents seront ceux du lieu du siège social de Canopées."],
            ]],
            'mentions-legales' => ['Mentions légales', [
                ['type' => 'paragraphe', 'titre' => 'Éditeur du site', 'texte' => "Le présent site est édité par Canopées, société spécialisée dans la création et l'entretien d'espaces verts, dont le siège se situe à La belle adresse, 34000 Montpellier.\nTéléphone : 00 11 22 33 44"],
                ['type' => 'paragraphe', 'titre' => 'Directeur de la publication', 'texte' => 'La direction de la publication est assurée par les fondateurs de Canopées.'],
                ['type' => 'paragraphe', 'titre' => 'Hébergement', 'texte' => "Ce site est hébergé par un prestataire d'hébergement web, dont les coordonnées peuvent être communiquées sur simple demande via le formulaire de contact."],
                ['type' => 'paragraphe', 'titre' => 'Propriété intellectuelle', 'texte' => "L'ensemble des éléments présents sur ce site (textes, images, logo) est protégé par le droit de la propriété intellectuelle. Toute reproduction sans autorisation est interdite."],
                ['type' => 'paragraphe', 'titre' => 'Données personnelles et cookies', 'texte' => "Ce site ne collecte que les données transmises volontairement via le formulaire de contact, dans le seul but d'y répondre. Aucune donnée n'est cédée à des tiers. Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression de vos données en nous contactant directement."],
            ]],
        ];

        foreach ($pages as $slug => [$titre, $sections]) {
            $page = (new Page())->setSlug($slug)->setTitre($titre);

            foreach ($sections as $index => $donnees) {
                $page->addSection(
                    (new Section())
                        ->setType($donnees['type'])
                        ->setTitre($donnees['titre'] ?? null)
                        ->setSousTitre($donnees['sousTitre'] ?? null)
                        ->setTexte($donnees['texte'] ?? null)
                        ->setImage($donnees['image'] ?? null)
                        ->setAlt($donnees['alt'] ?? null)
                        ->setIcone($donnees['icone'] ?? null)
                        ->setOrdre($index + 1)
                );
            }

            // persist() sur la page ne suffit pas pour les sections : on les persiste une par une
            $manager->persist($page);
            foreach ($page->getSections() as $section) {
                $manager->persist($section);
            }
        }
    }

    /**
     * Deux messages d'exemple, pour avoir quelque chose à lire dans le back-office.
     *
     * @param array<string, Prestation> $prestations
     */
    private function chargerMessages(ObjectManager $manager, array $prestations): void
    {
        $manager->persist(
            (new Message())
                ->setNom('Dupont')->setPrenom('Marie')->setMail('marie.dupont@exemple.fr')
                ->setTelephone('06 12 34 56 78')->setAdresse('12 rue des Lilas')
                ->setCodePostal('34000')->setVille('Montpellier')
                ->setMessage("Bonjour, j'aurais besoin d'un devis pour l'élagage de deux chênes dans mon jardin.")
                ->setRgpd(true)
                ->setPrestation($prestations['elagage'])
        );

        $manager->persist(
            (new Message())
                ->setNom('Martin')->setPrenom('Paul')
                ->setTelephone('07 98 76 54 32')->setAdresse('5 avenue de la Gare')
                ->setCodePostal('34170')->setVille('Castelnau-le-Lez')
                ->setMessage('Bonjour, proposez-vous des contrats d\'entretien à l\'année pour une copropriété ?')
                ->setRgpd(true)
        );
    }

    private function chargerAdmins(ObjectManager $manager): void
    {
        // Un compte par fondateur : email => [prénom, mot de passe de développement]
        $admins = [
            'bob@canopees.fr' => ['Bob', 'Canopees2026!'],
            'tom@canopees.fr' => ['Tom', 'Canopees2026!'],
        ];

        foreach ($admins as $email => [$prenom, $motDePasse]) {
            $user = (new User())
                ->setEmail($email)
                ->setNom('Canopées')
                ->setPrenom($prenom)
                ->setRoles(['ROLE_ADMIN']);

            // Le mot de passe n'est jamais stocké en clair : on enregistre son hachage
            $user->setPassword($this->hasher->hashPassword($user, $motDePasse));

            $manager->persist($user);
        }
    }
}
