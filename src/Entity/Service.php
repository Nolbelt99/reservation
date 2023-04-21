<?php

namespace App\Entity;

use App\Model\IdentifierTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Validator as AppAssert;

 /**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 * @Gedmo\TranslationEntity(class="App\Entity\ServiceTranslation")
 * @AppAssert\ReservationCondition()
 */
class Service implements Translatable
{
    use IdentifierTrait;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", name="`lead`", length=255)
     */
    private $lead;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImageCollection;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $reservationType;

    /**
     * @ORM\Column(type="integer")
     */
    private $avaibleSameTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $minDay;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $giftImage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $minGiftDay;

    /**
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(name="gift_service_id", referencedColumnName="id")
     */
    private $giftService;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $giftText;
    
    /**
     * @ORM\OneToMany(targetEntity="Gallery", mappedBy="service", cascade={"persist", "remove"})
     */
    private $galleryImages;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $serviceType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity="Service")
     * @ORM\JoinTable(name="related_services")
     */
    private $relatedServices;

    /**
     * @ORM\ManyToMany(targetEntity="Service")
     * @ORM\JoinTable(name="condition_services")
     */
    private $conditionServices;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $companyName;
    
    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $companyPriority;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $beds;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $extraBeds;




    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $assurance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cleaningCharge;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $captainType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $captainPrice;


    
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $halfDayPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fullDayPrice;


    /**
     * @ORM\OneToMany(
     *   targetEntity="ServiceTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->galleryImages = new ArrayCollection();
        $this->relatedServices = new ArrayCollection();
        $this->conditionServices = new ArrayCollection();
    }

    public function __toString() {
        return (string)$this->getName();
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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getLead(): ?string
    {
        return $this->lead;
    }

    public function setLead(string $lead): self
    {
        $this->lead = $lead;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getCoverImageCollection(): ?string
    {
        return $this->coverImageCollection;
    }

    public function setCoverImageCollection(string $coverImageCollection): self
    {
        $this->coverImageCollection = $coverImageCollection;

        return $this;
    }

    public function getReservationType(): ?string
    {
        return $this->reservationType;
    }

    public function setReservationType(string $reservationType): self
    {
        $this->reservationType = $reservationType;

        return $this;
    }

    public function getBeds(): ?int
    {
        return $this->beds;
    }

    public function setBeds(?int $beds): self
    {
        $this->beds = $beds;

        return $this;
    }

    public function getExtraBeds(): ?int
    {
        return $this->extraBeds;
    }

    public function setExtraBeds(?int $extraBeds): self
    {
        $this->extraBeds = $extraBeds;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, ServiceTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(ServiceTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setObject($this);
        }

        return $this;
    }

    public function removeTranslation(ServiceTranslation $translation): self
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getObject() === $this) {
                $translation->setObject(null);
            }
        }

        return $this;
    }

    public function getAvaibleSameTime(): ?int
    {
        return $this->avaibleSameTime;
    }

    public function setAvaibleSameTime(int $avaibleSameTime): self
    {
        $this->avaibleSameTime = $avaibleSameTime;

        return $this;
    }

    public function getMinDay(): ?int
    {
        return $this->minDay;
    }

    public function setMinDay(?int $minDay): self
    {
        $this->minDay = $minDay;

        return $this;
    }

    public function getGiftImage(): ?string
    {
        return $this->giftImage;
    }

    public function setGiftImage(?string $giftImage): self
    {
        $this->giftImage = $giftImage;

        return $this;
    }

    public function getMinGiftDay(): ?int
    {
        return $this->minGiftDay;
    }

    public function setMinGiftDay(int $minGiftDay): self
    {
        $this->minGiftDay = $minGiftDay;

        return $this;
    }

    public function getGiftText(): ?string
    {
        return $this->giftText;
    }

    public function setGiftText(?string $giftText): self
    {
        $this->giftText = $giftText;

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

    /**
     * @return Collection<int, Gallery>
     */
    public function getGalleryImages(): Collection
    {
        return $this->galleryImages;
    }

    public function addGalleryImage(Gallery $galleryImage): self
    {
        if (!$this->galleryImages->contains($galleryImage)) {
            $this->galleryImages->add($galleryImage);
            $galleryImage->setService($this);
        }

        return $this;
    }

    public function removeGalleryImage(Gallery $galleryImage): self
    {
        if ($this->galleryImages->removeElement($galleryImage)) {
            // set the owning side to null (unless already changed)
            if ($galleryImage->getService() === $this) {
                $galleryImage->setService(null);
            }
        }

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getAssurance(): ?int
    {
        return $this->assurance;
    }

    public function setAssurance(?int $assurance): self
    {
        $this->assurance = $assurance;

        return $this;
    }

    public function getCleaningCharge(): ?int
    {
        return $this->cleaningCharge;
    }

    public function setCleaningCharge(?int $cleaningCharge): self
    {
        $this->cleaningCharge = $cleaningCharge;

        return $this;
    }

    public function getCaptainType(): ?string
    {
        return $this->captainType;
    }

    public function setCaptainType(?string $captainType): self
    {
        $this->captainType = $captainType;

        return $this;
    }

    public function getHalfDayPrice(): ?int
    {
        return $this->halfDayPrice;
    }

    public function setHalfDayPrice(?int $halfDayPrice): self
    {
        $this->halfDayPrice = $halfDayPrice;

        return $this;
    }

    public function getFullDayPrice(): ?int
    {
        return $this->fullDayPrice;
    }

    public function setFullDayPrice(?int $fullDayPrice): self
    {
        $this->fullDayPrice = $fullDayPrice;

        return $this;
    }

    public function getCaptainPrice(): ?int
    {
        return $this->captainPrice;
    }

    public function setCaptainPrice(?int $captainPrice): self
    {
        $this->captainPrice = $captainPrice;

        return $this;
    }

    public function getServiceType(): ?string
    {
        return $this->serviceType;
    }

    public function setServiceType(string $serviceType): self
    {
        $this->serviceType = $serviceType;

        return $this;
    }

    public function getGiftService(): ?self
    {
        return $this->giftService;
    }

    public function setGiftService(?self $giftService): self
    {
        $this->giftService = $giftService;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getRelatedServices(): Collection
    {
        return $this->relatedServices;
    }

    public function addRelatedService(Service $relatedService): self
    {
        if (!$this->relatedServices->contains($relatedService)) {
            $this->relatedServices->add($relatedService);
        }

        return $this;
    }

    public function removeRelatedService(Service $relatedService): self
    {
        $this->relatedServices->removeElement($relatedService);

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getConditionServices(): Collection
    {
        return $this->conditionServices;
    }

    public function addConditionService(Service $conditionService): self
    {
        if (!$this->conditionServices->contains($conditionService)) {
            $this->conditionServices->add($conditionService);
        }

        return $this;
    }

    public function removeConditionService(Service $conditionService): self
    {
        $this->conditionServices->removeElement($conditionService);

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getCompanyPriority(): ?int
    {
        return $this->companyPriority;
    }

    public function setCompanyPriority(?int $companyPriority): self
    {
        $this->companyPriority = $companyPriority;

        return $this;
    }
    
}
