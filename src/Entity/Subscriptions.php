<?php

namespace App\Entity;

use App\Repository\SubscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionsRepository::class)
 */
class Subscriptions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Platform::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $platform;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $paymentIntent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customerId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productId;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $creationDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPlatform(): ?platform
    {
        return $this->platform;
    }

    public function setPlatform(?platform $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    public function getPaymentIntent(): ?string
    {
        return $this->paymentIntent;
    }

    public function setPaymentIntent(string $paymentIntent): self
    {
        $this->paymentIntent = $paymentIntent;

        return $this;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeImmutable $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }
}
