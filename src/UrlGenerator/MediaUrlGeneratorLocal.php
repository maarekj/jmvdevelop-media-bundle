<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\UrlGenerator;

final class MediaUrlGeneratorLocal implements MediaUrlGeneratorInterface
{
    public function __construct(private string $publicPath)
    {
    }

    public function generateUrl(string $key): string
    {
        return \sprintf('%s/%s', $this->publicPath, $key);
    }
}
