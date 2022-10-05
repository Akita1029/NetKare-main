<?php

namespace App\Form;

use App\Entity\Dealer;
use App\Entity\School;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class SchoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'school.labels.name'
            ])
            ->add('logo', FileType::class, [
                'label' => 'school.labels.logo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image()
                ]
            ])
            ->add('email', null, [
                'label' => 'school.labels.email',
                'required' => false
            ])
            ->add('address', null, [
                'label' => 'school.labels.address',
                'required' => false
            ])
            ->add('phone', TelType::class, [
                'label' => 'school.labels.phone',
                'required' => false
            ])
            ->add('phoneGsm', TelType::class, [
                'label' => 'school.labels.phoneGsm',
                'required' => false
            ])
            ->add('fax', TelType::class, [
                'label' => 'school.labels.fax',
                'required' => false
            ])
            ->add('note', null, [
                'label' => 'school.labels.note',
                'required' => false
            ]);

        if ($options['field_owner']) {
            $builder->add('owner', EntityType::class, [
                'label' => 'school.labels.owner',
                'class' => Dealer::class,
                'choice_label' => 'companyName',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => School::class,
            'field_owner' => false
        ]);
    }
}
