<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Filament\Support\ContentColumns;
use App\Filament\Support\ContentFields;
use App\Filament\Support\TranslatableTabs;
use App\Models\MenuItem;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $navigationLabel = 'Menú del sitio';

    protected static ?string $modelLabel = 'ítem de menú';

    protected static ?string $pluralModelLabel = 'ítems de menú';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Enlace del menú')
                ->description(
                    'El texto y la dirección (URL) cambian según el idioma. '.
                    'Direcciones internas útiles hoy: '.self::urlHints().'. '.
                    'Puedes usar direcciones internas ("/es/soluciones") o externas ("https://...").'
                )
                ->schema([
                    TranslatableTabs::make(fn (string $locale) => [
                        TextInput::make("label.{$locale}")
                            ->label('Texto del enlace')
                            ->helperText('Lo que se ve en el menú, p. ej. "Servicios".')
                            ->maxLength(60)
                            ->required($locale === config('site.default_locale')),
                        TextInput::make("url.{$locale}")
                            ->label('Dirección (URL)')
                            ->helperText('Ruta interna (empieza con /) o enlace externo completo.')
                            ->maxLength(500)
                            ->required($locale === config('site.default_locale')),
                    ]),
                ]),

            Section::make('Ajustes')
                ->description(
                    '⚠️ Antes de activar un enlace a Equipo o Casos de éxito, confirma que esa '.
                    'sección ya tiene contenido publicado: un enlace a una página vacía resta '.
                    'credibilidad al sitio.'
                )
                ->schema([
                    ContentFields::publishToggle()->label('Visible en el menú'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                ContentColumns::translated('label', 'Texto'),
                ContentColumns::translated('url', 'Dirección (URL)'),
                ContentColumns::published(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Visible')
                    ->trueLabel('Solo visibles')
                    ->falseLabel('Solo ocultos')
                    ->placeholder('Todos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Todavía no hay ítems de menú')
            ->emptyStateDescription('Agrega el primer enlace del menú con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record:id}/edit'),
        ];
    }

    /**
     * Lista de referencia rápida con las URLs vigentes del sitio, para que
     * quien edite el menú no tenga que adivinarlas ni abrir routes/web.php.
     */
    protected static function urlHints(): string
    {
        $locale = config('site.default_locale');

        $routes = ['home', 'hopex', 'solutions', 'services', 'projects', 'blog', 'team', 'about', 'contact'];

        return collect($routes)
            ->map(fn (string $name) => route("{$locale}.{$name}", absolute: false))
            ->implode(', ');
    }
}
