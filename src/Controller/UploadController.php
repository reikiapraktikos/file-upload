<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\ArchiveFormat;
use App\Filesystem\FilesystemFileFactoryInterface;
use App\Service\UsageHandler;
use App\Validator\FileValidatorInterface;
use DateTimeImmutable;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

final class UploadController extends AbstractController
{
    public function __construct(
        private FilesystemFileFactoryInterface $filesystemFileFactory,
        private FileValidatorInterface $fileValidator,
        private FilesystemOperator $localStorage,
        private UsageHandler $usageHandler
    ) {
    }

    /** @throws FilesystemException */
    #[Route('/upload', 'upload')]
    public function __invoke(Request $request): StreamedResponse
    {
        $this->usageHandler->handle($request->getClientIp(), new DateTimeImmutable());
        $format = strtolower($request->request->get('archive_format', ArchiveFormat::ZIP->value));
        $files = $request->files->all();
        $this->fileValidator->validateMany(...$files);
        $filesystemFile = $this->filesystemFileFactory->create($format, ...$files);
        $stream = $this->localStorage->readStream($filesystemFile->getName());
        $request->attributes->set('fileName', $filesystemFile->getName());

        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
            fclose($stream);
        }, Response::HTTP_OK, [
            'Content-Type' => $filesystemFile->getMimeType(),
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filesystemFile->getName()),
            'Content-Length' => $filesystemFile->getSize(),
        ]);
    }
}
