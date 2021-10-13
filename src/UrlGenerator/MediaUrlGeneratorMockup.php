<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\UrlGenerator;

final class MediaUrlGeneratorMockup implements MediaUrlGeneratorInterface
{
    public function generateUrl(string $key): string
    {
        return \sprintf('https://www.baseimageurl.com/%s', $key);
    }
}
