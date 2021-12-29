<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Controller;

use JmvDevelop\Domain\Exception\DomainException;
use JmvDevelop\Domain\HandlerInterface;
use JmvDevelop\MediaBundle\Domain\Command\CreateMedia;
use JmvDevelop\MediaBundle\Entity\Media;
use JmvDevelop\MediaBundle\Graphql\ImageTypeHelper;
use JmvDevelop\MediaBundle\UrlGenerator\MediaUrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

final class MediaController extends AbstractController
{
    #[Route(path: '/media/upload', name: 'media_upload', methods: ['POST'])]
    public function uploadMediaAction(
        Request $request,
        HandlerInterface $handler,
        MediaUrlGeneratorInterface $mediaUrlGenerator,
    ): Response {
        $files = $request->files->all();

        if (0 === \count($files)) {
            return $this->jsonError('Invalid request', 400);
        }

        /** @var File $file */
        $file = $files[\array_keys($files)[0]];

        $type = (string) $request->request->get('type', 'image');
        if (Media::TYPE_IMAGE !== $type && Media::TYPE_VIDEO !== $type) {
            return $this->jsonError('Invalid request', 400);
        }

        $context = (string) $request->request->get('context', Media::CONTEXT_TMP);
        if (Media::CONTEXT_TMP !== $context && Media::CONTEXT_MEDIA !== $context) {
            return $this->jsonError('Invalid request', 400);
        }

        $namer = (string) $request->request->get('namer', '');
        $namer = '' === $namer ? null : $namer;
        $name = (string) $request->request->get('name', '');
        $name = '' === $name ? null : $name;

        $command = new CreateMedia(
            type: $type,
            file: $file,
            context: $context,
            namer: $namer,
            name: $name,
        );

        try {
            $handler->handle($command);
            $media = $command->getReturnValue();
            Assert::notNull($media);

            $url = $mediaUrlGenerator->generateUrl($media->getKey());

            return $this->json([
                'status' => 'success',
                'data' => ['id' => $media->getId(), 'url' => $url],
            ]);
        } catch (FileException|DomainException $e) {
            return $this->jsonError($e->getMessage(), 500);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 500);
        }
    }

    #[Route(path: '/media/ckeditor/upload', name: 'media_upload.ckeditor', methods: ['POST'])]
    public function ckEditorUploadMediaAction(
        Request $request,
        HandlerInterface $handler,
        ImageTypeHelper $imageTypeHelper,
    ): Response {
        $files = $request->files->all();

        if (0 === \count($files)) {
            return $this->jsonError('Invalid request', 400);
        }

        /** @var File $file */
        $file = $files[\array_keys($files)[0]];

        $type = (string) $request->request->get('type', 'image');
        if (Media::TYPE_IMAGE !== $type) {
            return $this->jsonError('Invalid request', 400);
        }

        $command = new CreateMedia(
            type: $type,
            file: $file,
            context: 'ckeditor',
            namer: null,
            name: null,
        );

        try {
            $handler->handle($command);
            $media = $command->getReturnValue();
            Assert::notNull($media);

            return $this->json([
                'urls' => [
                    'default' => $imageTypeHelper->resolveFilteredUrl(root: $media, filter: 'widen1200'),
                    '100' => $imageTypeHelper->resolveFilteredUrl(root: $media, filter: 'widen100'),
                    '500' => $imageTypeHelper->resolveFilteredUrl(root: $media, filter: 'widen500'),
                    '1000' => $imageTypeHelper->resolveFilteredUrl(root: $media, filter: 'widen1000'),
                    '1200' => $imageTypeHelper->resolveFilteredUrl(root: $media, filter: 'widen1200'),
                ],
            ]);
        } catch (FileException|DomainException $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]])->setStatusCode(405);
        } catch (\Throwable $e) {
            return $this->json(['error' => ['message' => $e->getMessage()]])->setStatusCode(500);
        }
    }

    private function jsonError(string $message, int $code = 405): JsonResponse
    {
        return $this->json(['status' => 'error', 'message' => $message])->setStatusCode($code);
    }
}
