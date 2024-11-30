<?php

namespace App\Filament\Resources\TestRequestResource\Pages;

use App\Filament\Resources\TestRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTestRequest extends CreateRecord
{
    protected static string $resource = TestRequestResource::class;
    protected static ?string $title = 'Create New Test Request';
}
