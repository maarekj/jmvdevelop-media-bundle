<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle;

use JmvDevelop\Domain\HandlerAwareInterface;
use JmvDevelop\Domain\HandlerAwareTrait;
use JmvDevelop\MediaBundle\Domain\Command\CopyMedia;
use JmvDevelop\MediaBundle\Domain\Command\DeleteMedia;
use JmvDevelop\MediaBundle\Domain\Command\MoveMedia;
use JmvDevelop\MediaBundle\Entity\Media;
use JmvDevelop\MediaBundle\Repository\MediaRepository;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class MediaHelper implements HandlerAwareInterface
{
    use HandlerAwareTrait;

    public function __construct(private MediaRepository $mediaRepo)
    {
    }

    public function deleteMedia(?Media $image): void
    {
        if (null !== $image) {
            $this->domainHandler->handle(new DeleteMedia($image));
        }
    }

    public function copyIfNeed(Media|string|int|null $mediaId, string $context): ?Media
    {
        if (null === $mediaId) {
            return null;
        }

        $media = $this->findMedia($mediaId);
        if (null === $media) {
            return null;
        }
        if ($context === $media->getContext()) {
            return $media;
        } elseif (Media::CONTEXT_TMP === $media->getContext()) {
            $cmd = new MoveMedia($media, $context);
            $this->domainHandler->handle($cmd);

            return $cmd->getReturnValue();
        } else {
            $cmd = new CopyMedia($media, $context);
            $this->domainHandler->handle($cmd);

            return $cmd->getReturnValue();
        }
    }

    private function findMedia(Media|string|int|null $id): ?Media
    {
        if (null === $id || '' === $id) {
            return null;
        }

        if ($id instanceof Media) {
            return $id;
        }

        return $this->mediaRepo->find($id);
    }
}
