<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\Mime\MimeTypesInterface;

final class LiipUploadLoader implements LoaderInterface
{
    public function __construct(
        private MimeTypesInterface $extensionGuesser,
        private FilesystemOperator $filesystem
    ) {
    }

    /** {@inheritdoc}*/
    public function find($path): BinaryInterface
    {
        $path = (string) $path;
        try {
            $mimeType = $this->filesystem->mimeType($path);

            $extension = $this->getExtension($mimeType);

            return new Binary(
                $this->filesystem->read($path),
                $mimeType,
                $extension
            );
        } catch (FilesystemException $exception) {
            throw new NotLoadableException(\sprintf('Source image "%s" not found.', $path), 0, $exception);
        }
    }

    private function getExtension(?string $mimeType): ?string
    {
        if (null === $mimeType) {
            return null;
        }

        return $this->extensionGuesser->getExtensions($mimeType)[0] ?? null;
    }
}
