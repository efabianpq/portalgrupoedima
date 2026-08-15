<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Buzón de mensajes del formulario de contacto.
 *
 * Es de sólo lectura a propósito: son datos que escribieron los visitantes y
 * no deben poder alterarse desde el panel. Lo único que se puede cambiar es la
 * marca de "leído".
 */
class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $navigationLabel = 'Mensajes de contacto';

    protected static ?string $modelLabel = 'mensaje';

    protected static ?string $pluralModelLabel = 'mensajes';

    protected static ?int $navigationSort = 1;

    /** Los mensajes llegan desde el sitio web, no se crean a mano. */
    public static function canCreate(): bool
    {
        return false;
    }

    /** El contenido del mensaje no se edita. */
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    /** Cantidad de mensajes sin leer, junto al menú. */
    public static function getNavigationBadge(): ?string
    {
        $sinLeer = ContactMessage::query()->unread()->count();

        return $sinLeer > 0 ? (string) $sinLeer : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Mensaje recibido')
                ->schema([
                    TextEntry::make('name')->label('Nombre'),
                    TextEntry::make('email')->label('Correo electrónico')->copyable(),
                    TextEntry::make('phone')->label('Teléfono')->placeholder('No indicó teléfono'),
                    TextEntry::make('locale')
                        ->label('Idioma en que escribió')
                        ->formatStateUsing(fn (?string $state) => config("site.locales.{$state}", $state)),
                    TextEntry::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i'),
                    TextEntry::make('read_at')
                        ->label('Leído')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('Sin leer'),
                    TextEntry::make('message')
                        ->label('Mensaje')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\IconColumn::make('read_at')
                    ->label('Leído')
                    ->boolean()
                    ->getStateUsing(fn (ContactMessage $record) => $record->isRead())
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-s-envelope')
                    ->trueColor('gray')
                    ->falseColor('warning')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(60)
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('sin_leer')
                    ->label('Solo sin leer')
                    ->query(fn ($query) => $query->unread()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver'),
                Tables\Actions\Action::make('marcar_leido')
                    ->label('Marcar como leído')
                    ->icon('heroicon-o-envelope-open')
                    ->color('success')
                    ->visible(fn (ContactMessage $record) => ! $record->isRead())
                    ->action(fn (ContactMessage $record) => $record->markAsRead()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('marcar_leidos')
                        ->label('Marcar como leídos')
                        ->icon('heroicon-o-envelope-open')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each->markAsRead()),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay mensajes todavía')
            ->emptyStateDescription('Aquí aparecerán los mensajes que envíen desde el formulario de contacto del sitio.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
