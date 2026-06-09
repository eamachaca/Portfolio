<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true),
            TextInput::make('slug')
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('Leave empty to auto-generate from title.'),
            TextInput::make('excerpt')
                ->maxLength(255)
                ->helperText('Short one-line tagline shown in cards.'),
            Textarea::make('description')
                ->rows(8)
                ->columnSpanFull(),
            FileUpload::make('cover_image')
                ->image()
                ->disk('public')
                ->directory('projects/covers')
                ->imageEditor()
                ->maxSize(4096),
            FileUpload::make('gallery')
                ->image()
                ->multiple()
                ->reorderable()
                ->disk('public')
                ->directory('projects/gallery')
                ->maxSize(4096),
            TagsInput::make('tech_stack')
                ->placeholder('Add a tech and press Enter')
                ->helperText('Laravel, Filament, MySQL, …'),
            Select::make('experience_id')
                ->label('Done at (experience)')
                ->relationship('experience', 'company', fn ($query) => $query->where('owner_id', auth()->id())->orderBy('sort_order'))
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('Link this project to one of your work experiences. Leave empty for a personal project — it shows as #Personal on the front.'),
            Repeater::make('apps')
                ->label('Apps / components')
                ->helperText('Use this when one project ships as multiple apps (e.g. web + mobile). Leave empty for single-app projects.')
                ->columnSpanFull()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                ->defaultItems(0)
                ->addActionLabel('Add app')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('platform')
                        ->maxLength(255)
                        ->placeholder('Web · iOS · Android · Backend · …'),
                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                    TagsInput::make('tech_stack')
                        ->placeholder('Add tech and press Enter'),
                    KeyValue::make('links')
                        ->keyLabel('Label')
                        ->valueLabel('URL')
                        ->addActionLabel('Add link')
                        ->reorderable()
                        ->helperText('Live, Code, App Store, Play Store, TestFlight, …')
                        ->columnSpanFull(),
                ]),
            TextInput::make('url')
                ->label('Live URL')
                ->url()
                ->maxLength(255),
            TextInput::make('repo_url')
                ->label('Repository URL')
                ->url()
                ->maxLength(255),
            Toggle::make('featured')
                ->helperText('Featured projects show on the home page (top 3). Marking a new one un-features the oldest automatically.'),
            TextInput::make('sort_order')
                ->numeric()
                ->default(0),
            DateTimePicker::make('published_at')
                ->helperText('Leave empty to keep as a draft (hidden from the public site).'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('experience.company')
                    ->label('Done at')
                    ->placeholder('Personal')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('featured')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Draft'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('experience_id')
                    ->label('Experience')
                    ->relationship('experience', 'company', fn ($query) => $query->where('owner_id', auth()->id()))
                    ->preload(),
                TernaryFilter::make('featured'),
                TernaryFilter::make('published_at')
                    ->label('Published')
                    ->nullable()
                    ->placeholder('All')
                    ->trueLabel('Published')
                    ->falseLabel('Drafts'),
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
