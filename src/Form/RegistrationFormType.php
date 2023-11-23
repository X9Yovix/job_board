<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       /*  $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                ],
            ])

            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                ],
            ])

            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email address',
                    ]),
                    new Email([
                        'message' => 'The email address "{{ value }}" is not a valid email',
                    ]),
                ],
            ])

            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('birthday', BirthdayType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your birthday',
                    ]),
                    new Type([
                        'type' => '\DateTime',
                        'message' => 'The value {{ value }} is not a valid date',
                    ]),
                ],
            ])

            ->add('country', ChoiceType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select your country',
                    ]),
                ],
            ])

            ->add('state', ChoiceType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select your country',
                    ]),
                ],
            ])

            ->add('city', ChoiceType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select your country',
                    ]),
                ],
            ])

            ->add('phoneNumber', TelType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your phone number',
                    ]),
                ],
            ])

            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Male' => 'male',
                    'Female' => 'female',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select your gender',
                    ]),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ]); */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /* $resolver->setDefaults([
            'data_class' => User::class,
        ]); */
    }
}
