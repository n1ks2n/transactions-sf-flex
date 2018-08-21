<?php
declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\Abstraction\SoftDeletable;
use DateTimeInterface;

trait SoftDeletes
{
    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="deleted_at", type="datetime")
     */
    protected $deletedAt;

    /**
     * @return DateTimeInterface
     */
    public function getDeletedAt(): DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTimeInterface $deletedAt
     *
     * @return SoftDeletable|self
     */
    public function setDeletedAt(DateTimeInterface $deletedAt): SoftDeletable
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}