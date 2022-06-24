<?php

namespace App\Message;

use App\Entity\RabbitTasks;

class ToCheckMessage
{
    public function __construct(
        private array $addresses,
        private ?RabbitTasks $relatedEntity = null
    ) {}

    /**
     * @return array
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    /**
     * @return RabbitTasks
     */
    public function getRelatedEntity(): ?RabbitTasks
    {
        return $this->relatedEntity;
    }

    /**
     * @param RabbitTasks $relatedEntity
     */
    public function setRelatedEntity(RabbitTasks $relatedEntity): void
    {
        $this->relatedEntity = $relatedEntity;
    }
}


