<?php

namespace App\Entity;

use App\Model\IdentifierTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GalleryRepository;

 /**
 * @ORM\Entity(repositoryClass=GalleryRepository::class)
 */
class Gallery
{
    use IdentifierTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="gallery_images")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $service;

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

}
