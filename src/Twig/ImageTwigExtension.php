<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Twig;

use JmvDevelop\MediaBundle\Entity\Media;
use JmvDevelop\MediaBundle\Graphql\ImageTypeHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ImageTwigExtension extends AbstractExtension
{
    public function __construct(private ImageTypeHelper $helper)
    {
    }

    public function getFilters()
    {
        return [
            new TwigFilter('image_reference', function (?Media $image): ?string {
                return $this->imageUrl($image, 'reference');
            }),
            new TwigFilter('image_url', function (?Media $image, string $filter): ?string {
                return $this->imageUrl($image, $filter);
            }),
        ];
    }

    private function imageUrl(?Media $image, string $filter): ?string
    {
        if (null === $image) {
            return null;
        }

        return $this->helper->resolveFilteredUrl(root: $image, filter: $filter);
    }
}
