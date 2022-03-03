<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr'=> [
                    'class'=> 'form-control'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped'=> false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs ne sont pas similaires.',
                'attr'=> [
                    'class'=> 'form-control'
                ],
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])

            ->add('lastname', TextType::class, [
                'attr'=> [
                    'class'=> 'form-control'
                ]
            ])
            ->add('email', EmailType::class,
            [
                'attr'=> [
                    'class'=> 'form-control'
                ]
            ])

            ->add('sector', ChoiceType::class, [
                'choices'=> [
                    'Recrutement'=> 'RECRUTEMENT',
                    'Informatique'=> 'INFORMATIQUE',
                    'Comptabilité'=> 'COMPTA',
                    'Direction'=> 'DIRECTION'
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => 'Image de profil !',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])


            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
