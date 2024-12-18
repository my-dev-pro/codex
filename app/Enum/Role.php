<?php

namespace App\Enum;

enum Role : string
{
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case DOCTOR = 'doctor';
    case GENETICIST = 'geneticist';
    case SUPER_MODERATOR = 'superModerator';
}
