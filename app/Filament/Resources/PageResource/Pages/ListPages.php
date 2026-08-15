<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    /** Sin botón de "crear": las páginas del sitio son un conjunto fijo. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
