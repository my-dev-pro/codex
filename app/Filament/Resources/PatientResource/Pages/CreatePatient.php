<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;
    protected static ?string $title = 'Create New Patient';

    // filling created_by using current user ID
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!isset($data['created_by'])) {
            $data['created_by'] = auth()->id();
        }
        return $data;
    }
}
