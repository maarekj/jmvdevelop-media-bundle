<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use JmvDevelop\MediaBundle\Repository\MediaRepository;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[Entity(repositoryClass: MediaRepository::class)]
class Media
{
    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';

    public const CONTEXT_TMP = 'tmp';
    public const CONTEXT_MEDIA = 'media';

    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer', nullable: false)]
    private ?int $id = null;

    /** @var Media::TYPE_* */
    #[Column(type: 'string', length: 10, nullable: false)]
    #[NotBlank]
    #[Choice(choices: ['image', 'video'])]
    private string $type;

    /** @var Media::CONTEXT_* */
    #[Column(type: 'string', length: 10, nullable: false)]
    #[NotNull]
    #[NotBlank]
    #[Choice(choices: ['tmp', 'media'])]
    private string $context;

    #[Column(type: 'string', length: 255, nullable: false)]
    #[NotNull]
    #[NotBlank]
    private string $name;

    #[Column(type: 'integer', nullable: false)]
    #[NotNull]
    private int $width;

    #[Column(type: 'integer', nullable: false)]
    #[NotNull]
    private int $height;

    /**
     * @param Media::TYPE_*    $type
     * @param Media::CONTEXT_* $context
     */
    public function __construct(string $type, Media|string $context, string $name, int $width, int $height)
    {
        $this->type = $type;
        $this->context = $context;
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
    }

    public function getExtension(): ?string
    {
        return \pathinfo($this->getKey(), \PATHINFO_EXTENSION);
    }

    public function getKey(): string
    {
        return \sprintf('%s/%s', $this->getContext(), $this->getName());
    }

    /** @return int|null */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** @return Media::TYPE_* */
    public function getType(): string
    {
        return $this->type;
    }

    /** @return Media::CONTEXT_* */
    public function getContext(): string
    {
        return $this->context;
    }

    /** @param Media::CONTEXT_* $context */
    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
