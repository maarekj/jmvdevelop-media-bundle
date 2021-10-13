<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Domain\Handler;

use Doctrine\Persistence\ManagerRegistry;
use JmvDevelop\Domain\AbstractHandler;
use JmvDevelop\Domain\Utils\ValidatorUtils;
use JmvDevelop\MediaBundle\Domain\Command\MoveMedia;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;

final class MoveMediaHandler extends AbstractHandler
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private ValidatorUtils $validatorUtils,
        private FilesystemOperator $filesystem
    ) {
        parent::__construct([MoveMedia::class]);
    }

    protected function handleCommand(MoveMedia $command): void
    {
        $this->validatorUtils->validateOrThrow($command);

        $image = $command->getMedia();
        if ($image->getContext() != $command->getContext()) {
            $oldKey = $image->getKey();
            $image->setContext($command->getContext());
            $newKey = $image->getKey();

            $this->validatorUtils->validateOrThrow($image, null, ['Default']);

            $em = $this->doctrine->getManager();
            $em->persist($image);
            $em->flush();

            try {
                if (false === $this->filesystem->fileExists($newKey)) {
                    $this->filesystem->move($oldKey, $newKey);
                }

                $command->setReturnValue($image);
            } catch (FilesystemException $exception) {
                $command->setReturnValue(null);
            }
        }
    }
}
