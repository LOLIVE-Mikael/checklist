<?php

namespace App\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\TachesRepository;
use App\Entity\Checklists;

class FormServiceAddTask
{
    private $formFactory;
    private $tachesRepository;

    public function __construct(FormFactoryInterface $formFactory, TachesRepository $tachesRepository)
    {
        $this->formFactory = $formFactory;
        $this->tachesRepository = $tachesRepository;
    }

    public function createAjoutTacheForm(Checklists $checklist = null)
    {
        if ($checklist) {
            $choices = $this->tachesRepository->findTasksNotInChecklist($checklist);
            $idChecklist = $checklist->getId();
        } else {
            $choices = $this->tachesRepository->findAll();
            $idChecklist = null;
        }

        return $this->formFactory->createBuilder()
            ->add('task', ChoiceType::class, [
                'choices' => $choices,
                'choice_value' => 'id',
                'choice_label' => 'titre',
                'placeholder' => 'Choisir une tâche',
                'required' => false
            ])
            ->add('newTask', TextType::class, [
                'required' => false,
            ])
            ->add('checklist_id', HiddenType::class, [
                'data' => $idChecklist,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
            ])
            ->getForm();
    }
}
