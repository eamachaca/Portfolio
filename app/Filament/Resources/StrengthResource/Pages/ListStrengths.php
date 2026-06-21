<?php

namespace App\Filament\Resources\StrengthResource\Pages;

use App\Filament\Resources\StrengthResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStrengths extends ListRecords
{
    protected static string $resource = StrengthResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
