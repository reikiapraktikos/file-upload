<?php

declare(strict_types=1);

namespace App\Filesystem;

final class FilesystemFile implements FilesystemFileInterface
{
    public function __construct(private string $name, private string $path, private int $size, private string $mimeType)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
