<?php

namespace App\Support;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;

/**
 * Conversiones de imagen compartidas por todo el contenido del sitio.
 *
 * Se generan con `nonQueued()` a propósito: el hosting de Hostinger es
 * compartido y no tiene workers de colas, así que las miniaturas deben crearse
 * en el momento de subir la imagen, no en segundo plano.
 */
final class ImageConversions
{
    /** Miniatura cuadrada para las tablas del panel. */
    public const THUMB = 'miniatura';

    /** Tamaño servido en el sitio público. */
    public const WEB = 'web';

    /**
     * @param  HasMedia  $model  Un modelo que además use el trait InteractsWithMedia.
     */
    public static function register(HasMedia $model): void
    {
        $model->addMediaConversion(self::THUMB)
            ->fit(Fit::Crop, 400, 400)
            ->nonQueued();

        $model->addMediaConversion(self::WEB)
            ->fit(Fit::Max, 1600, 1200)
            ->nonQueued();
    }
}
