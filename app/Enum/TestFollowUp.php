<?php

namespace App\Enum;

enum TestFollowUp: string
{
    case NEW = "new";
    case REFUSED = "refused";
    case CANCELLED = "cancelled";
    case COMPLETED = "completed";
}
