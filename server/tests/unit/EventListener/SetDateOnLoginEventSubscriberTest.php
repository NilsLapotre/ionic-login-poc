<?php

namespace App\Tests\unit\EventListener;

use App\Entity\User;
use App\Event\UserLoggedEvent;
use App\EventListener\SetDateOnLoginEventSubscriber;
use function array_key_exists;
use Codeception\Test\Unit;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SetDateOnLoginEventSubscriberTest extends Unit
{
    /** @var SetDateOnLoginEventSubscriber */
    private $subscriber;

    /** @var EntityManagerInterface|MockObject */
    private $entityManager;

    protected function _before(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->subscriber = new SetDateOnLoginEventSubscriber($this->entityManager);
    }

    public function testGetSubscribedEvents(): void
    {
        // When
        $subscribedEvents = $this->subscriber->getSubscribedEvents();

        // Then
        $this->assertTrue(array_key_exists(SecurityEvents::INTERACTIVE_LOGIN, $subscribedEvents));
        $this->assertTrue(array_key_exists(UserLoggedEvent::NAME, $subscribedEvents));
    }

    public function testOnUserLogged(): void
    {
        // Given
        $user = new User();
        $event = new UserLoggedEvent($user);

        // Then
        $this->entityManager->expects($this->once())->method('persist')->with($user);
        $this->entityManager->expects($this->once())->method('flush');

        // When
        $this->subscriber->onUserLogged($event);

        // Then
        $this->assertInstanceOf(DateTimeInterface::class, $user->getLastLoginAt());
    }

    public function testOnSecurityInteractiveLoginWithOnlyUserInterface(): void
    {
        $user = $this->createMock(UserInterface::class);
        $token = $this->createConfiguredMock(TokenInterface::class, ['getUser' => $user]);
        $request = new Request();
        $event = new InteractiveLoginEvent($request, $token);

        // Then
        $this->entityManager->expects($this->never())->method('persist')->with($user);
        $this->entityManager->expects($this->never())->method('flush');

        // When
        $this->subscriber->onSecurityInteractiveLogin($event);
    }

    public function testOnSecurityInteractiveLoginWithUser(): void
    {
        $user = new User();
        $token = $this->createConfiguredMock(TokenInterface::class, ['getUser' => $user]);
        $request = new Request();
        $event = new InteractiveLoginEvent($request, $token);

        // Then
        $this->entityManager->expects($this->once())->method('persist')->with($user);
        $this->entityManager->expects($this->once())->method('flush');

        // When
        $this->subscriber->onSecurityInteractiveLogin($event);

        // Then
        $this->assertInstanceOf(DateTimeInterface::class, $user->getLastLoginAt());
    }
}
