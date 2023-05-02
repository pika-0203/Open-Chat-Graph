<?php

namespace Shadow\Exceptions;

/**
 * Exception thrown when a client has made too many requests within a certain amount of time.
 */
class ThrottleRequestsException extends \RuntimeException
{
}