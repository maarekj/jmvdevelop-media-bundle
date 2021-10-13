<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Domain\Handler;

use Doctrine\Persistence\ManagerRegistry;
use JmvDevelop\Domain\AbstractHandler;
use JmvDevelop\Domain\Utils\ValidatorUtils;
use JmvDevelop\MediaBundle\Domain\Command\CopyMedia;
use JmvDevelop\MediaBundle\Entity\Media;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;

final class CopyMediaHandler extends AbstractHandler
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private ValidatorUtils $validatorUtils,
        private FilesystemOperator $filesystem
    ) {
        parent::__construct(CopyMedia::class);
    }

    protected function handleCommand(CopyMedia $command): void
    {
        $this->validatorUtils->validateOrThrow($command);

        $srcImage = $command->getMedia();
        if ($srcImage->getContext() != $command->getContext()) {
            $targetImage = new Media(
                type: $srcImage->getType(),
                context: $srcImage->getContext(),
                name: $srcImage->getName(),
                width: $srcImage->getWidth(),
                height: $srcImage->getHeight(),
            );

            $this->validatorUtils->validateOrThrow($targetImage, null, ['Default', 'copyMedia']);

            $em = $this->doctrine->getManager();
            $em->persist($targetImage);
            $em->flush();

            try {
                if (false === $this->filesystem->fileExists($targetImage->getKey())) {
                    $this->filesystem->copy($srcImage->getKey(), $targetImage->getKey());
                }
                $command->setReturnValue($targetImage);
            } catch (FilesystemException $exception) {
                $command->setReturnValue(null);
            }
        }
    }
}
