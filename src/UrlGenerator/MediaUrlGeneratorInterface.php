<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\UrlGenerator;

interface MediaUrlGeneratorInterface
{
    public function generateUrl(string $key): string;
}
