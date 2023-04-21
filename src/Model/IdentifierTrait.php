<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Identifier
 * @package App\Model
 */
trait IdentifierTrait
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected  $id;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
