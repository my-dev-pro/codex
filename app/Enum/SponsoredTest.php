<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum SponsoredTest: string implements HasLabel
{
    case BRCA  = 'Pfizer BRCA';
    case Lung  = 'Pfizer Lung';
    case FGFR  = 'Helpat FGFR';
    case DDT_Phenotyping = 'Helpat DDT Phenotyping';
    case IGHV  = 'RAY IGHV';
    case Amgen_RAS   = 'Amgen RAS';
    case Amgen_MSI   = 'Amgen MSI';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
