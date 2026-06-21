<?php

namespace App\Filament\Resources\WorkStyleItemResource\Pages;

use App\Filament\Resources\WorkStyleItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkStyleItem extends EditRecord
{
    protected static string $resource = WorkStyleItemResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
