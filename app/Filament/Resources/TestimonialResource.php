<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('author')->required()->maxLength(255),
            TextInput::make('role')->maxLength(255),
            TextInput::make('company')->maxLength(255),
            Textarea::make('quote')->required()->rows(4)->columnSpanFull(),
            Select::make('source')
                ->label('Plataforma de origen')
                ->options([
                    'workana' => 'Workana',
                    'linkedin' => 'LinkedIn',
                    'upwork' => 'Upwork',
                    'email' => 'Email',
                    'other' => 'Otro',
                ])
                ->native(false),
            TextInput::make('source_url')
                ->label('URL del testimonio original')
                ->url()
                ->maxLength(500),
            FileUpload::make('avatar')
                ->image()
                ->avatar()
                ->disk('public')
                ->directory('testimonials')
                ->maxSize(1024),
            TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')->disk('public')->circular(),
                TextColumn::make('author')->searchable()->sortable(),
                TextColumn::make('company')->toggleable(),
                TextColumn::make('sort_order')->sortable()->toggleable(),
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->id());
    }
}
