<?php

use App\Entity\Keyword;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KeywordFormEventSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (isset($data['keywords'])) {
            $keywords = $data['keywords'];
            $existingKeywords = [];

            foreach ($keywords as $value) {
                if (is_numeric($value)) {
                    $existingKeywords[] = $value;
                    //$existingKeywords[] = $this->entityManager->getReference(Keyword::class, $value);
                } else {
                    $newKeyword = new Keyword();
                    $newKeyword->setName(ucwords($value));
                    $this->entityManager->persist($newKeyword);
                    $this->entityManager->flush();

                    $existingKeywords[] = strval($newKeyword->getId());
                }
            }

            $data['keywords'] = $existingKeywords;
            $event->setData($data);
        }
    }
}
