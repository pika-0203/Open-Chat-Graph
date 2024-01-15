<?php

namespace Shadow;

/**
 * Class JsonStorage
 *
 * Provides functionality to initialize, copy properties to an object, and update a JSON file.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class JsonStorage implements JsonStorageInterface
{
    public string $filePath;
    public object $instance;
    public string $className;

    public function __construct(object $instance, ?string $jsonFilePath = null)
    {
        $this->instance = $instance;
        $this->className = get_class($instance);

        if ($this->className === \App\Config\ConfigJson::class) {
            $jsonFilePath = CONFIG_JSON_FILE_PATH;
        }

        if ($jsonFilePath === null) {
            $fileName = substr($this->className, strrpos($this->className, '\\') + 1);
            $this->filePath = JSON_STORAGE_DIR . '/' . $fileName . '.json';
        } else {
            $this->filePath = $jsonFilePath;
        }

        $this->mapToInstanceFromArray($this->loadJsonFile());
    }

    protected function loadJsonFile(): array
    {
        if (!file_exists($this->filePath)) {
            throw new \RuntimeException('JSON file does not exist: ' . $this->filePath);
        }

        $jsonData = file_get_contents($this->filePath);

        $array = json_decode($jsonData, true);
        if (!is_array($array)) {
            throw new \RuntimeException('Failed to load JSON file: JSON is not an object ' . $this->filePath);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to load JSON file: ' . $this->filePath);
        }

        return $array;
    }

    /**
     * Map properties from the storage array to the class's instance.
     */
    protected function mapToInstanceFromArray(array $array): void
    {
        $isStdClass = in_array('stdClass', class_parents($this->className));
        
        foreach ($array as $key => $value) {
            if (!$isStdClass && !property_exists($this->instance, $key)) {
                new \RuntimeException("Property '{$key}' does not exist on '{$this->className}': " . $this->filePath);
            }

            $this->instance->$key = $value;
        }
    }

    public function updateJsonFile(): void
    {
        $json = json_encode($this->instance);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode JSON data.');
        }

        $this->writeTextFileWithExclusiveLock($this->filePath, $json);
    }

    /**
     * Writes text to a file with an exclusive lock, ensuring atomicity of the write operation.
     *
     * @param string $filePath The path to the file that needs to be written to.
     * @param string $newContent The content to be written to the file.
     * @throws \RuntimeException If the file cannot be opened, or the lock cannot be acquired.
     */
    protected function writeTextFileWithExclusiveLock(string $filePath, string $newContent): void
    {
        $mode = $newContent === null ? 'r' : 'w'; // Use 'r' for reading, 'w' for writing

        // Open the file for reading or writing
        $fileHandle = fopen($filePath, $mode);

        if (!$fileHandle) {
            throw new \RuntimeException("Failed to open the file: $filePath");
        }

        try {
            if (flock($fileHandle, LOCK_EX)) {
                // If new content is provided, write it and return null
                ftruncate($fileHandle, 0); // Clear the file
                fwrite($fileHandle, $newContent);
                fflush($fileHandle);
            } else {
                throw new \RuntimeException('Failed to acquire an exclusive lock.');
            }
        } finally {
            fclose($fileHandle); // Always close the file handle, even on exceptions
        }
    }
}
