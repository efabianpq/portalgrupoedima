<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Filament\Support\ContentColumns;
use App\Filament\Support\ContentFields;
use App\Filament\Support\TranslatableTabs;
use App\Models\TeamMember;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Contenido del sitio';

    protected static ?string $navigationLabel = 'Equipo';

    protected static ?string $modelLabel = 'integrante del equipo';

    protected static ?string $pluralModelLabel = 'integrantes del equipo';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Datos de la persona')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre completo')
                        ->helperText('No se traduce: es un nombre propio.')
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make('Cargo y biografía')
                ->description('El cargo y la biografía sí cambian según el idioma.')
                ->schema([
                    TranslatableTabs::make(fn (string $locale) => [
                        TextInput::make("role.{$locale}")
                            ->label('Cargo')
                            ->maxLength(255),
                        Textarea::make("bio.{$locale}")
                            ->label('Biografía')
                            ->rows(5)
                            ->maxLength(2000),
                    ]),
                ]),

            Section::make('Foto')
                ->schema([
                    ContentFields::image(TeamMember::PHOTO, 'Foto de la persona'),
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
                ContentColumns::image(TeamMember::PHOTO, 'Foto'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                ContentColumns::translated('role', 'Cargo'),
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
            ->emptyStateHeading('Todavía no hay integrantes')
            ->emptyStateDescription('Agrega a la primera persona del equipo con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
