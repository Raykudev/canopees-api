<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use App\Repository\PublicCibleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicCibleRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/publics-cibles'),
    ],
    normalizationContext: ['groups' => ['public_cible:read']],
    order: ['ordre' => 'ASC'],
)]
class PublicCible
{
    // Dossier (dans public/) où sont rangées les images de cette entité
    public const DOSSIER_IMAGES = 'uploads/publics-cibles';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['public_cible:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['public_cible:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    #[Groups(['public_cible:read'])]
    private ?string $photoAlt = null;

    #[ORM\Column(length: 255)]
    private ?string $icone = null;

    #[ORM\Column]
    private ?int $ordre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->nom ?? '';
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhotoAlt(): ?string
    {
        return $this->photoAlt;
    }

    public function setPhotoAlt(string $photoAlt): static
    {
        $this->photoAlt = $photoAlt;

        return $this;
    }

    public function getIcone(): ?string
    {
        return $this->icone;
    }

    public function setIcone(string $icone): static
    {
        $this->icone = $icone;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Chemin public de la photo, exposé dans l'API sous le nom « photo »
     * (en base, on ne stocke que le nom du fichier).
     */
    #[Groups(['public_cible:read'])]
    #[SerializedName('photo')]
    public function getPhotoUrl(): ?string
    {
        return $this->photo ? '/' . self::DOSSIER_IMAGES . '/' . $this->photo : null;
    }

    /**
     * Chemin public de l'icône ronde, exposé dans l'API sous le nom « icone ».
     */
    #[Groups(['public_cible:read'])]
    #[SerializedName('icone')]
    public function getIconeUrl(): ?string
    {
        return $this->icone ? '/' . self::DOSSIER_IMAGES . '/' . $this->icone : null;
    }
}
