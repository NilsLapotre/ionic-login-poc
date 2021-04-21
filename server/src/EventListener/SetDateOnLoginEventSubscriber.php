<?php

namespace App\EventListener;

use App\Entity\User;
use App\Event\UserLoggedEvent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SetDateOnLoginEventSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            UserLoggedEvent::NAME => 'onUserLogged',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            $this->setLastLoginAt($user);
        }
    }

    public function onUserLogged(UserLoggedEvent $event): void
    {
        $user = $event->getUser();

        $this->setLastLoginAt($user);
    }

    protected function setLastLoginAt(User $user): void
    {
        $user->setLastLoginAt(new DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
