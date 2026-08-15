<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Support\ContentColumns;
use App\Filament\Support\ContentFields;
use App\Filament\Support\TranslatableTabs;
use App\Models\Testimonial;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Contenido del sitio';

    protected static ?string $navigationLabel = 'Testimonios';

    protected static ?string $modelLabel = 'testimonio';

    protected static ?string $pluralModelLabel = 'testimonios';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Quién da el testimonio')
                ->schema([
                    TextInput::make('author_name')
                        ->label('Nombre de la persona')
                        ->helperText('No se traduce: es un nombre propio.')
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make('Cargo y testimonio')
                ->description('El cargo y el texto del testimonio sí cambian según el idioma.')
                ->schema([
                    TranslatableTabs::make(fn (string $locale) => [
                        TextInput::make("author_role.{$locale}")
                            ->label('Cargo y empresa')
                            ->helperText('Por ejemplo: Directora de Tecnología, Banco de Bogotá.')
                            ->maxLength(255),
                        Textarea::make("quote.{$locale}")
                            ->label('Testimonio')
                            ->required($locale === config('site.default_locale'))
                            ->rows(5)
                            ->maxLength(1000),
                    ]),
                ]),

            Section::make('Foto')
                ->schema([
                    ContentFields::image(Testimonial::PHOTO, 'Foto de la persona'),
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
                ContentColumns::image(Testimonial::PHOTO, 'Foto'),
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Persona')
                    ->searchable(),
                ContentColumns::translated('quote', 'Testimonio'),
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
            ->emptyStateHeading('Todavía no hay testimonios')
            ->emptyStateDescription('Agrega el primer testimonio con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
