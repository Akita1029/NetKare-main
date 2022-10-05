<?php

namespace App\Form;

use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['has_school']) {
            $builder
                ->add('school', null, [
                    'label' => 'album.labels.school'
                ]);
        }

        $builder
            ->add('name', null, [
                'label' => 'album.labels.name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
            'has_school' => true
        ]);
    }
}
