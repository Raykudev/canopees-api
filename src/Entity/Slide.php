<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use App\Repository\SlideRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlideRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['slide:read']],
    order: ['ordre' => 'ASC'],
)]
class Slide
{
    // Dossier (dans public/) où sont rangées les images de cette entité
    public const DOSSIER_IMAGES = 'uploads/slides';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['slide:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['slide:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Groups(['slide:read'])]
    private ?string $alt = null;

    #[ORM\Column]
    private ?int $ordre = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): static
    {
        $this->alt = $alt;

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
     * Chemin public de l'image, exposé dans l'API sous le nom « image »
     * (en base, on ne stocke que le nom du fichier).
     */
    #[Groups(['slide:read'])]
    #[SerializedName('image')]
    public function getImageUrl(): ?string
    {
        return $this->image ? '/' . self::DOSSIER_IMAGES . '/' . $this->image : null;
    }
}
