<?php

namespace App\Tests\unit\Security;

use App\Entity\User;
use App\Security\UserChecker;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCheckerTest extends Unit
{
    /** @var UserChecker */
    private $checker;

    protected function _before(): void
    {
        $this->checker = new UserChecker();
    }

    public function testCheckPreAuthWithOnlyUserInterface(): void
    {
        // Given
        /** @var UserInterface|MockObject $user */
        $user = $this->createMock(UserInterface::class);

        // When
        $result = $this->checker->checkPreAuth($user);

        // Then
        $this->assertNull($result);
    }

    public function testCheckPreAuthWithEnabledUser(): void
    {
        // Given
        $user = new User();
        $user->setEnabled(true);

        // When
        $result = $this->checker->checkPreAuth($user);

        // Then
        $this->assertNull($result);
    }

    public function testCheckPreAuthWithDisabledUser(): void
    {
        // Given
        $user = new User();
        $user->setEnabled(false);

        // Then
        $this->expectException(DisabledException::class);
        $this->expectExceptionMessage('User account is disabled.');

        // When
        $result = $this->checker->checkPreAuth($user);
    }
}
