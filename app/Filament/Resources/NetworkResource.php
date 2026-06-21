<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NetworkResource\Pages;
use App\Models\Network;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NetworkResource extends Resource
{
    protected static ?string $model = Network::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 8;

    public static function getNavigationLabel(): string
    {
        return __('Networks');
    }

    public static function getModelLabel(): string
    {
        return __('Network');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Networks');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(50)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => filled($state) ? $set('slug', Str::slug($state)) : null),
            TextInput::make('slug')
                ->label(__('Slug'))
                ->required()
                ->maxLength(80)
                ->unique(ignoreRecord: true)
                ->helperText(__('URL-safe identifier. Auto from name; change only if you know what you are doing.')),
            TextInput::make('themify_class')
                ->label(__('Themify class'))
                ->maxLength(60)
                ->placeholder('ti-github · ti-linkedin · ti-twitter-alt')
                ->helperText(__('Themify icon class (from the ReFrame icon set). If set, takes precedence over the uploaded icon.')),
            FileUpload::make('icon_path')
                ->label(__('Icon (upload)'))
                ->image()
                ->disk('public')
                ->directory('network-icons')
                ->maxSize(512)
                ->helperText(__('Used as fallback when no themify class is set. Square SVG/PNG, ideally under 256×256.')),
            Toggle::make('is_approved')
                ->label(__('Approved (visible to all owners)'))
                ->helperText(__('When off, only the creator and admins see this network in their Profile Select.')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->withCount('socialLinks'))
            ->columns([
                ImageColumn::make('icon_path')
                    ->label(__('Icon'))
                    ->disk('public')
                    ->square()
                    ->size(28)
                    ->placeholder('—'),
                TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
                TextColumn::make('slug')->label(__('Slug'))->toggleable()->color('gray'),
                TextColumn::make('themify_class')
                    ->label(__('Themify class'))
                    ->toggleable()
                    ->placeholder('—')
                    ->color('gray'),
                TextColumn::make('social_links_count')
                    ->label(__('Profiles'))
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('is_approved')
                    ->label(__('Approved'))
                    ->boolean(),
                TextColumn::make('mergedInto.name')
                    ->label(__('Merged into'))
                    ->toggleable()
                    ->placeholder('—')
                    ->color('warning'),
                TextColumn::make('creator.name')
                    ->label(__('Created by'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder(__('system')),
                TextColumn::make('created_at')->label(__('Created at'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label(__('Status'))
                    ->placeholder(__('All'))
                    ->trueLabel(__('Approved'))
                    ->falseLabel(__('Pending')),
                TernaryFilter::make('merged_into_id')
                    ->label(__('Alias'))
                    ->placeholder(__('Active only'))
                    ->trueLabel(__('Aliases only'))
                    ->falseLabel(__('Active only'))
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('merged_into_id'),
                        false: fn (Builder $q) => $q->whereNull('merged_into_id'),
                        blank: fn (Builder $q) => $q->whereNull('merged_into_id'),
                    ),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Network $n): bool => ! $n->is_approved && ! $n->isAlias())
                    ->requiresConfirmation()
                    ->modalDescription(fn (Network $n) => __('Approve ":name" so it appears in every owner\'s Profile Select.', ['name' => $n->name]))
                    ->action(function (Network $n): void {
                        $n->update(['is_approved' => true]);
                        Notification::make()->title(__('Approved'))->success()->send();
                    }),
                Action::make('merge')
                    ->label(__('Merge'))
                    ->icon('heroicon-o-arrows-pointing-in')
                    ->color('warning')
                    ->visible(fn (Network $n): bool => ! $n->isAlias())
                    ->modalHeading(fn (Network $n) => __('Merge ":name" into…', ['name' => $n->name]))
                    ->modalDescription(__('Reassigns every profile using this network to the target. The current name becomes an alias and is permanently banned from being recreated.'))
                    ->modalSubmitActionLabel(__('Merge and alias'))
                    ->schema(fn (Network $n) => [
                        Select::make('target_id')
                            ->label(__('Target network'))
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->options(fn () => Network::query()
                                ->active()
                                ->where('id', '!=', $n->id)
                                ->orderBy('name')
                                ->pluck('name', 'id')),
                    ])
                    ->action(function (Network $n, array $data): void {
                        $target = Network::find($data['target_id']);

                        if (! $target || $target->id === $n->id) {
                            Notification::make()->title(__('Invalid target'))->danger()->send();
                            return;
                        }

                        DB::transaction(function () use ($n, $target): void {
                            DB::table('social_links')
                                ->where('network_id', $n->id)
                                ->whereNotIn('user_id', function ($q) use ($target): void {
                                    $q->select('user_id')->from('social_links')->where('network_id', $target->id);
                                })
                                ->update(['network_id' => $target->id, 'updated_at' => now()]);

                            DB::table('social_links')->where('network_id', $n->id)->delete();

                            $n->update(['merged_into_id' => $target->id, 'is_approved' => false]);
                        });

                        Notification::make()
                            ->title(__('Merged'))
                            ->body(__('":name" is now an alias of ":target".', ['name' => $n->name, 'target' => $target->name]))
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (Network $n): bool => $n->social_links_count === 0),
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
            'index' => Pages\ListNetworks::route('/'),
            'create' => Pages\CreateNetwork::route('/create'),
            'edit' => Pages\EditNetwork::route('/{record}/edit'),
        ];
    }
}
