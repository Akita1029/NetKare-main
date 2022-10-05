<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Classroom;
use App\Entity\OrderLine;
use App\Entity\ProductOption;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $product = $options['product'];

        $builder
            ->add('productOptions', EntityType::class, [
                'label' => 'order_line.labels.product_options',
                'class' => ProductOption::class,
                'query_builder' => function (EntityRepository $entityRepository) use ($product) {
                    return $entityRepository->createQueryBuilder('po')
                        ->where('po.product = :product')
                        ->setParameter('product', $product)
                        ->orderBy('po.title');
                },
                'expanded' => true,
                'multiple' => $product->getCanSelectMultipleOption(),
                'choice_label' => function (ProductOption $productOption): string {
                    $label = $productOption->getTitle();

                    if ($productOption->getPrice() > 0) {
                        $label = $label .  ' ' . $productOption->getPrice();
                    }

                    return $label;
                }
            ])
            ->add('classrooms', EntityType::class, [
                'label' => 'order_line.labels.classrooms',
                'class' => Classroom::class,
                'expanded' => false,
                'multiple' => true,
                'choices' => $options['school'] ? $options['school']->getClassrooms() : []
            ])
            ->add('description', TextareaType::class, [
                'label' => 'order_line.labels.description',
                'required' => false
            ])
            ->add('album', EntityType::class, [
                'label' => 'order_line.labels.album',
                'class' => Album::class,
                'choices' => $options['school'] ? $options['school']->getAlbums() : []
            ]);

        if ($product->getCanFillLaboratoryReference()) {
            $builder
                ->add('laboratoryReferance', NumberType::class, [
                    'label' => 'order_line.labels.laboratory_referance',
                    'required' => false
                ]);
        }

        if ($product->getCanSelectTemplate()) {
            $builder
                ->add('productTemplate', NumberType::class, [
                    'label' => 'order_line.labels.product_template',
                    'required' => false
                ])
                ->add('productTemplateFile', FileType::class, [
                    'label' => 'order_line.labels.product_template_file',
                    'required' => false,
                    'mapped' => false
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderLine::class,
            'school' => null,
            'product' => null
        ]);
    }
}
