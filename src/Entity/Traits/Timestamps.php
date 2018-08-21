<?php
declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\Abstraction\Timestampable;
use DateTimeInterface;

trait Timestamps
{
    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     *
     * @return mixed self
     */
    public function setCreatedAt(DateTimeInterface $createdAt): Timestampable
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface $updatedAt
     *
     * @return mixed self
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): Timestampable
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}