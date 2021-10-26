<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Graphql;

use GraphQL\Error\Error;
use JmvDevelop\MediaBundle\Entity\Media;
use JmvDevelop\MediaBundle\UrlGenerator\MediaUrlGeneratorInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

final class ImageTypeHelper
{
    public function __construct(private CacheManager $cacheManager, private MediaUrlGeneratorInterface $mediaUrlGenerator)
    {
    }

    public function resolveFilteredUrl(Media $root, string $filter): string
    {
        if (Media::TYPE_IMAGE === $root->getType()) {
            if ('reference' === $filter) {
                return $this->mediaUrlGenerator->generateUrl($root->getKey());
            } else {
                return $this->cacheManager->getBrowserPath($root->getKey(), $filter);
            }
        }

        $id = $root->getId() ?? 0;
        throw new Error(\sprintf('Media %s must be an image media', $id));
    }
}
