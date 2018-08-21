<?php
declare(strict_types=1);

namespace App\Listener;

use App\Entity\Abstraction\Timestampable;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class TimestampableListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Timestampable) {
            if ($entity->getCreatedAt() === null) {
                $entity->setCreatedAt(new \DateTime());
            }

            $entity->setUpdatedAt(new \DateTime());
        }
    }
}
