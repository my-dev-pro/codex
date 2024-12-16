<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum Test : string implements HasLabel
{
    case CODEX_PRO = 'CodexPro';
    case CODEXI = 'Codexi';
    case CODEX_LIQUID = 'Codex Liquid';
    case EXOM = 'Whole Exom Sequencing';
    case MMR = 'MMR';
    case MSI = 'MSI';
    case PFIZER_CODEXI = 'Pfizer and Codexi';
    case SINGLE_GENE = 'Single Gene';
    case WES = 'WES';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
