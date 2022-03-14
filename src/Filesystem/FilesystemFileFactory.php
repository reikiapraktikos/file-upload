<?php

declare(strict_types=1);

namespace App\Filesystem;

use App\Enum\ArchiveFormat;
use App\Enum\ExceptionMessage;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class FilesystemFileFactory implements FilesystemFileFactoryInterface
{
    public function __construct(
        private string $fileDir,
        private FilesystemOperator $localStorage,
        private FilesystemOperator $tmpStorage
    ) {
    }

    /** @throws FilesystemException */
    public function create(string $format, UploadedFile ...$files): FilesystemFileInterface
    {
        $uniqueId = uniqid();
        $name = sprintf('%s.%s', $uniqueId, $format);
        $path = sprintf('%s%s', $this->fileDir, $name);
        $this->archive($format, $path, ...$files);
        $size = $this->localStorage->fileSize($name);
        $mimeType = $this->localStorage->mimeType($name);

        return new FilesystemFile($name, $path, $size, $mimeType);
    }

    /** @throws FilesystemException */
    private function archive(string $format, string $path, UploadedFile ...$files): void
    {
        $filesystem = $this->getArchiveFilesystem($format, $path);

        foreach ($files as $file) {
            $filesystem->writeStream(
                $file->getClientOriginalName(),
                $this->tmpStorage->readStream($file->getFilename())
            );
        }
    }

    private function getArchiveFilesystem(string $format, string $path): Filesystem
    {
        if ($format === ArchiveFormat::ZIP->value) {
            $filesystem = new Filesystem(new ZipArchiveAdapter(new FilesystemZipArchiveProvider($path)));
        } else {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                ExceptionMessage::UNSUPPORTED_ARCHIVE_FORMAT->value
            );
        }

        return $filesystem;
    }
}
