<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire',
                'constraints' => [
                    new NotBlank(['message' => 'Le commentaire ne peut pas être vide']),
                ],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Écrivez votre commentaire ici...'
                ]
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'title',
                'placeholder' => 'Sélectionnez un événement',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un événement']),
                ],
                'mapped' => true,
            ])
            ->add('event_id', HiddenType::class, [
                'mapped' => false
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $comment = $event->getData();
            if ($comment->getEvent()) {
                $comment->setEventId($comment->getEvent()->getId());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}