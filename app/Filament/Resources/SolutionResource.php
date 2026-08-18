<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolutionResource\Pages;
use App\Filament\Support\ContentColumns;
use App\Filament\Support\ContentFields;
use App\Filament\Support\TranslatableTabs;
use App\Models\Service;
use App\Models\Solution;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SolutionResource extends Resource
{
    protected static ?string $model = Solution::class;

    /**
     * En el panel los registros se identifican por ID, no por slug: el slug
     * es traducible y puede cambiar, y la URL del panel no debe depender de él.
     */
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Contenido del sitio';

    protected static ?string $navigationLabel = 'Soluciones';

    protected static ?string $modelLabel = 'solución';

    protected static ?string $pluralModelLabel = 'soluciones';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Textos de la solución')
                ->description('El nombre debe corresponder a la disciplina de HOPEX (ver storage/migration/ANALISIS-REFERENCIA.md). Escribe el contenido en los dos idiomas.')
                ->schema([
                    TranslatableTabs::make(fn (string $locale) => [
                        ContentFields::title($locale, 'Nombre de la solución'),
                        ContentFields::slug($locale, Solution::class),
                        Textarea::make("summary.{$locale}")
                            ->label('Resumen breve')
                            ->helperText('Una o dos frases. Es lo que se ve en la lista de soluciones.')
                            ->rows(3)
                            ->maxLength(500),
                        ContentFields::richText("body.{$locale}", 'Contenido: qué resuelve, preguntas que responde y cómo lo implementamos'),
                        TextInput::make("cta_label.{$locale}")
                            ->label('Texto del botón de contacto')
                            ->maxLength(255),
                    ]),
                ]),

            Section::make('Servicios relacionados')
                ->description('Con qué servicios se entrega típicamente esta solución.')
                ->schema([
                    Select::make('services')
                        ->label('Servicios')
                        ->relationship('services', 'id')
                        ->getOptionLabelFromRecordUsing(fn (Service $service) => $service->title)
                        ->multiple()
                        ->preload()
                        ->searchable(),
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
                ContentColumns::translated('title', 'Nombre de la solución'),
                ContentColumns::published(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publicado')
                    ->trueLabel('Solo publicadas')
                    ->falseLabel('Solo ocultas')
                    ->placeholder('Todas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Todavía no hay soluciones')
            ->emptyStateDescription('Crea la primera solución con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSolutions::route('/'),
            'create' => Pages\CreateSolution::route('/create'),
            // Ver la nota equivalente en ServiceResource::getPages(): ":id"
            // evita el 404 al hacer clic en "Editar" desde la tabla.
            'edit' => Pages\EditSolution::route('/{record:id}/edit'),
        ];
    }
}
