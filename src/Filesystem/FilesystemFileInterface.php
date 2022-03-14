<?php

declare(strict_types=1);

namespace App\Filesystem;

interface FilesystemFileInterface
{
    public function getName(): string;
    public function getPath(): string;
    public function getSize(): int;
    public function getMimeType(): string;
}
