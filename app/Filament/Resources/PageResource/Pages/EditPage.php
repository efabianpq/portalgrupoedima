<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Page;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    /** Sin botón de borrar: las páginas del sitio no se eliminan. */
    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        /** @var Page $page */
        $page = $this->getRecord();

        return 'Página: '.$page->label();
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Cambios guardados';
    }
}
