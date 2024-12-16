<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum TestFollowUp: string implements HasLabel
{
    case NEW = "New";
    case PICKUP = "Sample pick up";
    case CENTRAL_HUB = "Sample Received at Central Hub";
    case CODEX_HUB = "Recieved at Codex Hub";
    case DELIVERY_AND_REGISTRATION = "Delivery & registeration at PerkinElmer";
    case REJECTED = "Rejected";
    case APPROVED = "Approved";
    case COMPLETED = "Report Released";

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
