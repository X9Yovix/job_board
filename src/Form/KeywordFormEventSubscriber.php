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

        //$form = $event->getForm();
        //dump($data);
        //dump($form);
        /* $form = $event->getForm(); */
        /* dump($data);
        dump($data['keywords']); */
        if (isset($data['keywords'])) {
            $keywords = $data['keywords'];
            $existingKeywords = [];
            $newKeywords = [];

            foreach ($keywords as $value) {
                if (is_numeric($value)) {
                    //$keyword = $this->entityManager->getRepository(Keyword::class)->findOneBy(['id' => $value]);
                    $existingKeywords[] = $value;
                } else {
                    $newKeyword = new Keyword();
                    $newKeyword->setName(ucwords($value));
                    $this->entityManager->persist($newKeyword);
                    $this->entityManager->flush();

                    $existingKeywords[] = strval($newKeyword->getId());
                    //$newKeywords[] = $newKeyword;
                }
            }


            /* dump($existingKeywords);
            dump($newKeywords);
            $test = array_merge($existingKeywords, $newKeywords);
            dump($test);
            die; */
            //$data['keywords'] = array_merge($existingKeywords, $newKeywords);
            // Fetch existing keywords from the Announcement entity
            //$existingKeywords = $event->getForm()->getData()->getKeywords();

            //$data['keywords'] = array_merge($existingKeywords, $newKeywords);
            $data['keywords'] = $existingKeywords;
            $event->setData($data);
        }
    }
}
