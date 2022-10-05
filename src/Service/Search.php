<?php

namespace App\Service;

use App\Entity\School;
use App\Repository\SchoolRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class Search
{
    private const SESSION_NAME = 'school';

    private RequestStack $requestStack;
    private SchoolRepository $schoolRepository;

    public function __construct(RequestStack $requestStack, SchoolRepository $schoolRepository)
    {
        $this->requestStack  = $requestStack;
        $this->schoolRepository = $schoolRepository;
    }

    public function getSchool(): ?School
    {
        $session = $this->requestStack->getSession();

        if (!$session->has(self::SESSION_NAME)) {
            return null;
        }

        return $this->schoolRepository->find($session->get(self::SESSION_NAME));
    }

    public function setSchool(School $school): void
    {
        $session = $this->requestStack->getSession();

        $session->set(self::SESSION_NAME, $school->getId());
    }
}
