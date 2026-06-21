<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExperienceResource\Pages;
use App\Filament\Support\LocaleTabs;
use App\Models\Experience;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExperienceResource extends Resource
{
    protected static ?string $model = Experience::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'company';

    public static function getNavigationLabel(): string
    {
        return __('Experience');
    }

    public static function getModelLabel(): string
    {
        return __('Experience');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Experiences');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('company')
                ->label(__('Company'))
                ->required()
                ->maxLength(255),
            FileUpload::make('logo')
                ->label(__('Logo'))
                ->image()
                ->disk('public')
                ->directory('experiences/logos')
                ->maxSize(2048),
            LocaleTabs::for(fn (string $locale) => [
                Textarea::make("summary.{$locale}")
                    ->label(__('Summary'))
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText(__('Optional. Single description shared across all levels of this role. Leave empty if each level has its own.')),
            ], key: 'summary_translations'),
            TagsInput::make('tech_stack')
                ->label(__('Tech stack'))
                ->placeholder(__('Add a tech and press Enter'))
                ->helperText(__('Shared stack across all levels at this company.')),
            TextInput::make('sort_order')
                ->label(__('Sort order'))
                ->numeric()
                ->default(0)
                ->helperText(__('Lower numbers show first. Use to push current job to the top.')),
            Repeater::make('levels')
                ->label(__('Levels / promotions'))
                ->helperText(__('One entry per role at this company. Add another when you got promoted.'))
                ->columnSpanFull()
                ->collapsible()
                ->minItems(1)
                ->defaultItems(1)
                ->reorderable()
                ->addActionLabel(__('Add level'))
                ->itemLabel(fn (array $state): ?string => $state['role'] ?? null)
                ->schema([
                    TextInput::make('role')
                        ->label(__('Role'))
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ssr. Backend Engineer'),
                    DatePicker::make('start_date')
                        ->label(__('Start date'))
                        ->native(false),
                    DatePicker::make('end_date')
                        ->label(__('End date'))
                        ->native(false)
                        ->helperText(__('Leave empty if currently in this role.')),
                    Toggle::make('in_progress')
                        ->label(__('Current')),
                    LocaleTabs::for(fn (string $locale) => [
                        Textarea::make("description.{$locale}")
                            ->label(__('Description'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText(__('Optional. Description specific to this level.')),
                    ], key: 'description_translations'),
                    Repeater::make('highlights')
                        ->label(__('Highlights / bullets'))
                        ->columnSpanFull()
                        ->reorderable()
                        ->defaultItems(0)
                        ->addActionLabel(__('Add bullet'))
                        ->schema([
                            LocaleTabs::for(fn (string $locale) => [
                                TextInput::make($locale)
                                    ->label(__('Bullet (:locale)', ['locale' => $locale]))
                                    ->maxLength(500)
                                    ->placeholder(__('What you shipped, improved or owned.')),
                            ], key: 'highlight_translations'),
                        ]),
                ]),
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
                TextColumn::make('company')
                    ->label(__('Company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('levels')
                    ->label(__('Levels'))
                    ->state(fn (Experience $r): string => (string) count($r->levels ?? [])),
                TextColumn::make('sort_order')
                    ->label(__('Sort order'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
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
            'index' => Pages\ListExperiences::route('/'),
            'create' => Pages\CreateExperience::route('/create'),
            'edit' => Pages\EditExperience::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->id());
    }
}
