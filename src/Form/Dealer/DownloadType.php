<?php

namespace App\Form\Dealer;

use App\Entity\Download;
use App\Entity\School;
use App\Form\DownloadTypeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', DownloadTypeType::class, [
                'label' => 'download.labels.type',
                'choices' => $options['types']
            ])
            ->add('school', EntityType::class, [
                'label' => 'download.labels.school',
                'class' => School::class,
                'choices' => $options['schools']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Download::class,
            'types' => [],
            'schools' => []
        ]);
    }
}
