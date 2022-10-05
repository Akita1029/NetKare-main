<?php

namespace App\Form;

use App\Entity\Classroom;
use App\Entity\Operator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $formEvent) {
                $form = $formEvent->getForm();
                $operator = $formEvent->getData();

                $form->add('classrooms', EntityType::class, [
                    'class' => Classroom::class,
                    'choices' => $operator->getYearbook()->getClassrooms(),
                    'multiple' => true,
                    'label' => 'operator.labels.classrooms'
                ]);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Operator::class
        ]);
    }
}
