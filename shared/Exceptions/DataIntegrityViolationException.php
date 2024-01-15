<?php

namespace Shared\Exceptions;

/**
 * Exception thrown when there is an error in the integrity of data, such as violating a unique constraint or foreign key constraint.
 */
class DataIntegrityViolationException extends \Exception
{
}