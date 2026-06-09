<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent()
                ->label('Display name')
                ->helperText('Short name used in the title, sidebar and across the site (e.g. Eduardo Machaca).'),
            TextInput::make('full_name')
                ->maxLength(255)
                ->helperText('Full name, shown only in the About section (e.g. Eduardo Andrés Machaca Peña).'),
            TextInput::make('username')
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('Used for login and for the public profile URL when multi-persona is enabled.'),
            $this->getEmailFormComponent(),
            TextInput::make('headline')
                ->maxLength(255)
                ->helperText('Short tagline shown next to your name.'),
            Textarea::make('bio')
                ->rows(5)
                ->columnSpanFull(),
            FileUpload::make('avatar')
                ->image()
                ->disk('public')
                ->directory('avatars')
                ->imageEditor()
                ->avatar()
                ->maxSize(2048),
            KeyValue::make('social_links')
                ->keyLabel('Network')
                ->valueLabel('URL')
                ->addActionLabel('Add link')
                ->reorderable()
                ->columnSpanFull(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ]);
    }
}
