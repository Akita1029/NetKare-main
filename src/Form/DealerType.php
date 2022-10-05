<?php

namespace App\Form;

use App\Entity\Dealer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DealerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'dealer.labels.email'
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'dealer.labels.plainPassword',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('companyName', TextType::class, [
                'label' => 'dealer.labels.companyName'
            ])
            ->add('authorizedPersonName', TextType::class, [
                'label' => 'dealer.labels.authorizedPersonName'
            ])
            ->add('address', null, [
                'label' => 'dealer.labels.address',
            ])
            ->add('phone', TelType::class, [
                'label' => 'dealer.labels.phone',
            ])
            ->add('phoneGsm', TelType::class, [
                'label' => 'dealer.labels.phoneGsm',
            ])
            ->add('fax', TelType::class, [
                'label' => 'dealer.labels.fax',
            ]);

        if ($options['password_required'] === false) {
            $builder
                ->add('plainPassword', PasswordType::class, [
                    'label' => 'dealer.labels.plainPassword',
                    'mapped' => false,
                    'attr' => [
                        'autocomplete' => 'new-password'
                    ],
                    'required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dealer::class,
            'password_required' => false
        ]);
    }
}
