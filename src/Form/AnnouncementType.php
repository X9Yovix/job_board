<?php

namespace App\Form;

use DateTimeImmutable;
use App\Entity\Company;
use App\Entity\Keyword;
use App\Entity\Announcement;
use App\Form\KeywordFormEventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class AnnouncementType extends AbstractType
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly Security $security)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a Company',
                'query_builder' => function () use ($user) {
                    return $this->entityManager->getRepository(Company::class)->createQueryBuilder('c')
                        ->innerJoin('c.users', 'u')
                        ->where('u = :user')
                        ->setParameter('user', $user);
                },
            ])
            ->add('title')
            ->add('description')
            ->add('requirements')
            ->add('experince')
            ->add('salary')
            ->add('deadline', DateTimeType::class, [
                //'widget' => 'single_text',
                //'html5' => true,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => new DateTimeImmutable('today'), // 'today' means the current date
                        'message' => 'Please select a date greater than or equal to today.',
                    ]),
                ],
                'data' => new DateTimeImmutable(),
            ])
            ->add(
                'keywords',
                EntityType::class,
                [
                    'class' => Keyword::class,
                    'multiple' => true,
                    'choice_label' => 'name',
                ]
            )
            ->add('jobType');

        $builder->addEventSubscriber(new KeywordFormEventSubscriber($this->entityManager));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announcement::class,
        ]);
    }
}
