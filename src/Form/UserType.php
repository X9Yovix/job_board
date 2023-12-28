<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
            ])

            ->add('birthday', BirthdayType::class, [
                'label' => 'Birthday',
                'widget' => 'single_text',
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Phone Number',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Gender',
                'choices' => ['male' => 'Male', 'female' => 'Female'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, // Set to null to avoid errors when working with arrays
            'countries' => [], // Pass the countries as an option
            'roles' => [], // Pass the roles as an option
        ]);
    }
}
