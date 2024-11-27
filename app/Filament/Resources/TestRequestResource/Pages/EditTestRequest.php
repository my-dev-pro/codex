<?php

namespace App\Filament\Resources\TestRequestResource\Pages;

use App\Filament\Resources\TestRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestRequest extends EditRecord
{
    protected static string $resource = TestRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
