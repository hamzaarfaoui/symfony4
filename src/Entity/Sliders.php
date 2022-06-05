<?php

namespace App\Entity;

use App\Repository\SlidersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SlidersRepository::class)
 */
class Sliders
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Products::class)
     */
    private $product;

    /**
     * @ORM\OneToOne(targetEntity=ProductsList::class, mappedBy="slider", cascade={"persist", "remove"})
     */
    private $productsList;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProductsList(): ?ProductsList
    {
        return $this->productsList;
    }

    public function setProductsList(?ProductsList $productsList): self
    {
        // unset the owning side of the relation if necessary
        if ($productsList === null && $this->productsList !== null) {
            $this->productsList->setSlider(null);
        }

        // set the owning side of the relation if necessary
        if ($productsList !== null && $productsList->getSlider() !== $this) {
            $productsList->setSlider($this);
        }

        $this->productsList = $productsList;

        return $this;
    }
}