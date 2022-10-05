<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Classroom;
use App\Entity\Dealer;
use App\Entity\School;
use App\Entity\Yearbook;
use App\Repository\DealerRepository;
use App\Repository\SchoolRepository;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class YearbookType extends AbstractType
{
    private $translator;
    private $dealerRepository;
    private $schoolRepository;

    public function  __construct(TranslatorInterface $translator, DealerRepository $dealerRepository, SchoolRepository $schoolRepository)
    {
        $this->translator = $translator;
        $this->dealerRepository = $dealerRepository;
        $this->schoolRepository = $schoolRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dealer', EntityType::class, [
                'label' => 'Bayi',
                'class' => Dealer::class,
                'placeholder' => 'Lütfen seçiniz',
                'mapped' => false,
                'data' => $options['dealer']
            ])
            ->add('school', EntityType::class, [
                'label' => 'Okul',
                'class' => School::class,
                'placeholder' => 'Lütfen seçiniz'
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            )
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'onPreSubmit']
            );
    }

    public function modify(FormInterface $form, Dealer $dealer = null, School $school = null, DateTimeImmutable $endsAt = null, string $memoir = null): void
    {
        $schools = [];

        if ($dealer) {
            $schools = $dealer->getSchools();
        }

        $classrooms = [];
        $albums = [];

        if ($school) {
            $classrooms = $school->getClassrooms();
            $albums = $school->getAlbums();
        }

        $form
            ->add('school', EntityType::class, [
                'label' => 'yearbook.labels.school',
                'class' => School::class,
                'placeholder' => 'Lütfen seçiniz',
                'choices' => $schools,
                'disabled' => $schools === []
            ])
            ->add('classrooms', EntityType::class, [
                'label' => 'yearbook.labels.classrooms',
                'class' => Classroom::class,
                'multiple' => true,
                'choices' => $classrooms,
                'disabled' => $classrooms === []
            ])
            ->add('startsAt', DateType::class, [
                'label' => 'yearbook.labels.startsAt',
                'widget' => 'single_text',
                'input' => 'datetime_immutable'
            ])
            ->add('endsAt', DateType::class, [
                'label' => 'yearbook.labels.endsAt',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'mapped' => false,
                'data' => $endsAt
            ])
            ->add('memoir', ChoiceType::class, [
                'label' => 'yearbook.labels.memoir',
                'choices' => [
                    $this->translator->trans('yearbook.memoir.statuses.' . Yearbook::MEMOIR_NOBODY) => Yearbook::MEMOIR_NOBODY,
                    $this->translator->trans('yearbook.memoir.statuses.' . Yearbook::MEMOIR_CLASSROOM) => Yearbook::MEMOIR_CLASSROOM,
                    $this->translator->trans('yearbook.memoir.statuses.' . Yearbook::MEMOIR_EVERYBODY) => Yearbook::MEMOIR_EVERYBODY
                ],
                'mapped' => false,
                'data' => $memoir
            ])
            ->add('imageUpload', null, [
                'label' => 'yearbook.labels.imageUpload'
            ])
            ->add('imageUploadLimit', null, [
                'label' => 'yearbook.labels.imageUploadLimit'
            ])
            ->add('youtube', null, [
                'label' => 'yearbook.labels.youtube'
            ])
            ->add('qrPrefix', null, [
                'label' => 'yearbook.labels.qrPrefix'
            ])
            ->add('profileAlbum', EntityType::class, [
                'label' => 'yearbook.labels.profileAlbum',
                'class' => Album::class,
                'choices' => $albums,
                'disabled' => $albums === []
            ])
            ->add('galleryAlbums', EntityType::class, [
                'label' => 'yearbook.labels.galleryAlbums',
                'class' => Album::class,
                'choices' => $albums,
                'disabled' => $albums === [],
                'multiple' => true
            ]);
    }

    public function onPreSetData(FormEvent $formEvent): void
    {
        $form = $formEvent->getForm();
        $data = $formEvent->getData();

        $dealer = $form->get('dealer')->getData();
        $school = $form->get('school')->getData();

        if (!$school) {
            $school = $data->getSchool();
        }

        $this->modify($form, $dealer, $school, $data->getEndsAt(), $data->getMemoir());
    }

    public function onPreSubmit(FormEvent $formEvent): void
    {
        $form = $formEvent->getForm();
        $data = $formEvent->getData();

        $dealer = null;

        if (isset($data['dealer'])) {
            $dealer = $this->dealerRepository->find($data['dealer']);
        }

        $school = null;

        if (isset($data['school'])) {
            $school = $this->schoolRepository->find($data['school']);
        }

        $this->modify($form, $dealer, $school);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Yearbook::class,
            'dealer' => null
        ]);
    }
}
