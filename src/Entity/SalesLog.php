<?php

namespace App\Entity;

use App\Repository\SalesLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalesLogRepository::class)]
class SalesLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $quantitySold = null;

    #[ORM\Column]
    private ?float $announcedTotal = null;

    #[ORM\Column]
    private ?float $calculatedTotal = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $shift = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $worker = null;

    public function getId(): ?int { return $this->id; }

    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(?Product $product): static { $this->product = $product; return $this; }

    public function getQuantitySold(): ?int { return $this->quantitySold; }
    public function setQuantitySold(int $quantitySold): static { $this->quantitySold = $quantitySold; return $this; }

    public function getAnnouncedTotal(): ?float { return $this->announcedTotal; }
    public function setAnnouncedTotal(float $announcedTotal): static { $this->announcedTotal = $announcedTotal; return $this; }

    public function getCalculatedTotal(): ?float { return $this->calculatedTotal; }
    public function setCalculatedTotal(float $calculatedTotal): static { $this->calculatedTotal = $calculatedTotal; return $this; }

    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): static { $this->date = $date; return $this; }

    public function getWorker(): ?User { return $this->worker; }
    public function setWorker(?User $worker): static { $this->worker = $worker; return $this; }

    public function getShift(): ?string { return $this->shift; }
    public function setShift(?string $shift): static { $this->shift = $shift; return $this; }
}
