<?php

namespace App\Form;

use App\Entity\ProductTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProductTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Başlık'
            ])
            ->add('preview1Title', null, [
                'label' => 'Önizleme 1 Başlık'
            ])
            ->add('preview1Image', FileType::class, [
                'label' => 'Önizleme 1 Görsel',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image()
                ]
            ])
            ->add('preview2Title', null, [
                'label' => 'Önizleme 2 Başlık'
            ])
            ->add('preview2Image', FileType::class, [
                'label' => 'Önizleme 2 Görsel',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductTemplate::class,
        ]);
    }
}
