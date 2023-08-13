<?php

namespace Packeton\Form\Type\Package;

use Doctrine\Persistence\ManagerRegistry;
use Packeton\Form\Handler\ArtifactHandler;
use Packeton\Form\Type\EmbedCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomPackageType extends AbstractType
{
    use ArtifactFormTrait;

    public function __construct(
        protected ManagerRegistry $registry,
        protected ArtifactHandler $handler,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->remove('credentials');
        $builder->remove('pullRequestReview');

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'package-repo-info', 'placeholder' => 'acme/package-name'],
                'disabled' => false === $options['is_created'],
            ])
            ->add('customVersions', EmbedCollectionType::class, [
                'entry_type' => CustomVersionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'dist_choices' => $this->getChoices($options['is_created'])
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return BasePackageType::class;
    }
}
