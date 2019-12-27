<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RabbitMq Entity
 *
 * @ApiResource(
 *     itemOperations={
 *     },
 *     collectionOperations={
 *      "post"={
 *             "method"="POST",
 *             "route_name"="post-rabbit-mq",
 *             "swagger_context" = {"summary" = "Publish rabbitMq message."},
 *          }
 *     }
 * )
 */
class RabbitMq
{
    /**
     * @var int
     */
    private $id;

    /**
     * Construct
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
