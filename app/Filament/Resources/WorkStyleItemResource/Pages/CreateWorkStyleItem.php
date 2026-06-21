<?php

namespace App\Filament\Resources\WorkStyleItemResource\Pages;

use App\Filament\Resources\WorkStyleItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkStyleItem extends CreateRecord
{
    protected static string $resource = WorkStyleItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_id'] = auth()->id();

        return $data;
    }
}
