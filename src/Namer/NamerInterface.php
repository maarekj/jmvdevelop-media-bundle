<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Namer;

use Symfony\Component\HttpFoundation\File\File;

interface NamerInterface
{
    public function path(File $file, string $context, ?string $name): string;

    public function filename(File $file, string $context, ?string $name): string;
}
