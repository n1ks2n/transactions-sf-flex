<?php
declare(strict_types=1);

namespace App\Entity\Abstraction;

use DateTimeInterface;

interface SoftDeletable
{
    /**
     * @return DateTimeInterface
     */
    public function getDeletedAt(): DateTimeInterface;

    /**
     * @param DateTimeInterface $deletedAt
     *
     * @return mixed self
     */
    public function setDeletedAt(DateTimeInterface $deletedAt): self;
}
