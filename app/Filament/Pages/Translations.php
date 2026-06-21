<?php

namespace App\Filament\Pages;

use App\Filament\Support\LocaleTabs;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class Translations extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = -2;

    protected string $view = 'filament.pages.translations';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('Translations');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Translations');
    }

    public function mount(): void
    {
        $this->form->fill($this->loadFromUser(auth()->user()));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make(__('Hero'))
                    ->description(__('First thing a visitor sees on the home page.'))
                    ->schema([
                        TextInput::make('hero_tag')
                            ->label(__('Tag'))
                            ->maxLength(255)
                            ->helperText(__('Short uppercase categorisation shown above the headline (e.g. "WEB APPS / LARAVEL / AUTOMATION"). Not translated.')),
                        LocaleTabs::for(fn (string $locale) => [
                            TextInput::make("hero_title.{$locale}")->label(__('Headline'))->maxLength(255),
                            Textarea::make("hero_copy.{$locale}")->label(__('Sub-headline'))->rows(3)->columnSpanFull(),
                            TextInput::make("hero_note.{$locale}")->label(__('Note under sub-headline'))->maxLength(255),
                        ], key: 'hero_translations'),
                    ])
                    ->columnSpanFull(),

                $this->sectionHeading(__('About'), 'about', withBody: true),
                $this->sectionHeading(__('Strengths'), 'strengths', withIntro: true),
                $this->sectionHeading(__('Experience'), 'experience', withIntro: true),
                $this->sectionHeading(__('Education'), 'education'),
                $this->sectionHeading(__('Portfolio'), 'portfolio', withIntro: true),
                $this->sectionHeading(__('Skills'), 'skills', withIntro: true),
                $this->sectionHeading(__('Work style'), 'workstyle', withIntro: true),
                $this->sectionHeading(__('Testimonials'), 'testimonials'),
                $this->sectionHeading(__('FAQ'), 'faq'),
                $this->sectionHeading(__('Blog'), 'blog'),
                $this->sectionHeading(__('Contact'), 'contact', withIntro: true),
            ]);
    }

    protected function sectionHeading(string $label, string $prefix, bool $withIntro = false, bool $withBody = false): Section
    {
        $tabKey = "{$prefix}_translations";

        return Section::make($label)
            ->collapsible()
            ->collapsed()
            ->schema([
                LocaleTabs::for(function (string $locale) use ($prefix, $withIntro, $withBody) {
                    $fields = [
                        TextInput::make("{$prefix}_heading.{$locale}")->label(__('Heading'))->maxLength(255),
                    ];
                    if ($withIntro) {
                        $fields[] = Textarea::make("{$prefix}_intro.{$locale}")->label(__('Intro paragraph'))->rows(3)->columnSpanFull();
                    }
                    if ($withBody) {
                        $fields[] = Textarea::make("{$prefix}_body.{$locale}")->label(__('Body'))->rows(5)->columnSpanFull();
                    }

                    return $fields;
                }, key: $tabKey),
            ])
            ->columnSpanFull();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label(__('Save'))->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        foreach ($data as $key => $value) {
            $user->{$key} = $value;
        }
        $user->save();

        Notification::make()->title(__('Translations saved'))->success()->send();
    }

    /**
     * Pull translatable JSON columns as full arrays so the locale tabs show
     * existing values across locales.
     */
    protected function loadFromUser(User $user): array
    {
        $payload = ['hero_tag' => $user->hero_tag];

        $translatable = [
            'hero_title', 'hero_copy', 'hero_note',
            'about_heading', 'about_body',
            'strengths_heading', 'strengths_intro',
            'experience_heading', 'experience_intro',
            'education_heading',
            'portfolio_heading', 'portfolio_intro',
            'skills_heading', 'skills_intro',
            'workstyle_heading', 'workstyle_intro',
            'testimonials_heading',
            'faq_heading',
            'blog_heading',
            'contact_heading', 'contact_intro',
        ];

        foreach ($translatable as $field) {
            $payload[$field] = $user->getTranslations($field);
        }

        return $payload;
    }
}
