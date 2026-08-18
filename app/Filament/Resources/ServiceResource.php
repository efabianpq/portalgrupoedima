<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Support\ContentColumns;
use App\Filament\Support\ContentFields;
use App\Filament\Support\TranslatableTabs;
use App\Models\Service;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    /**
     * En el panel los registros se identifican por ID, no por slug: el slug
     * es traducible y puede cambiar, y la URL del panel no debe depender de él.
     */
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Contenido del sitio';

    protected static ?string $navigationLabel = 'Servicios';

    protected static ?string $modelLabel = 'servicio';

    protected static ?string $pluralModelLabel = 'servicios';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Textos del servicio')
                ->description('Escribe el contenido en los dos idiomas. Si dejas el inglés vacío, el servicio no aparecerá en la versión en inglés del sitio.')
                ->schema([
                    TranslatableTabs::make(fn (string $locale) => [
                        ContentFields::title($locale, 'Nombre del servicio'),
                        ContentFields::slug($locale, Service::class),
                        Textarea::make("summary.{$locale}")
                            ->label('Resumen breve')
                            ->helperText('Una o dos frases. Es lo que se ve en la lista de servicios.')
                            ->rows(3)
                            ->maxLength(500),
                        ContentFields::richText("body.{$locale}", 'Descripción completa'),
                    ]),
                ]),

            Section::make('Imagen')
                ->schema([
                    ContentFields::image(Service::IMAGE, 'Imagen del servicio'),
                ]),

            Section::make('Ajustes')
                ->schema([
                    TextInput::make('icon')
                        ->label('Ícono')
                        ->helperText('Opcional. Nombre del ícono a mostrar junto al servicio.')
                        ->maxLength(255),
                    ContentFields::publishToggle(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                ContentColumns::image(Service::IMAGE),
                ContentColumns::translated('title', 'Nombre del servicio'),
                ContentColumns::published(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publicado')
                    ->trueLabel('Solo publicados')
                    ->falseLabel('Solo ocultos')
                    ->placeholder('Todos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Todavía no hay servicios')
            ->emptyStateDescription('Crea el primer servicio con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            // ":id" fuerza a Laravel a usar el ID también al GENERAR la URL del
            // botón "Editar" (route()->getRouteKey() del modelo devuelve el
            // slug, no el ID — ver App\Models\Concerns\HasTranslatableSlug).
            // Sin esto, el botón enlaza a /admin/servicios/{slug}/edit pero el
            // panel resuelve el registro por ID (ver $recordRouteKeyName más
            // arriba) y el clic siempre da 404.
            'edit' => Pages\EditService::route('/{record:id}/edit'),
        ];
    }
}
