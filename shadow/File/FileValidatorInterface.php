<?php

namespace Shadow\File;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
interface FileValidatorInterface
{
    /**
     * Validate uploaded file data based on the allowed mime types and maximum file size.
     *
     * @param string $fileData             The file data as a string to be validated.
     * @param int    $maxFileSize          The maximum file size in kilobytes (KB).
     * @param array  $allowedMimeTypeArray An associative array of allowed MIME types and their corresponding file extensions.  
     * * **Example:** `['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp']`
     * 
     * @return string                      The value of the matched MINE Type.
     * 
     * @throws \RuntimeException           If there is a failure in writing the file data to a temporary file.
     * 
     * @throws Shared\Exceptions\ValidationException 
     *                           * Error codes:  
     *                           3001 - File too large.  
     *                           3002 - File extension not allowed.  
     *                           3003 - File type does not match.  
     */
    public function validate(string $fileData, int $maxFileSize, array $allowedMimeTypeArray): mixed;
}
