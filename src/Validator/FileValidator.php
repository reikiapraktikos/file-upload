<?php

declare(strict_types=1);

namespace App\Validator;

use App\Enum\ExceptionMessage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class FileValidator implements FileValidatorInterface
{
    /** @var Constraint[] */
    private array $constrains;

    public function __construct(private ValidatorInterface $validator)
    {
        $this->constrains = [
            new NotBlank([
                'message' => ExceptionMessage::EMPTY_FILE->value,
            ]),
            new File([
                'maxSize' => '1M',
            ])
        ];
    }

    public function validateMany(UploadedFile ...$files): void
    {
        if ($files === []) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, ExceptionMessage::NO_FILES->value);
        }

        foreach ($files as $file) {
            $this->validate($file);
        }
    }

    public function validate(UploadedFile $file): void
    {
        $violations = $this->validator->validate($file, $this->constrains);

        if ($violations->count() > 0) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $violations[0]->getMessage());
        }
    }
}
