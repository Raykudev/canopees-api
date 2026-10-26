<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Repository\RealisationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealisationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['realisation:read']],
    order: ['dateRealisation' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['prestation' => 'exact'])]
#[ApiFilter(BooleanFilter::class, properties: ['accueil'])]

class Realisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['realisation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['realisation:read'])]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Groups(['realisation:read'])]
    private ?string $alt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['realisation:read'])]
    private ?\DateTimeImmutable $dateRealisation = null;

    #[ORM\Column]
    private ?bool $accueil = null;

    #[ORM\ManyToOne(inversedBy: 'realisations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['realisation:read'])]
    private ?Prestation $prestation = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateRealisation(): ?\DateTimeImmutable
    {
        return $this->dateRealisation;
    }

    public function setDateRealisation(\DateTimeImmutable $dateRealisation): static
    {
        $this->dateRealisation = $dateRealisation;

        return $this;
    }

    public function isAccueil(): ?bool
    {
        return $this->accueil;
    }

    public function setAccueil(bool $accueil): static
    {
        $this->accueil = $accueil;

        return $this;
    }

    public function getPrestation(): ?Prestation
    {
        return $this->prestation;
    }

    public function setPrestation(?Prestation $prestation): static
    {
        $this->prestation = $prestation;

        return $this;
    }
}
