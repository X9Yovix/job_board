<?php

namespace App\Form;

use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CandidatForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    { $builder->add('id', IntegerType::class)
        ->add('firstName', TextType::class)
        ->add('lastName', TextType::class)
        ->add('email', EmailType::class)
        ->add('birthday', BirthdayType::class)
        ->add('country', ChoiceType::class)
        ->add('state', ChoiceType::class)
        ->add('city', ChoiceType::class)
        ->add('phoneNumber', TelType::class)
        ->add('gender', ChoiceType::class, [
            'choices' => [
                'Male' => 'male',
                'Female' => 'female',
            ]]);
    }

    public function getName(){
        return "User";
    }
}