<?php

namespace App\Form\Admin;

use App\Entity\Dealer;
use App\Entity\Download;
use App\Entity\School;
use App\Form\DownloadTypeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', DownloadTypeType::class, [
                'label' => 'download.labels.type'
            ])
            ->add('dealer', EntityType::class, [
                'mapped' => false,
                'required' => false,
                'class' => Dealer::class
            ]);

        $formModifier = function (FormInterface $form, ?Dealer $dealer) {
            $schools = [];

            if ($dealer) {
                $schools = $dealer->getSchools();
                dump($schools->toArray());
            }

            $form->add('school', EntityType::class, [
                'label' => 'download.labels.school',
                'class' => School::class,
                'choices' => $schools
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $formEvent) use ($formModifier) {
                $form = $formEvent->getForm();

                $formModifier($form, $form->get('dealer')->getData());
            }
        );

        $builder->get('dealer')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $formEvent) use ($formModifier) {
            $dealer = $formEvent->getForm()->getData();

            $formModifier($formEvent->getForm()->getParent(), $dealer);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Download::class,
            'schools' => []
        ]);
    }
}
