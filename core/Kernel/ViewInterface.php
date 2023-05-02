<?php

namespace Shadow\Kernel;

interface ViewInterface
{
    /**
     * Display the cached content.
     */
    public function render(): void;

    /**
     * Gets rendered template as a string.
     *
     * @return string The rendered template as a string.
     */
    public function getRenderChahe(): string;
}
