<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'product.labels.title'
            ])
            ->add('description', null, [
                'label' => 'product.labels.description'
            ])
            ->add('image', FileType::class, [
                'label' => 'product.labels.image',
                'mapped' => false,
                'required' => $options['image_required'],
                'constraints' => [
                    new Image()
                ]
            ])
            ->add('price', null, [
                'label' => 'product.labels.price'
            ])
            ->add('options', CollectionType::class, [
                'label' => false,
                'entry_type' => ProductOptionType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('category', EntityType::class, [
                'label' => 'product.labels.category',
                'class' => Category::class
            ])
            ->add('canSelectMultipleOption', null, [
                'label' => 'product.labels.canSelectMultipleOption'
            ])
            ->add('canFillLaboratoryReference', null, [
                'label' => 'product.labels.canFillLaboratoryReference'
            ])
            ->add('canSelectTemplate', null, [
                'label' => 'product.labels.canSelectTemplate'
            ])
            ->add('templates', CollectionType::class, [
                'entry_type' => ProductTemplateType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false
            ])
            ->add('knifeTemplate', FileType::class, [
                'label' => 'product.labels.knifeTemplate',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg'
                        ],
                    ])
                ]
            ])
            ->add('fields', CollectionType::class, [
                'entry_type' => ProductFieldType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'image_required' => true
        ]);
    }
}
