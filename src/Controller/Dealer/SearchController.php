<?php

namespace App\Controller\Dealer;

use App\Entity\School;
use App\Repository\StudentRepository;
use App\Service\Search;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private function getFormSwitchSchool(School $school = null): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dealer_switch_school'))
            ->setData([
                'school' => $school
            ])
            ->add('school', EntityType::class, [
                'attr' => [
                    'placeholder' => 'toolbar.switch_school.placeholders.school'
                ],
                'required' => false,
                'class' => School::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.owner = :owner')
                        ->setParameter('owner', $this->getUser())
                        ->orderBy('s.name');
                }
            ])
            ->getForm();
    }

    private function getFormSearch(): FormInterface
    {
        return $this->createFormBuilder()
            ->setMethod('GET')
            ->setAction($this->generateUrl('app_dealer_search'))
            ->add('search', TextType::class, [
                'attr' => [
                    'placeholder' => 'toolbar.search.placeholders.search'
                ],
                'required' => false
            ])
            ->getForm();
    }

    #[Route('/dealer/search', name: 'app_dealer_search', methods: 'GET')]
    public function index(Request $request, Search $search, StudentRepository $studentRepository): Response
    {
        $form = $this->getFormSearch();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!$data['search']) {
                return $this->redirect($request->headers->get('referer'));
            }

            $students = $studentRepository->findAll();
        }

        return $this->render('dealer/search/index.html.twig', [
            'students' => $students
        ]);
    }

    #[Route('/dealer/switch-school', name: 'dealer_switch_school', methods: 'POST')]
    public function swithSchool(Request $request, Search $search): Response
    {
        $form = $this->getFormSwitchSchool();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['school']) {
                $search->setSchool($data['school']);
            }

            return $this->redirect($request->headers->get('referer'));
        }
    }

    public function toolbar(Search $search): Response
    {
        $school = $search->getSchool();

        $form1 = $this->getFormSwitchSchool($school);
        $form2 = $this->getFormSearch();

        return $this->render('dealer/search/toolbar.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView()
        ]);
    }
}
