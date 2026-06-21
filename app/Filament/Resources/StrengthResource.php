<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StrengthResource\Pages;
use App\Filament\Support\LocaleTabs;
use App\Models\Strength;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StrengthResource extends Resource
{
    protected static ?string $model = Strength::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Strengths');
    }

    public static function getModelLabel(): string
    {
        return __('Strength');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Strengths');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            LocaleTabs::for(fn (string $locale) => [
                TextInput::make("label.{$locale}")
                    ->label(__('Label'))
                    ->maxLength(255)
                    ->helperText(__('Short tag shown above the title (e.g. "Web Applications").')),
                TextInput::make("title.{$locale}")
                    ->label(__('Title'))
                    ->maxLength(255),
                Textarea::make("body.{$locale}")
                    ->label(__('Body'))
                    ->rows(4)
                    ->columnSpanFull(),
            ]),
            TagsInput::make('tech_stack')
                ->label(__('Tech stack'))
                ->placeholder(__('Add a tech and press Enter'))
                ->columnSpanFull(),
            TextInput::make('sort_order')->label(__('Sort order'))->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label(__('Label'))->searchable()->sortable(),
                TextColumn::make('title')->label(__('Title'))->limit(40)->toggleable(),
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
            'index' => Pages\ListStrengths::route('/'),
            'create' => Pages\CreateStrength::route('/create'),
            'edit' => Pages\EditStrength::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->id());
    }
}
