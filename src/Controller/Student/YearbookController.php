<?php

namespace App\Controller\Student;

use App\Entity\Student;
use App\Entity\Yearbook;
use App\Repository\YearbookRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class YearbookController extends AbstractController
{
    private YearbookRepository $yearbookRepository;

    private Student $user;
    private ?Yearbook $yearbook;

    public function __construct(YearbookRepository $yearbookRepository)
    {
        $this->yearbookRepository = $yearbookRepository;
    }

    protected function getYearbookRepository(): YearbookRepository
    {
        return $this->yearbookRepository;
    }

    protected function getUser(): UserInterface
    {
        if (!isset($this->user)) {
            $this->user = parent::getUser();
        }

        return $this->user;
    }

    protected function getYearbook(): ?Yearbook
    {
        if (!isset($this->yearbook)) {
            $this->yearbook = $this->yearbookRepository->findOneByClassroom($this->getUser()->getClassroom());
        }

        return $this->yearbook;
    }

    protected function checkUserYearbook(): void
    {
        $yearbook = $this->getYearbook();

        if (!$yearbook) {
            throw $this->createNotFoundException('Kayıtlı olduğunuz yıllık bulunamamıştır.');
        }

        if ($yearbook->getMemoir() === Yearbook::MEMOIR_NOBODY) {
            throw $this->createAccessDeniedException('Kayıtlı olduğunuz yıllık için anı yazısı özelliği aktif değildir.');
        }

        if ($yearbook->getEndsAt() < new DateTimeImmutable()) {
            throw $this->createAccessDeniedException('Kayıtlı olduğunuz yıllığın son anı yazısı yazma süresi geçmiştir.');
        }
    }
}
