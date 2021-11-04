<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Domain\Command;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\Logger\Annotation\LogFields;
use JmvDevelop\Domain\Logger\Annotation\LogMessage;
use JmvDevelop\MediaBundle\Entity\Media;

/**
 * @LogMessage(expression="'Move media ' ~ o.getMedia().getId() ~ ' to context ' ~ o.getContext()")
 */
final class MoveMedia implements CommandInterface
{
    use MediaReturnValueTrait;

    //------------------------------------------------------------------------

    public function __construct(
        /* @LogFields(fields={"id", "name", "type", "context"}) */
        private Media $media,
        private string $context
    ) {
    }

    public function getMedia(): Media
    {
        return $this->media;
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
