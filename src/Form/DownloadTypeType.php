<?php

namespace App\Form;

use App\Entity\Download;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DownloadTypeType extends AbstractType
{
    private $translator;

    public function  __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'choices' => [
                $this->translateType(Download::TYPE_E_SCHOOL_ALBUM) => Download::TYPE_E_SCHOOL_ALBUM,
                $this->translateType(Download::TYPE_E_SCHOOL_ALBUM_WITH_STUDENT_NAME_SURNAME) => Download::TYPE_E_SCHOOL_ALBUM_WITH_STUDENT_NAME_SURNAME,
                $this->translateType(Download::TYPE_ALL_ALBUMS) => Download::TYPE_ALL_ALBUMS,
                $this->translateType(Download::TYPE_BIOMETRIC_2) => Download::TYPE_BIOMETRIC_2,
                $this->translateType(Download::TYPE_BIOMETRIC_4) => Download::TYPE_BIOMETRIC_4,
                $this->translateType(Download::TYPE_HEADSHOT_2) => Download::TYPE_HEADSHOT_2,
                $this->translateType(Download::TYPE_HEADSHOT_4) => Download::TYPE_HEADSHOT_4,
                $this->translateType(Download::TYPE_HEADSHOT_8) => Download::TYPE_HEADSHOT_8,
                $this->translateType(Download::TYPE_EXECUTIVE) => Download::TYPE_EXECUTIVE,
                $this->translateType(Download::TYPE_EXCEL) => Download::TYPE_EXCEL,
                $this->translateType(Download::TYPE_TRANSCRIPT) => Download::TYPE_TRANSCRIPT,
                $this->translateType(Download::TYPE_YEARBOOK) => Download::TYPE_YEARBOOK
            ]
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    private function translateType(string $type): string
    {
        return $this->translator->trans('download.types.' . $type);
    }
}
