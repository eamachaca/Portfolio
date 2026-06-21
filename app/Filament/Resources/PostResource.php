<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Support\LocaleTabs;
use App\Models\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?int $navigationSort = 8;

    public static function getNavigationLabel(): string
    {
        return __('Blog');
    }

    public static function getModelLabel(): string
    {
        return __('Post');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Posts');
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
                Textarea::make("excerpt.{$locale}")
                    ->label(__('Excerpt'))
                    ->rows(2)
                    ->maxLength(255)
                    ->columnSpanFull(),
                RichEditor::make("content.{$locale}")
                    ->label(__('Content'))
                    ->columnSpanFull(),
            ]),
            FileUpload::make('cover_image')
                ->label(__('Cover image'))
                ->image()
                ->disk('public')
                ->directory('posts/covers')
                ->imageEditor()
                ->maxSize(4096),
            DateTimePicker::make('published_at')
                ->label(__('Published at'))
                ->helperText(__('Leave empty to keep as a draft.')),
            TextInput::make('sort_order')->label(__('Sort order'))->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')->label(__('Cover image'))->disk('public')->square(),
                TextColumn::make('title')->label(__('Title'))->searchable()->sortable(),
                TextColumn::make('published_at')->label(__('Published at'))->dateTime()->sortable()->placeholder(__('Draft')),
                TextColumn::make('sort_order')->label(__('Sort order'))->sortable()->toggleable(),
            ])
            ->defaultSort('published_at', 'desc')
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->id());
    }
}
