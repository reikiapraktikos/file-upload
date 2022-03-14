<?php

declare(strict_types=1);

namespace App\Enum;

enum ExceptionMessage: string
{
    case EMPTY_FILE = 'Empty file provided';
    case NO_FILES = 'No files have been uploaded';
    case UNSUPPORTED_ARCHIVE_FORMAT = 'Unsupported archive format';
    case DEFAULT = 'Something went wrong';
}
