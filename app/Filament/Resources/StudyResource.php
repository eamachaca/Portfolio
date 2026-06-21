<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudyResource\Pages;
use App\Filament\Support\LocaleTabs;
use App\Models\Study;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudyResource extends Resource
{
    protected static ?string $model = Study::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('Education');
    }

    public static function getModelLabel(): string
    {
        return __('Study');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Studies');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('institution')
                ->label(__('Institution'))
                ->required()
                ->maxLength(255),
            LocaleTabs::for(fn (string $locale) => [
                TextInput::make("title.{$locale}")
                    ->label(__('Degree / Title'))
                    ->maxLength(255),
                Textarea::make("description.{$locale}")
                    ->label(__('Description'))
                    ->rows(4)
                    ->columnSpanFull(),
            ]),
            TextInput::make('field')
                ->label(__('Field'))
                ->maxLength(255)
                ->helperText(__('e.g. Software Engineering, Mathematics.')),
            DatePicker::make('start_date')
                ->label(__('Start date'))
                ->native(false),
            DatePicker::make('end_date')
                ->label(__('End date'))
                ->native(false)
                ->helperText(__('Leave empty if currently in progress.')),
            Toggle::make('in_progress')->label(__('In progress')),
            FileUpload::make('logo')
                ->label(__('Logo'))
                ->image()
                ->disk('public')
                ->directory('studies/logos')
                ->maxSize(2048),
            TextInput::make('sort_order')
                ->label(__('Sort order'))
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label(__('Logo'))
                    ->disk('public')
                    ->square(),
                TextColumn::make('institution')
                    ->label(__('Institution'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(__('Start date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('End date'))
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                IconColumn::make('in_progress')
                    ->label(__('In progress'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('Sort order'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('start_date', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudies::route('/'),
            'create' => Pages\CreateStudy::route('/create'),
            'edit' => Pages\EditStudy::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->id());
    }
}
