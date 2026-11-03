<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;

class GalleryController extends AdminGalleryController
{
    /**
     * Guru memakai route & view prefix sendiri (layout guru).
     */
    protected string $routePrefix = 'guru';

    protected string $viewPrefix = 'guru.gallery';
}
