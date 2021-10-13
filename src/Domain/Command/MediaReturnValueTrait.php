<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Domain\Command;

use JmvDevelop\Domain\Logger\Annotation\LogFields;
use JmvDevelop\MediaBundle\Entity\Media;

trait MediaReturnValueTrait
{
    /** @LogFields(fields={"id", "name", "type", "context"}) */
    protected ?Media $returnValue = null;

    public function getReturnValue(): ?Media
    {
        return $this->returnValue;
    }

    public function setReturnValue(?Media $returnValue): self
    {
        $this->returnValue = $returnValue;

        return $this;
    }
}
