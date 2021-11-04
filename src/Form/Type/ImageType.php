<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Form\Type;

use JmvDevelop\MediaBundle\Entity\Media;
use JmvDevelop\MediaBundle\Graphql\ImageTypeHelper;
use JmvDevelop\MediaBundle\Repository\MediaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

final class ImageType extends AbstractType
{
    public function __construct(
        private MediaRepository $repo,
        private ImageTypeHelper $helper
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer(new CallbackTransformer(
            function (Media|null $value): string {
                if ($value instanceof Media) {
                    return (string) $value->getId();
                }

                return '';
            },
            function (string|int|null $value): ?Media {
                if (null === $value || '' === $value) {
                    return null;
                }
                $id = match (true) {
                    \is_string($value) => (int) $value,
                    \is_int($value) => $value,
                };

                return $this->repo->find($id);
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $image = $form->getData();
        Assert::nullOrIsInstanceOf($image, Media::class);
        $imageId = $image?->getId();

        /** @psalm-suppress MixedArrayAssignment */
        $view->vars['default_image_id'] = null === $imageId ? null : (string) $imageId;

        /** @psalm-suppress MixedArrayAssignment */
        $view->vars['reference_url'] = null !== $image ? $this->helper->resolveFilteredUrl(root: $image, filter: 'reference') : null;

        /** @psalm-suppress MixedArrayAssignment */
        $view->vars['thumbnail_url'] = null !== $image ? $this->helper->resolveFilteredUrl(root: $image, filter: 'widen100') : null;

        /** @psalm-suppress MixedArrayAssignment */
        $view->vars['class_name'] = $options['class_name'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('class_name', '');
    }

    public function getBlockPrefix()
    {
        return 'image';
    }

    public function getParent()
    {
        return TextType::class;
    }
}
