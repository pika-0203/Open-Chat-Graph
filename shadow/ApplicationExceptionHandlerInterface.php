<?php

namespace Shadow;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
interface ApplicationExceptionHandlerInterface
{
    /**
     * Handles the specified \Throwable instance.
     *
     * @param \Throwable $e The \Throwable instance to handle.
     * @param string $className Throwed exception class name.
     */
    public static function handleException(\Throwable $e, string $className);
}
