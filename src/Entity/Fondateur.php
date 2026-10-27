<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use App\Repository\FondateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FondateurRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['fondateur:read']],
    order: ['ordre' => 'ASC'],
)]
class Fondateur
{
    // Dossier (dans public/) où sont rangées les images de cette entité
    public const DOSSIER_IMAGES = 'uploads/fondateurs';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fondateur:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['fondateur:read'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    #[Groups(['fondateur:read'])]
    private ?string $role = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['fondateur:read'])]
    private ?string $bio = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fondateur:read'])]
    private ?string $photoAlt = null;

    #[ORM\Column(length: 255)]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?int $ordre = null;

    // Pas de #[Groups] : l'email reste dans le back-office, il n'est jamais envoyé par l'API
    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->prenom ?? '';
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): static
    {
        $this->bio = $bio;

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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Chemin public de la photo, exposé dans l'API sous le nom « photo »
     * (en base, on ne stocke que le nom du fichier).
     */
    #[Groups(['fondateur:read'])]
    #[SerializedName('photo')]
    public function getPhotoUrl(): ?string
    {
        return $this->photo ? '/' . self::DOSSIER_IMAGES . '/' . $this->photo : null;
    }

    /**
     * Chemin public de l'avatar, exposé dans l'API sous le nom « avatar ».
     */
    #[Groups(['fondateur:read'])]
    #[SerializedName('avatar')]
    public function getAvatarUrl(): ?string
    {
        return $this->avatar ? '/' . self::DOSSIER_IMAGES . '/' . $this->avatar : null;
    }
}
