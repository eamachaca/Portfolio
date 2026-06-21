<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Support\LocaleTabs;
use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('Projects');
    }

    public static function getModelLabel(): string
    {
        return __('Project');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Projects');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('slug')
                ->label(__('Slug'))
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText(__('Leave empty to auto-generate from the first available title translation.')),
            LocaleTabs::for(fn (string $locale) => [
                TextInput::make("title.{$locale}")
                    ->label(__('Title'))
                    ->maxLength(255),
                TextInput::make("excerpt.{$locale}")
                    ->label(__('Excerpt'))
                    ->maxLength(255)
                    ->helperText(__('Short one-line tagline shown in cards.')),
                Textarea::make("description.{$locale}")
                    ->label(__('Description'))
                    ->rows(8)
                    ->columnSpanFull(),
            ]),
            FileUpload::make('cover_image')
                ->label(__('Cover image'))
                ->image()
                ->disk('public')
                ->directory('projects/covers')
                ->imageEditor()
                ->maxSize(4096),
            FileUpload::make('gallery')
                ->label(__('Gallery'))
                ->image()
                ->multiple()
                ->reorderable()
                ->disk('public')
                ->directory('projects/gallery')
                ->maxSize(4096),
            TagsInput::make('tech_stack')
                ->label(__('Tech stack'))
                ->placeholder(__('Add a tech and press Enter'))
                ->helperText('Laravel, Filament, MySQL, …'),
            Select::make('experience_id')
                ->label(__('Done at (experience)'))
                ->relationship('experience', 'company', fn ($query) => $query->where('owner_id', auth()->id())->orderBy('sort_order'))
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText(__('Link this project to one of your work experiences. Leave empty for a personal project — it shows as #Personal on the front.')),
            Repeater::make('apps')
                ->label(__('Apps / components'))
                ->helperText(__('Use this when one project ships as multiple apps (e.g. web + mobile). Leave empty for single-app projects.'))
                ->columnSpanFull()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                ->defaultItems(0)
                ->addActionLabel(__('Add app'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('platform')
                        ->label(__('Platform'))
                        ->maxLength(255)
                        ->placeholder('Web · iOS · Android · Backend · …'),
                    LocaleTabs::for(fn (string $locale) => [
                        Textarea::make("description.{$locale}")
                            ->label(__('Description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ], key: 'app_description_translations'),
                    TagsInput::make('tech_stack')
                        ->label(__('Tech stack'))
                        ->placeholder(__('Add a tech and press Enter')),
                    KeyValue::make('links')
                        ->label(__('Links'))
                        ->keyLabel(__('Label'))
                        ->valueLabel(__('URL'))
                        ->addActionLabel(__('Add link'))
                        ->reorderable()
                        ->helperText('Live, Code, App Store, Play Store, TestFlight, …')
                        ->columnSpanFull(),
                ]),
            TextInput::make('url')
                ->label(__('Live URL'))
                ->url()
                ->maxLength(255),
            TextInput::make('repo_url')
                ->label(__('Repository URL'))
                ->url()
                ->maxLength(255),
            Toggle::make('featured')
                ->label(__('Featured'))
                ->helperText(__('Featured projects show on the home page (top 3). Marking a new one un-features the oldest automatically.')),
            TextInput::make('sort_order')
                ->label(__('Sort order'))
                ->numeric()
                ->default(0),
            DateTimePicker::make('published_at')
                ->label(__('Published at'))
                ->helperText(__('Leave empty to keep as a draft (hidden from the public site).')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label(__('Cover image'))
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('experience.company')
                    ->label(__('Done at'))
                    ->placeholder(__('Personal'))
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('Sort order'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('published_at')
                    ->label(__('Published at'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder(__('Draft')),
                TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('experience_id')
                    ->label(__('Experience'))
                    ->relationship('experience', 'company', fn ($query) => $query->where('owner_id', auth()->id()))
                    ->preload(),
                TernaryFilter::make('featured')->label(__('Featured')),
                TernaryFilter::make('published_at')
                    ->label(__('Published'))
                    ->nullable()
                    ->placeholder(__('All'))
                    ->trueLabel(__('Published'))
                    ->falseLabel(__('Drafts')),
            ])
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->id());
    }
}
