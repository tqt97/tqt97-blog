<?php

namespace App\Filament\Pages;

use App\Models\Seo;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use  Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;



class General extends Page implements HasForms
{
    use InteractsWithForms;

    private Seo $seo;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.general';

    public static function getNavigationGroup(): ?string
    {
        return __('settings');
    }

    public function mount(): void
    {
        $seo = Seo::find(1);
        $this->form->fill($seo?->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('section_main_content'))
                    // ->description(__('section_main_content_description'))
                    ->icon('heroicon-m-computer-desktop')
                    ->collapsible()
                    ->persistCollapsed()
                    ->compact()
                    ->schema([
                        TextInput::make('meta_title')
                            ->columnSpan(6),
                        TextInput::make('meta_description')
                            ->columnSpan(6),
                        TextInput::make('meta_keywords')
                            ->columnSpan(6),
                        TextInput::make('meta_robots')
                            ->columnSpan(6),
                        TextInput::make('meta_author')
                            ->columnSpan(6),
                        TextInput::make('meta_canonical')->columnSpan(6),
                        TextInput::make('meta_og_title')->columnSpan(6),
                        TextInput::make('meta_og_description')->columnSpan(6),
                        TextInput::make('meta_og_image')->columnSpan(6),
                        TextInput::make('meta_og_type')->columnSpan(6),
                        TextInput::make('meta_og_url')->columnSpan(6),
                        TextInput::make('meta_og_locale')->columnSpan(6),
                        // TextInput::make('meta_twitter_title')->columnSpan(6),
                        // TextInput::make('meta_twitter_description')->columnSpan(6),
                        // TextInput::make('meta_twitter_image')->columnSpan(6),
                        // TextInput::make('meta_twitter_card')->columnSpan(6),
                        // TextInput::make('meta_twitter_site')->columnSpan(6),
                        // TextInput::make('meta_twitter_creator')->columnSpan(6),
                        // TextInput::make('meta_twitter_url')->columnSpan(6),
                        // TextInput::make('meta_twitter_locale')->columnSpan(6),
                    ])->columns(12)
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $seo = Seo::find(1);
            if ($seo) {
                $seo->update($data);
            } else {
                Seo::create($data);
            }
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }
}
