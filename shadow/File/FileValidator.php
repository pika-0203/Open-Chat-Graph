<?php

declare(strict_types=1);

namespace Shadow\File;

use Shared\Exceptions\ValidationException;

/**
 * Class FileValidator
 * 
 * Validates uploaded files based on the allowed mime types and maximum file size.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class FileValidator implements FileValidatorInterface
{
    public function validate(string $fileData, int $maxFileSize, array $allowedMimeTypeArray): mixed
    {
        $tmpFilePath = tempnam(sys_get_temp_dir(), 'shadow-file-');

        if (file_put_contents($tmpFilePath, $fileData) === false) {
            throw new \RuntimeException('Failed to write file data to temp file.');
        }

        try {
            if (filesize($tmpFilePath) > $maxFileSize * 1024) {
                throw new ValidationException("File too large: '{$tmpFilePath}'", 3001);
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($tmpFilePath);

            if (!isset($allowedMimeTypeArray[$mimeType])) {
                throw new ValidationException("File extension not allowed: '{$tmpFilePath}'", 3002);
            }
        } finally {
            unlink($tmpFilePath);
        }

        return $allowedMimeTypeArray[$mimeType];
    }
}
