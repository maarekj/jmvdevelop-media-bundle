<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Namer;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Webmozart\Assert\Assert;

class NamerRegistry
{
    public function __construct(
        private ServiceLocator $locator,
    ) {
    }

    public function getNamerOrNull(string $id): ?NamerInterface
    {
        if (!$this->locator->has($id)) {
            return null;
        }

        $namer = $this->locator->get($id);
        Assert::notNull($namer);
        Assert::isInstanceOf($namer, NamerInterface::class);

        return $namer;
    }

    public function getNamerOrThrow(string $id): NamerInterface
    {
        $namer = $this->getNamerOrNull($id);
        Assert::notNull($namer);

        return $namer;
    }
}
