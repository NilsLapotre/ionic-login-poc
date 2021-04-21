<?php

namespace App\Event;

class PasswordRequestedEvent extends UserEvent
{
    public const NAME = 'password.requested';
}
