<?php

namespace App\Entity;

use App\Repository\AddressStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressStatusRepository::class)]
class AddressStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $address;

    #[ORM\Column(type: 'integer')]
    private $http_status;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getHttpStatus(): int
    {
        return $this->http_status;
    }

    /**
     * @param int $http_status
     */
    public function setHttpStatus(int $http_status): self
    {
        $this->http_status = $http_status;

        return $this;
    }

    public function getCratedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCratedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
