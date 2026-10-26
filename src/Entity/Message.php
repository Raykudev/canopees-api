<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Attribute\Groups;


#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource(
    operations: [
        new Post(),
    ],
    denormalizationContext: ['groups' => ['message:write']],
)]


class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['message:write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['message:write'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['message:write'])]
    private ?string $mail = null;

    #[ORM\Column(length: 20)]
    #[Groups(['message:write'])]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Groups(['message:write'])]
    private ?string $adresse = null;

    #[ORM\Column(length: 10)]
    #[Groups(['message:write'])]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255)]
    #[Groups(['message:write'])]
    private ?string $ville = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['message:write'])]
    private ?string $message = null;

    #[ORM\Column]
    #[Groups(['message:write'])]
    private ?bool $rgpd = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateEnvoi = null;

    #[ORM\Column]
    private ?bool $lu = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[Groups(['message:write'])]
    private ?Prestation $prestation = null;

    public function __construct()
{
    $this->dateEnvoi = new \DateTimeImmutable();   // maintenant
    $this->lu = false;                              // un nouveau message n'est pas lu
}


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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function isRgpd(): ?bool
    {
        return $this->rgpd;
    }

    public function setRgpd(bool $rgpd): static
    {
        $this->rgpd = $rgpd;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeImmutable
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeImmutable $dateEnvoi): static
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function isLu(): ?bool
    {
        return $this->lu;
    }

    public function setLu(bool $lu): static
    {
        $this->lu = $lu;

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
