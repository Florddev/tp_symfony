<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('begin_at', null, [
                'widget' => 'single_text',
            ])
            ->add('end_at', null, [
                'widget' => 'single_text',
            ])
            ->add('place_id', null, [
                'required' => false
            ])
            ->add('created_at', null, [
                'widget' => 'single_text',
                'required' => false,
                'data' => new \DateTimeImmutable()
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text',
                'required' => false,
                'data' => new \DateTimeImmutable()
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
