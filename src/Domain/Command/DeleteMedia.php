<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Domain\Command;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\Logger\Annotation\LogFields;
use JmvDevelop\Domain\Logger\Annotation\LogMessage;
use JmvDevelop\MediaBundle\Entity\Media;

/** @LogMessage(expression="'Delete image ' ~ o.getImage().getId()") */
final class DeleteMedia implements CommandInterface
{
    use MediaReturnValueTrait;

    //------------------------------------------------------------------------

    public function __construct(
        /** @LogFields(fields={"id", "name", "type", "context"}) */
        private Media $media
    ) {
    }

    public function getMedia(): Media
    {
        return $this->media;
    }
}
