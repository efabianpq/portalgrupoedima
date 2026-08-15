<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Support\ContentColumns;
use App\Filament\Support\ContentFields;
use App\Filament\Support\TranslatableTabs;
use App\Models\Post;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    /** En el panel los registros se identifican por ID, no por el slug traducible. */
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Contenido del sitio';

    protected static ?string $navigationLabel = 'Blog y noticias';

    protected static ?string $modelLabel = 'entrada';

    protected static ?string $pluralModelLabel = 'entradas';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Textos de la entrada')
                ->description('Escribe el contenido en los dos idiomas. Si dejas el inglés vacío, la entrada no aparecerá en la versión en inglés del sitio.')
                ->schema([
                    TranslatableTabs::make(fn (string $locale) => [
                        ContentFields::title($locale, 'Título'),
                        ContentFields::slug($locale, Post::class),
                        TextInput::make("category.{$locale}")
                            ->label('Categoría')
                            ->helperText('Por ejemplo: Noticias, Eventos, Gobierno de datos.')
                            ->maxLength(255),
                        Textarea::make("excerpt.{$locale}")
                            ->label('Resumen breve')
                            ->helperText('Una o dos frases. Es lo que se ve en la lista del blog.')
                            ->rows(3)
                            ->maxLength(500),
                        ContentFields::richText("body.{$locale}", 'Contenido de la entrada'),
                    ]),
                ]),

            Section::make('Imagen')
                ->schema([
                    ContentFields::image(Post::COVER, 'Imagen de portada'),
                ]),

            Section::make('Publicación')
                ->schema([
                    DateTimePicker::make('published_at')
                        ->label('Fecha de publicación')
                        ->helperText('Si pones una fecha futura, la entrada aparecerá sola ese día.')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->default(now()),
                    ContentFields::publishToggle(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ContentColumns::image(Post::COVER, 'Portada'),
                ContentColumns::translated('title', 'Título'),
                ContentColumns::translated('category', 'Categoría')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y')
                    ->sortable(),
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
            ->emptyStateHeading('Todavía no hay entradas')
            ->emptyStateDescription('Escribe la primera entrada del blog con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
