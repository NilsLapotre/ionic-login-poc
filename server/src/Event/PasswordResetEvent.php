<?php

namespace App\Event;

class PasswordResetEvent extends UserEvent
{
    public const NAME = 'password.reset';
}
