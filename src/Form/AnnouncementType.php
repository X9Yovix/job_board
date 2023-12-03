<?php

namespace App\Form;

use App\Entity\Keyword;
use App\Entity\Announcement;
use KeywordFormEventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Validator\AddKeywordsInAnnouncement;
use App\Validator\KeywordsAnnouncement;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class AnnouncementType extends AbstractType
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('requirements')
            ->add('location')
            ->add('salary')
            ->add('companyName')
            /* ->add('recruiter') */
            /* ->add('keywords') */
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
