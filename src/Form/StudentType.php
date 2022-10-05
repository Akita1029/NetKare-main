<?php

namespace App\Form;

use App\Entity\Classroom;
use App\Entity\School;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['has_school']) {
            $builder
                ->add('school', EntityType::class, [
                    'label' => 'student.labels.school',
                    'class' => School::class,
                    'mapped' => false
                ]);
        }

        $builder
            ->add('classroom', EntityType::class, [
                'label' => 'student.labels.classroom',
                'class' => Classroom::class,
                'choices' => $options['school'] ? $options['school']->getClassrooms() : []
            ])
            ->add('schoolNumber', null, [
                'label' => 'student.labels.schoolNumber',
            ])
            ->add('name', null, [
                'label' => 'student.labels.name',
            ])
            ->add('surname', null, [
                'label' => 'student.labels.surname',
            ])
            ->add('gender', null, [
                'label' => 'student.labels.gender',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
            'school' => null,
            'has_school' => true
        ]);
    }
}
