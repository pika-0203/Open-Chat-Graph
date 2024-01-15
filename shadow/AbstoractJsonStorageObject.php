<?php

namespace Shadow;

/**
 * Abstract base class providing mechanisms to associate objects with JSON storage persistence.
 * It leverages a JsonStorage instance to synchronize object properties with a JSON file.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
abstract class AbstoractJsonStorageObject extends \stdClass
{
    /**
     * @var JsonStorageInterface Reference to the JsonStorage instance for handling JSON data operations.
     */
    protected JsonStorageInterface $jsonStorageInstance;

    /**
     * Constructor initializes the JsonStorage instance and copies properties from the corresponding JSON file into this object.
     */
    public function __construct()
    {
        $this->jsonStorageInstance = new JsonStorage($this);
    }

    /**
     * Updates the JSON file with the values from the class properties.
     *
     * @throws \RuntimeException If there is a failure to write the data.
     */
    public function update()
    {
        $this->jsonStorageInstance->updateJsonFile();
    }
}
