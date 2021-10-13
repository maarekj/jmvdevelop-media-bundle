<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Validator;

use JmvDevelop\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint for media type.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class MediaType extends Constraint
{
    /**
     * @param Media::TYPE_*|list<Media::TYPE_*> $type
     * @param null|string[]                     $groups
     */
    public function __construct(
        public array|string $type,
        ?array $groups = null,
    ) {
        parent::__construct([], $groups);
    }

    /** {@inheritdoc} */
    public function getDefaultOption()
    {
        return 'type';
    }
}
