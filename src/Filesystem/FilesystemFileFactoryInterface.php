<?php

declare(strict_types=1);

namespace App\Filesystem;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FilesystemFileFactoryInterface
{
    public function create(string $format, UploadedFile ...$files): FilesystemFileInterface;
}
