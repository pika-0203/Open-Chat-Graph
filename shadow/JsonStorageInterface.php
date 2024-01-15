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
interface JsonStorageInterface
{
    /**
     * Initializes the JSON storage.
     *
     * @param object $instance The instance to be stored.
     * @param ?string $jsonFilePath The path to the JSON file to store the data in. If `null`, the JSON file will be stored in the default JSON storage directory.
     *
     * @throws \RuntimeException If the JSON file does not exist or cannot be loaded.
     */
    public function __construct(object $instance, ?string $jsonFilePath = null);

    /**
     * Updates the JSON file with the values from the class instance's properties.
     *
     * @throws \RuntimeException If there is a failure to write the data.
     */
    public function updateJsonFile(): void;
}
