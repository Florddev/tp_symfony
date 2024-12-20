<?php
namespace App\Form;

use App\Entity\Place;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du lieu est obligatoire']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse est obligatoire']),
                ]
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La ville est obligatoire']),
                ]
            ])
            ->add('postal_code', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le code postal est obligatoire']),
                    new Length([
                        'min' => 5,
                        'max' => 10,
                        'minMessage' => 'Le code postal doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Le code postal ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('capacity', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La capacité est obligatoire']),
                    new Positive(['message' => 'La capacité doit être un nombre positif'])
                ]
            ])
            ->add('created_at', null, [
                'widget' => 'single_text',
                'data' => new \DateTimeImmutable(),
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text',
                'data' => new \DateTimeImmutable(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}