<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Domain\Handler;

use Doctrine\ORM\EntityManager;
use JmvDevelop\Domain\AbstractHandler;
use JmvDevelop\Domain\Exception\DomainException;
use JmvDevelop\Domain\Utils\ValidatorUtils;
use JmvDevelop\MediaBundle\Domain\Command\CreateMedia;
use JmvDevelop\MediaBundle\Entity\Media;
use JmvDevelop\MediaBundle\Namer\NamerInterface;
use JmvDevelop\MediaBundle\Namer\NamerRegistry;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use wapmorgan\MediaFile\Exceptions\FileAccessException;
use wapmorgan\MediaFile\Exceptions\ParsingException;
use wapmorgan\MediaFile\MediaFile;
use wapmorgan\MediaFile\VideoAdapter;
use Webmozart\Assert\Assert;

final class CreateMediaHandler extends AbstractHandler
{
    public function __construct(
        private EntityManager $manager,
        private ValidatorUtils $validatorUtils,
        private FilesystemOperator $filesystem,
        private NamerRegistry $namerRegistry,
        private string $defaultNamerId,
    ) {
        parent::__construct([CreateMedia::class]);
    }

    protected function handleCommand(CreateMedia $command): void
    {
        $this->validatorUtils->validateOrThrow($command, null, ['Default']);
        $media = $this->createMedia($command);
        $this->validatorUtils->validateOrThrow($media, null, ['Default']);

        $this->manager->persist($media);
        $this->manager->flush();

        $command->setReturnValue($media);
    }

    private function createMedia(CreateMedia $command): Media
    {
        $context = $command->getContext();
        $file = $command->getFile();

        $namer = $this->namer($command);
        $filename = $namer->filename(file: $file, context: $command->getContext(), name: $command->getName());
        $targetPath = $namer->path(file: $file, context: $command->getContext(), name: $command->getName());

        $fd = null;
        try {
            $realPath = $file->getRealPath();
            Assert::string($realPath);
            $fd = \fopen($realPath, 'r');
            Assert::resource($fd);

            $this->filesystem->writeStream($targetPath, $fd, ['visibility' => 'public']);

            $type = $command->getType();
            if (Media::TYPE_IMAGE === $type) {
                $size = $this->getSizeForImage($realPath);
            } elseif (Media::TYPE_VIDEO === $type) {
                $size = $this->getSizeForVideo($realPath);
            } else {
                $size = [0, 0];
            }

            return new Media(
                type: $command->getType(),
                context: $context,
                name: $filename,
                width: $size[0],
                height: $size[1],
            );
        } catch (FilesystemException|\InvalidArgumentException $e) {
            throw new DomainException($e->getMessage(), false, (int) $e->getCode(), $e);
        } finally {
            if (null != $fd) {
                \fclose($fd);
            }
        }
    }

    private function namer(CreateMedia $command): NamerInterface
    {
        $namerId = $command->getNamer();
        if (null === $namerId) {
            $namerId = $this->defaultNamerId;
        }

        return $this->namerRegistry->getNamerOrThrow(id: $namerId);
    }

    /** @return array{0: int, 1: int} */
    private function getSizeForImage(string $realPath): array
    {
        [$width, $height,] = \getimagesize($realPath);

        if (\is_numeric($width) && \is_numeric($height)) {
            return [(int) $width, (int) $height];
        }

        return [0, 0];
    }

    /** @return array{0: int, 1: int} */
    private function getSizeForVideo(string $realPath): array
    {
        try {
            /** @var MediaFile $media */
            $media = MediaFile::open($realPath);
            if ($media->isVideo()) {
                /** @var VideoAdapter $video */
                $video = $media->getVideo();

                return [(int) $video->getWidth(), (int) $video->getHeight()];
            } else {
                return [0, 0];
            }
        } catch (FileAccessException|ParsingException $e) {
            return [0, 0];
        }
    }
}
