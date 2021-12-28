<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Filter;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use function Psl\Type\int;
use function Psl\Type\shape;
use function Psl\Type\union;

final class ResizeFilterLoader implements LoaderInterface
{
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        $options = union(shape([
            'max_width' => int(),
        ]), shape([
            'max_height' => int(),
        ]), shape([
            'width' => int(),
        ]), shape([
            'height' => int(),
        ]))->coerce($options);

        $maxWidth = $options['max_width'] ?? null;
        $maxHeight = $options['max_height'] ?? null;
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;

        $size = $image->getSize();

        if (null !== $maxWidth) {
            $currentWidth = $size->getWidth();
            if ($currentWidth >= $maxWidth) {
                return $image->resize($size->widen($maxWidth));
            } else {
                return $image;
            }
        } elseif (null !== $maxHeight) {
            $currentHeight = $size->getHeight();
            if ($currentHeight >= $maxWidth) {
                return $image->resize($size->heighten($maxHeight));
            } else {
                return $image;
            }
        } elseif (null !== $width) {
            return $image->resize($size->widen($width));
        } elseif (null !== $height) {
            return $image->resize($size->heighten($height));
        }

        throw new InvalidArgumentException('Expected max_width, max_height, width or height, none given');
    }
}
