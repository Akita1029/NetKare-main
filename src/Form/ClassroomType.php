<?php

namespace App\Form;

use App\Entity\Classroom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ClassroomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['has_school']) {
            $builder
                ->add('school', null, [
                    'label' => 'classroom.labels.school'
                ]);
        }

        $builder
            ->add('name', null, [
                'label' => 'classroom.labels.name'
            ])
            ->add('image', FileType::class, [
                'label' => 'Toplu Fotoğraf',
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
            'data_class' => Classroom::class,
            'has_school' => true
        ]);
    }
}
