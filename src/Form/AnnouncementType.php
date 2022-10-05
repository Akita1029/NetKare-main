<?php

namespace App\Form;

use App\Entity\Announcement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'announcement.labels.image',
                'mapped' => false,
                'constraints' => [
                    new Image()
                ],
                'required' => $options['image_required']
            ])
            ->add('title', null, [
                'label' => 'announcement.labels.title'
            ])
            ->add('description', null, [
                'label' => 'announcement.labels.description',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announcement::class,
            'image_required' => true
        ]);
    }
}
