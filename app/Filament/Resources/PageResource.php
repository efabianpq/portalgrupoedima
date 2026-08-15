<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\ContentColumns;
use App\Filament\Support\ContentFields;
use App\Filament\Support\TranslatableTabs;
use App\Models\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Páginas institucionales: Inicio, Nosotros y Contacto.
 *
 * Son un conjunto cerrado: la persona editora edita sus textos pero no puede
 * crearlas ni borrarlas, porque las rutas del sitio dependen de que existan.
 */
class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Contenido del sitio';

    protected static ?string $navigationLabel = 'Páginas del sitio';

    protected static ?string $modelLabel = 'página';

    protected static ?string $pluralModelLabel = 'páginas';

    protected static ?int $navigationSort = 0;

    /** Las páginas existen desde la instalación; no se crean a mano. */
    public static function canCreate(): bool
    {
        return false;
    }

    /** Borrar una página rompería la navegación del sitio. */
    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Textos de la página')
                ->description('Escribe el contenido en los dos idiomas.')
                ->schema([
                    TranslatableTabs::make(fn (string $locale) => [
                        TextInput::make("title.{$locale}")
                            ->label('Título de la página')
                            ->required($locale === config('site.default_locale'))
                            ->maxLength(255),
                        TextInput::make("subtitle.{$locale}")
                            ->label('Subtítulo')
                            ->helperText('Frase corta bajo el título. Opcional.')
                            ->maxLength(255),
                        ContentFields::richText("body.{$locale}", 'Contenido de la página'),
                    ]),
                ]),

            Section::make('Imagen principal')
                ->schema([
                    ContentFields::image(Page::COVER, 'Imagen de cabecera'),
                ]),

            Section::make('Buscadores (SEO)')
                ->description('Cómo se ve la página en Google. Si lo dejas vacío se usa el título.')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Página')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => Page::labels()[$state] ?? $state),
                ContentColumns::translated('title', 'Título'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última edición')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
