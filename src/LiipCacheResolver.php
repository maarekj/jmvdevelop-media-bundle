<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle;

use JmvDevelop\MediaBundle\UrlGenerator\MediaUrlGeneratorInterface;
use League\Flysystem\FilesystemOperator;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;

final class LiipCacheResolver implements ResolverInterface
{
    public function __construct(
        private FilesystemOperator $flysystem,
        private MediaUrlGeneratorInterface $urlGenerator
    ) {
    }

    public function isStored($path, $filter): bool
    {
        return $this->flysystem->fileExists($this->getFilePath($path, $filter));
    }

    public function resolve($path, $filter): string
    {
        return $this->urlGenerator->generateUrl($this->getFilePath($path, $filter));
    }

    public function store(BinaryInterface $binary, $path, $filter): void
    {
        $filePath = $this->getFilePath($path, $filter);
        $this->flysystem->write($filePath, $binary->getContent());
        $this->flysystem->setVisibility($filePath, 'public');
    }

    /**
     * @param string[] $paths   The paths where the original files are expected to be
     * @param string[] $filters The imagine filters in effect
     */
    public function remove(array $paths, array $filters): void
    {
        if (empty($paths) && empty($filters)) {
            return;
        }

        if (empty($paths)) {
            foreach ($filters as $filter) {
                $filterCacheDir = 'cache/generated/'.$filter;
                $this->flysystem->deleteDirectory($filterCacheDir);
            }

            return;
        }

        foreach ($paths as $path) {
            foreach ($filters as $filter) {
                $filePath = $this->getFilePath($path, $filter);
                if ($this->flysystem->fileExists($filePath)) {
                    $this->flysystem->delete($filePath);
                }
            }
        }
    }

    private function getFilePath(string $path, string $filter): string
    {
        // crude way of sanitizing URL scheme ("protocol") part
        $path = \str_replace('://', '---', $path);

        return 'cache/generated'.'/'.$filter.'/'.\ltrim($path, '/');
    }
}
