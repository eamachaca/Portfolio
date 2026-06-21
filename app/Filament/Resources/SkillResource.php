<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkillResource\Pages;
use App\Models\Skill;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Skills');
    }

    public static function getModelLabel(): string
    {
        return __('Skill');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Skills');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('Name'))->required()->maxLength(255),
            Select::make('category')
                ->label(__('Category'))
                ->options([
                    'Languages' => __('Languages'),
                    'Frameworks' => __('Frameworks'),
                    'Databases' => __('Databases'),
                    'Tools' => __('Tools'),
                    'Cloud' => __('Cloud'),
                    'Methodology' => __('Methodology'),
                ])
                ->searchable()
                ->allowHtml(false)
                ->native(false)
                ->placeholder(__('Optional — leave empty for ungrouped.'))
                ->helperText(__('Pills get grouped by category on the front.')),
            TextInput::make('sort_order')->label(__('Sort order'))->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
                TextColumn::make('category')->label(__('Category'))->sortable()->placeholder('—'),
                TextColumn::make('sort_order')->label(__('Sort order'))->sortable()->toggleable(),
            ])
            ->defaultSort('category')
            ->filters([
                SelectFilter::make('category')
                    ->options(fn () => Skill::query()->where('owner_id', auth()->id())->whereNotNull('category')->distinct()->pluck('category', 'category')->all()),
            ])
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
            'index' => Pages\ListSkills::route('/'),
            'create' => Pages\CreateSkill::route('/create'),
            'edit' => Pages\EditSkill::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->id());
    }
}
