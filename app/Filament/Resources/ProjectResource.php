<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Support\ContentColumns;
use App\Filament\Support\ContentFields;
use App\Filament\Support\TranslatableTabs;
use App\Models\Project;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    /** En el panel los registros se identifican por ID, no por el slug traducible. */
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Contenido del sitio';

    protected static ?string $navigationLabel = 'Casos de éxito';

    protected static ?string $modelLabel = 'caso de éxito';

    protected static ?string $pluralModelLabel = 'casos de éxito';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Textos del proyecto')
                ->description('Escribe el contenido en los dos idiomas. Si dejas el inglés vacío, el proyecto no aparecerá en la versión en inglés del sitio.')
                ->schema([
                    TranslatableTabs::make(fn (string $locale) => [
                        ContentFields::title($locale, 'Nombre del proyecto'),
                        ContentFields::slug($locale, Project::class),
                        Textarea::make("summary.{$locale}")
                            ->label('Resumen breve')
                            ->helperText('Una o dos frases. Es lo que se ve en la lista de proyectos.')
                            ->rows(3)
                            ->maxLength(500),
                        ContentFields::richText("body.{$locale}", 'Descripción del caso de éxito'),
                    ]),
                ]),

            Section::make('Datos del proyecto')
                ->schema([
                    TextInput::make('client_name')
                        ->label('Nombre del cliente')
                        ->helperText('No se traduce: es un nombre propio.')
                        ->maxLength(255),
                    Select::make('services')
                        ->label('Servicios relacionados')
                        ->helperText('Servicios a los que corresponde este proyecto.')
                        ->relationship('services')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->title)
                        ->multiple()
                        ->preload(),
                ])
                ->columns(2),

            Section::make('Imagen')
                ->schema([
                    ContentFields::image(Project::COVER, 'Imagen de portada'),
                ]),

            Section::make('Ajustes')
                ->schema([
                    ContentFields::publishToggle(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                ContentColumns::image(Project::COVER, 'Portada'),
                ContentColumns::translated('title', 'Nombre del proyecto'),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->toggleable(),
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
            ->emptyStateHeading('Todavía no hay proyectos')
            ->emptyStateDescription('Crea el primer caso de éxito con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            // Ver la nota equivalente en ServiceResource::getPages(): ":id"
            // evita el 404 al hacer clic en "Editar" desde la tabla.
            'edit' => Pages\EditProject::route('/{record:id}/edit'),
        ];
    }
}
