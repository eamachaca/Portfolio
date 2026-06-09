<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExperienceResource\Pages;
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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('company')
                ->required()
                ->maxLength(255),
            FileUpload::make('logo')
                ->image()
                ->disk('public')
                ->directory('experiences/logos')
                ->maxSize(2048),
            Textarea::make('summary')
                ->rows(3)
                ->columnSpanFull()
                ->helperText('Optional. Single description shared across all levels of this role. Leave empty if each level has its own.'),
            TagsInput::make('tech_stack')
                ->placeholder('Add a tech and press Enter')
                ->helperText('Shared stack across all levels at this company.'),
            TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->helperText('Lower numbers show first. Use to push current job to the top.'),
            Repeater::make('levels')
                ->label('Levels / promotions')
                ->helperText('One entry per role at this company. Add another when you got promoted.')
                ->columnSpanFull()
                ->collapsible()
                ->minItems(1)
                ->defaultItems(1)
                ->reorderable()
                ->addActionLabel('Add level')
                ->itemLabel(fn (array $state): ?string => $state['role'] ?? null)
                ->schema([
                    TextInput::make('role')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ssr. Backend Engineer'),
                    DatePicker::make('start_date')
                        ->native(false),
                    DatePicker::make('end_date')
                        ->native(false)
                        ->helperText('Leave empty if currently in this role.'),
                    Toggle::make('in_progress')
                        ->label('Current'),
                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText('Optional. Description specific to this level.'),
                    Repeater::make('highlights')
                        ->label('Highlights / bullets')
                        ->columnSpanFull()
                        ->reorderable()
                        ->defaultItems(0)
                        ->addActionLabel('Add bullet')
                        ->simple(
                            TextInput::make('text')
                                ->maxLength(500)
                                ->placeholder('What you shipped, improved or owned.')
                        ),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->disk('public')
                    ->square(),
                TextColumn::make('company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('levels')
                    ->label('Levels')
                    ->state(fn (Experience $r): string => (string) count($r->levels ?? [])),
                TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
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
