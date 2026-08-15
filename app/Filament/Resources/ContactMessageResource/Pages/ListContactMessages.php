<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    /** Sin botón de "crear": los mensajes llegan desde el sitio web. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
