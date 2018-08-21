<?php
declare(strict_types=1);

namespace App\Entity\Abstraction;

use DateTimeInterface;

interface Timestampable
{
    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface;

    /**
     * @param DateTimeInterface $createdAt
     *
     * @return mixed self
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self;

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt(): DateTimeInterface;

    /**
     * @param DateTimeInterface $updatedAt
     *
     * @return mixed self
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self;
}