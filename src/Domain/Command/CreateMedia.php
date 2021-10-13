<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Domain\Command;

use JmvDevelop\Domain\CommandInterface;
use JmvDevelop\Domain\Logger\Annotation\LogFields;
use JmvDevelop\Domain\Logger\Annotation\LogMessage;
use JmvDevelop\MediaBundle\Entity\Media;
use JmvDevelop\SameAsBundle\SameAs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @LogMessage(expression="'Create new media in context (' ~  o.getContext() ~ ')'")
 */
final class CreateMedia implements CommandInterface
{
    use MediaReturnValueTrait;

    //------------------------------------------------------------------------

    /**
     * @param Media::TYPE_*    $type
     * @param Media::CONTEXT_* $context
     */
    public function __construct(
        #[SameAs(class: Media::class)]
        private string $type,

        /** @LogFields(fields={"path", "filename", "pathname", "size"}) */
        #[Assert\File(maxSize: '10M', mimeTypes: ['image/jpeg', 'image/png'], groups: ['image'])]
        #[Assert\File(maxSize: '50M', mimeTypes: ['video/mp4'], groups: ['video'])]
        private File $file,
        #[SameAs(class: Media::class)]
        private string $context,
    ) {
    }

    #[Assert\Callback]
    public function validateType(ExecutionContextInterface $context): void
    {
        $type = $this->getType();

        if ('image' === $type) {
            $context->getValidator()->inContext($context)->validate($this, null, ['image']);
        } elseif ('video' === $type) {
            $context->getValidator()->inContext($context)->validate($this, null, ['video']);
        }
    }

    /** @return Media::TYPE_* */
    public function getType(): string
    {
        return $this->type;
    }

    /** @return Media::CONTEXT_* */
    public function getContext(): string
    {
        return $this->context;
    }

    public function getFile(): File
    {
        return $this->file;
    }
}
