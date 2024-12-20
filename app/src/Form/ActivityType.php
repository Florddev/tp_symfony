<?php
// src/Form/ActivityType.php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Doctrine\ORM\EntityManagerInterface;

class ActivityType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de l\'activité est obligatoire']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'La description doit faire au moins {{ limit }} caractères'
                    ])
                ],
                'attr' => ['rows' => 4]
            ])
            ->add('heure_debut', TimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'L\'heure de début est obligatoire'])
                ]
            ])
            ->add('heure_fin', TimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'L\'heure de fin est obligatoire'])
                ]
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'title',
                'placeholder' => 'Sélectionnez un événement',
                'constraints' => [
                    new NotBlank(['message' => 'L\'événement est obligatoire'])
                ],
                'mapped' => true,
            ])
            ->add('evenement_id', HiddenType::class, [
                'mapped' => false
            ]);

        // Ajouter un event listener pour mettre à jour automatiquement evenement_id
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $activity = $event->getData();
            $form = $event->getForm();

            if ($activity->getEvent()) {
                $activity->setEvenementId($activity->getEvent()->getId());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}