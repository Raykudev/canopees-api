<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    // Dossier (dans public/) où sont rangées les images de cette entité
    public const DOSSIER_IMAGES = 'uploads/pages';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['page:read'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['page:read'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['page:read'])]
    private ?string $texte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['page:read'])]
    private ?string $alt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['page:read'])]
    private ?string $sousTitre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icone = null;

    #[ORM\Column]
    private ?int $ordre = null;

    #[ORM\ManyToOne(inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Page $page = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(?string $texte): static
    {
        $this->texte = $texte;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function getSousTitre(): ?string
    {
        return $this->sousTitre;
    }

    public function setSousTitre(?string $sousTitre): static
    {
        $this->sousTitre = $sousTitre;

        return $this;
    }

    public function getIcone(): ?string
    {
        return $this->icone;
    }

    public function setIcone(?string $icone): static
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

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): static
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Chemin public de l'image, exposé dans l'API sous le nom « image »
     * (en base, on ne stocke que le nom du fichier).
     */
    #[Groups(['page:read'])]
    #[SerializedName('image')]
    public function getImageUrl(): ?string
    {
        return $this->image ? '/' . self::DOSSIER_IMAGES . '/' . $this->image : null;
    }

    /**
     * Chemin public de l'image, exposé dans l'API sous le nom « icone »
     * (en base, on ne stocke que le nom du fichier).
     */
    #[Groups(['page:read'])]
    #[SerializedName('icone')]
    public function getIconeUrl(): ?string
    {
        return $this->icone ? '/' . self::DOSSIER_IMAGES . '/' . $this->icone : null;
    }
}
