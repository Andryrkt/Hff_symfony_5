<?php

namespace App\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIdTransformer implements DataTransformerInterface
{
    private $entityManager;
    private $class;

    public function __construct(EntityManagerInterface $entityManager, string $class)
    {
        $this->entityManager = $entityManager;
        $this->class = $class;
    }

    /**
     * Transforms an object (entity) to a string (number).
     *
     * @param  mixed|null $entity
     */
    public function transform($entity): ?string
    {
        if (null === $entity) {
            return '';
        }

        return $entity->getId();
    }

    /**
     * Transforms a string (number) to an object (entity).
     *
     * @param  string $id
     * @throws TransformationFailedException if object (entity) is not found.
     */
    public function reverseTransform($id): mixed
    {
        // no issue number? It's optional, so that's ok
        if (!$id) {
            return null;
        }

        $entity = $this->entityManager
            ->getRepository($this->class)
            // query for the entity with this id
            ->find($id);

        if (null === $entity) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An entity with number "%s" does not exist!',
                $id
            ));
        }

        return $entity;
    }
}
