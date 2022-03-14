<?php

declare(strict_types=1);

namespace App\Tests\Unit\Filesystem;

use App\Filesystem\FilesystemFileFactory;
use League\Flysystem\Filesystem;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FilesystemFileFactoryTest extends TestCase
{
    use PHPMock;

    private string $directory;
    private MockObject|Filesystem $localStorage;
    private MockObject|Filesystem $tmpStorage;
    private FilesystemFileFactory $filesystemFileFactory;

    public function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/';
        $this->localStorage = $this->createMock(Filesystem::class);
        $this->tmpStorage = $this->createMock(Filesystem::class);
        $this->filesystemFileFactory = new FilesystemFileFactory(
            $this->directory,
            $this->localStorage,
            $this->tmpStorage
        );
    }

    /** @dataProvider provideCreate */
    public function testCreate(
        string $archiveFormat,
        int $archivedFileSize,
        string $archivedFileMimeType,
        string $uniqid
    ): void {
        $archivedFileName = sprintf('%s.%s', $uniqid, $archiveFormat);
        $archivedFilePath = sprintf('%s%s', $this->directory, $archivedFileName);
        $uploadedFile = new UploadedFile(
            (string) tempnam($this->directory, ''),
            '',
            null,
            null,
            true
        );
        $this
            ->getFunctionMock('App\Filesystem', 'uniqid')
            ->expects(self::once())
            ->willReturn($uniqid);
        $this
            ->tmpStorage
            ->expects(self::once())
            ->method('readStream')
            ->with($uploadedFile->getFilename())
            ->willReturn($this->getStreamWithContents(''));
        $this
            ->localStorage
            ->expects(self::once())
            ->method('fileSize')
            ->with($archivedFileName)
            ->willReturn($archivedFileSize);
        $this
            ->localStorage
            ->expects(self::once())
            ->method('mimeType')
            ->with($archivedFileName)
            ->willReturn($archivedFileMimeType);

        $createdFile = $this->filesystemFileFactory->create($archiveFormat, $uploadedFile);

        $this->assertSame($archivedFileName, $createdFile->getName());
        $this->assertSame($archivedFileSize, $createdFile->getSize());
        $this->assertSame($archivedFileMimeType, $createdFile->getMimeType());
        $this->assertSame($archivedFilePath, $createdFile->getPath());
    }

    /**
     * @param string $contents
     * @return resource
     */
    private function getStreamWithContents(string $contents)
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $contents);
        rewind($stream);

        return $stream;
    }

    public function provideCreate(): array
    {
        return [
            [
                'archiveFormat' => 'zip',
                'archivedFileSize' => 67508,
                'archivedFileMimeType' => 'application/zip',
                'uniqid' => '622ec364e0e9e',
            ]
        ];
    }
}
