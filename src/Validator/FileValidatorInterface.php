<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileValidatorInterface
{
    public function validate(UploadedFile $file): void;
    public function validateMany(UploadedFile ...$files): void;
}
