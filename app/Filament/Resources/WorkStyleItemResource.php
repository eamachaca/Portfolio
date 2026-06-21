<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkStyleItemResource\Pages;
use App\Filament\Support\LocaleTabs;
use App\Models\WorkStyleItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkStyleItemResource extends Resource
{
    protected static ?string $model = WorkStyleItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('Work style');
    }

    public static function getModelLabel(): string
    {
        return __('Work style item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Work style items');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            LocaleTabs::for(fn (string $locale) => [
                Textarea::make("text.{$locale}")
                    ->label(__('Text'))
                    ->rows(2)
                    ->columnSpanFull(),
            ]),
            TextInput::make('sort_order')->label(__('Sort order'))->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('text')->label(__('Text'))->limit(80)->searchable(),
                TextColumn::make('sort_order')->label(__('Sort order'))->sortable()->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkStyleItems::route('/'),
            'create' => Pages\CreateWorkStyleItem::route('/create'),
            'edit' => Pages\EditWorkStyleItem::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->id());
    }
}
