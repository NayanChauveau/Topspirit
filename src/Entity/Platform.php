<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\PlatformRepository;



/**
 * @ORM\Entity(repositoryClass=PlatformRepository::class)
 * @Vich\Uploadable
 */
class Platform
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     * @Assert\NotNull
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $banner;

    /**
     * @Vich\UploadableField(mapping="platforms", fileNameProperty="banner")
     * 
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="platforms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *      min = 20,
     *      max = 200,
     *      minMessage = "Votre description doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre description ne doit pas faire plus de {{ limit }} caractères"
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Subscriptions::class, mappedBy="platform")
     */
    private $subscriptions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripeId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $monthPriceId;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $endOfSubscription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $redirectToken;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $monthRedirect;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $actualMonth;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerAlt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }
    
	/**
	 * 
	 * @return File|null
	 */
	function getImageFile() {
                                                     return $this->imageFile;
                                                 }
	
	/**
	 * 
	 * @param File|null $imageFile 
	 * @return Platform
	 */
	function setImageFile(File $imageFile = null): self {
                                                     $this->imageFile = $imageFile;
                                             
                                                     if ($imageFile) {
                                                         $this->createdAt = new  \DateTimeImmutable();
                                                     }
                                             
                                                     return $this;
                                                 }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Subscriptions[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscriptions $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setPlatform($this);
        }

        return $this;
    }

    public function removeSubscription(Subscriptions $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getPlatform() === $this) {
                $subscription->setPlatform(null);
            }
        }

        return $this;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function setStripeId(?string $stripeId): self
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    public function getMonthPriceId(): ?string
    {
        return $this->monthPriceId;
    }

    public function setMonthPriceId(?string $monthPriceId): self
    {
        $this->monthPriceId = $monthPriceId;

        return $this;
    }

    public function getEndOfSubscription(): ?\DateTimeImmutable
    {
        return $this->endOfSubscription;
    }

    public function setEndOfSubscription(?\DateTimeImmutable $endOfSubscription): self
    {
        $this->endOfSubscription = $endOfSubscription;

        return $this;
    }

    public function getRedirectToken(): ?string
    {
        return $this->redirectToken;
    }

    public function setRedirectToken(?string $redirectToken): self
    {
        $this->redirectToken = $redirectToken;

        return $this;
    }

    public function getMonthRedirect(): ?int
    {
        return $this->monthRedirect;
    }

    public function setMonthRedirect(?int $monthRedirect): self
    {
        $this->monthRedirect = $monthRedirect;

        return $this;
    }

    public function getActualMonth(): ?\DateTimeImmutable
    {
        return $this->actualMonth;
    }

    public function setActualMonth(\DateTimeImmutable $actualMonth): self
    {
        $this->actualMonth = $actualMonth;

        return $this;
    }

    public function getBannerAlt(): ?string
    {
        return $this->bannerAlt;
    }

    public function setBannerAlt(?string $bannerAlt): self
    {
        $this->bannerAlt = $bannerAlt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
