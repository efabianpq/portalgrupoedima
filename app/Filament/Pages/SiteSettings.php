<?php

namespace App\Filament\Pages;

use App\Filament\Support\TranslatableTabs;
use App\Models\SiteSetting;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Datos globales del sitio: los que se repiten en todas las páginas
 * (contacto, redes sociales, pie de página).
 *
 * Es una página propia y no un Resource porque sólo existe una fila: la
 * persona editora no debe ver una lista con un único elemento.
 */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.site-settings';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $navigationLabel = 'Configuración del sitio';

    protected static ?string $title = 'Configuración del sitio';

    protected static ?int $navigationSort = 2;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('Datos de contacto')
                    ->description('Aparecen en el pie de página y en la página de Contacto.')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Nombre de la empresa')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->helperText('Sólo números, con código de país. Ejemplo: 573001234567.')
                            ->maxLength(50),
                        TextInput::make('google_maps_url')
                            ->label('Enlace de Google Maps')
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Dirección')
                    ->schema([
                        TranslatableTabs::make(fn (string $locale) => [
                            Textarea::make("address.{$locale}")
                                ->label('Dirección')
                                ->rows(2)
                                ->maxLength(500),
                        ], 'Dirección'),
                    ]),

                Section::make('Redes sociales')
                    ->schema([
                        Repeater::make('social_links')
                            ->hiddenLabel()
                            ->schema([
                                Select::make('platform')
                                    ->label('Red social')
                                    ->options([
                                        'linkedin' => 'LinkedIn',
                                        'x' => 'X (Twitter)',
                                        'facebook' => 'Facebook',
                                        'instagram' => 'Instagram',
                                        'youtube' => 'YouTube',
                                    ])
                                    ->required(),
                                TextInput::make('url')
                                    ->label('Enlace')
                                    ->url()
                                    ->required()
                                    ->maxLength(500),
                            ])
                            ->columns(2)
                            ->addActionLabel('Agregar red social')
                            ->reorderable()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pie de página')
                    ->schema([
                        TranslatableTabs::make(fn (string $locale) => [
                            Textarea::make("footer_text.{$locale}")
                                ->label('Texto del pie de página')
                                ->rows(3)
                                ->maxLength(500),
                        ], 'Pie de página'),
                    ]),

                Section::make('Buscadores (SEO) por defecto')
                    ->description('Se usan en las páginas que no tengan su propio texto para buscadores.')
                    ->collapsed()
                    ->schema([
                        TranslatableTabs::make(fn (string $locale) => [
                            TextInput::make("meta_title.{$locale}")
                                ->label('Título en buscadores')
                                ->maxLength(60)
                                ->helperText('Máximo 60 caracteres.'),
                            Textarea::make("meta_description.{$locale}")
                                ->label('Descripción en buscadores')
                                ->rows(2)
                                ->maxLength(160)
                                ->helperText('Máximo 160 caracteres.'),
                        ], 'Buscadores'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $setting = SiteSetting::current();
        $setting->fill($this->form->getState());
        $setting->save();

        Notification::make()
            ->success()
            ->title('Configuración guardada')
            ->send();
    }
}
