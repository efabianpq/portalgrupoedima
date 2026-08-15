<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('marcar_leido')
                ->label('Marcar como leído')
                ->icon('heroicon-o-envelope-open')
                ->color('success')
                ->visible(fn (ContactMessage $record) => ! $record->isRead())
                ->action(fn (ContactMessage $record) => $record->markAsRead()),
        ];
    }
}
