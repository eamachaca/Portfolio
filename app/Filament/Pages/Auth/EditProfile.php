<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Support\LocaleTabs;
use App\Models\Network;
use App\Support\Locales;
use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    public function getMaxWidth(): Width|string|null
    {
        return Width::FiveExtraLarge;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent()
                ->label(__('Display name'))
                ->helperText(__('Short name used in the title, sidebar and across the site (e.g. Eduardo Machaca).')),
            TextInput::make('full_name')
                ->label(__('Full name'))
                ->maxLength(255)
                ->helperText(__('Full name, shown only in the About section (e.g. Eduardo Andrés Machaca Peña).')),
            TextInput::make('username')
                ->label(__('Username'))
                ->disabled()
                ->dehydrated(false)
                ->helperText(__('Used for login and for the public profile URL when multi-persona is enabled. Cannot be changed after creation — it is the login key and is referenced by external links.')),
            $this->getEmailFormComponent(),
            FileUpload::make('avatar')
                ->label(__('Avatar'))
                ->image()
                ->disk('public')
                ->directory('avatars')
                ->imageEditor()
                ->avatar()
                ->maxSize(2048),
            LocaleTabs::for(fn (string $locale) => [
                TextInput::make("headline.{$locale}")
                    ->label(__('Headline'))
                    ->maxLength(255)
                    ->helperText(__('Short tagline shown next to your name.')),
                Textarea::make("bio.{$locale}")
                    ->label(__('Bio'))
                    ->rows(5)
                    ->columnSpanFull(),
                FileUpload::make("resume.{$locale}")
                    ->label(__('Resume (PDF)'))
                    ->helperText(__('Downloadable CV shown next to the hero in this language.'))
                    ->disk('public')
                    ->directory('resumes')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(5120),
            ]),
            Section::make(__('Social links'))
                ->description(__('Profiles and contact links shown in the menu of your portfolio. Each one must point to a network from the catalog; create a new one if it is not there.'))
                ->schema([
                    Repeater::make('socialLinks')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->addActionLabel(__('Add link'))
                        ->defaultItems(0)
                        ->columnSpanFull()
                        ->itemLabel(fn (array $state): ?string => Network::find($state['network_id'] ?? null)?->name)
                        ->schema([
                            Select::make('network_id')
                                ->label(__('Network'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->options(fn () => Network::query()
                                    ->active()
                                    ->where(function ($q): void {
                                        $q->where('is_approved', true)
                                            ->orWhere('created_by', auth()->id());
                                    })
                                    ->orderBy('name')
                                    ->pluck('name', 'id'))
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label(__('Name'))
                                        ->required()
                                        ->maxLength(50)
                                        ->helperText(__('e.g. "Workana", "Bsky". If a network with this name already exists or was banned, you will be told.')),
                                    FileUpload::make('icon_path')
                                        ->label(__('Icon'))
                                        ->image()
                                        ->disk('public')
                                        ->directory('network-icons')
                                        ->maxSize(512)
                                        ->helperText(__('Square SVG or PNG, ideally under 256×256.')),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    $slug = Str::slug($data['name']);

                                    $existing = Network::query()->where('slug', $slug)->first();

                                    if ($existing) {
                                        if ($existing->isAlias()) {
                                            Notification::make()
                                                ->title(__('Name not available'))
                                                ->body(__('":name" was merged into ":target". Pick that one instead.', ['name' => $data['name'], 'target' => $existing->mergedInto->name]))
                                                ->danger()
                                                ->send();

                                            return $existing->mergedInto->id;
                                        }

                                        Notification::make()
                                            ->title(__('Already exists'))
                                            ->body(__('":name" is already in the catalog.', ['name' => $existing->name]))
                                            ->warning()
                                            ->send();

                                        return $existing->id;
                                    }

                                    $network = Network::create([
                                        'slug' => $slug,
                                        'name' => $data['name'],
                                        'icon_path' => $data['icon_path'] ?? null,
                                        'is_approved' => false,
                                        'created_by' => auth()->id(),
                                    ]);

                                    Notification::make()
                                        ->title(__('Network created'))
                                        ->body(__('Visible to you immediately. An admin will review it before other users see it.'))
                                        ->success()
                                        ->send();

                                    return $network->id;
                                }),
                            TextInput::make('url')
                                ->label(__('URL'))
                                ->required()
                                ->url()
                                ->maxLength(2048),
                        ]),
                ])
                ->columnSpanFull(),
            Section::make(__('Languages'))
                ->description(__('Locales your portfolio is published in. The default is what visitors see if no language is selected.'))
                ->schema([
                    CheckboxList::make('active_locales')
                        ->label(__('Active locales'))
                        ->options(Locales::available())
                        ->required()
                        ->minItems(1)
                        ->live()
                        ->columns(3)
                        ->helperText(__('Each translatable field can be filled per active locale. Empty locales fall back to your default.')),
                    Select::make('default_locale')
                        ->label(__('Default locale'))
                        ->options(fn (Get $get) => Arr::only(
                            Locales::available(),
                            (array) $get('active_locales') ?: ['en'],
                        ))
                        ->required()
                        ->native(false),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label(__('Change password'))
                ->icon(Heroicon::OutlinedKey)
                ->color('gray')
                ->modalHeading(__('Change password'))
                ->modalDescription(__('Are you sure you want to change your password? You will need to log in again on other devices.'))
                ->modalSubmitActionLabel(__('Save new password'))
                ->schema([
                    TextInput::make('current_password')
                        ->label(__('Current password'))
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->autocomplete('current-password')
                        ->required()
                        ->currentPassword(guard: Filament::getAuthGuard()),
                    TextInput::make('new_password')
                        ->label(__('New password'))
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->autocomplete('new-password')
                        ->required()
                        ->rule(Password::default())
                        ->same('new_password_confirmation'),
                    TextInput::make('new_password_confirmation')
                        ->label(__('Confirm new password'))
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->autocomplete('new-password')
                        ->required()
                        ->dehydrated(false),
                ])
                ->action(function (array $data): void {
                    $user = $this->getUser();
                    $user->forceFill(['password' => Hash::make($data['new_password'])])->save();

                    if (request()->hasSession()) {
                        request()->session()->put([
                            'password_hash_' . Filament::getAuthGuard() => $user->getAuthPassword(),
                        ]);
                    }

                    Notification::make()
                        ->title(__('Password updated'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
