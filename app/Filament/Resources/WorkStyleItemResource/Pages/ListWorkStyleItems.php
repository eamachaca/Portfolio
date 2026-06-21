<?php

namespace App\Filament\Resources\WorkStyleItemResource\Pages;

use App\Filament\Resources\WorkStyleItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkStyleItems extends ListRecords
{
    protected static string $resource = WorkStyleItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
