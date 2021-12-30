<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Namer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class DefaultNamer implements NamerInterface
{
    private AsciiSlugger $slugger;

    public function __construct()
    {
        $this->slugger = new AsciiSlugger();
    }

    public function path(File $file, string $context, ?string $name): string
    {
        return $context.'/'.$this->filename(file: $file, context: $context, name: $name);
    }

    public function filename(File $file, string $context, ?string $name): string
    {
        $guessExtension = $file->guessExtension();
        $extension = null !== $guessExtension ? $guessExtension : $file->getExtension();

        $uniqid = \uniqid();
        $a = $uniqid[0] ?? 'a';
        $b = $uniqid[1] ?? 'a';
        $c = $uniqid[2] ?? 'a';

        $name = null !== $name && '' !== $name ? \trim($name) : \trim($this->getDefaultName(file: $file));
        $name = '' === $name ? $uniqid : $name;
        $name = $this->slugger->slug($name, '-', 'en');

        return $a.$b.$c.'/'.$name.'.'.$extension;
    }

    private function getDefaultName(File $file): string
    {
        if ($file instanceof UploadedFile) {
            return \pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
        } else {
            return $file->getFilename();
        }
    }
}
