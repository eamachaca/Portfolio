<?php

namespace App\Filament\Resources\StrengthResource\Pages;

use App\Filament\Resources\StrengthResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStrength extends EditRecord
{
    protected static string $resource = StrengthResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
