<?php

namespace App\Filament\Resources\StudyResource\Pages;

use App\Filament\Resources\StudyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudy extends CreateRecord
{
    protected static string $resource = StudyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_id'] = auth()->id();

        return $data;
    }
}
