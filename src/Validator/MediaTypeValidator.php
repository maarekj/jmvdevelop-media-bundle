<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Validator;

use JmvDevelop\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class MediaTypeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof MediaType) {
            throw new UnexpectedTypeException($constraint, MediaType::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!($value instanceof Media)) {
            throw new InvalidArgumentException();
        }

        $types = (array) $constraint->type;

        if (false === \in_array($value->getType(), $types, true)) {
            $this->context
                ->buildViolation('media_type.invalid_type')
                ->setParameter('{{ wanted }}', \implode(', ', $types))
                ->setParameter('{{ given }}', $value->getType())
                ->addViolation();
        }
    }
}
