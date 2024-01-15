<?php

namespace Shadow\Kernel;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
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
    public function getRenderCache(): string;

    /**
     * Sets rendered template as a string.
     *
     * @param string            $viewTemplateFile Path to the template file.
     * @param array|object|null $valuesArray      Optional associative array of values to pass to the template, 
     *                                            Keys starting with "_" will not be sanitized.
     * @return static
     * 
     * @throws \InvalidArgumentException          If passed invalid array or template file.
     */
    public function set(string $viewTemplateFile, ?array $valuesArray = null): static;

    /**
     * Render a template file with optional values.
     *
     * @param string|ViewInterface $viewTemplateFile Path to the template file.
     * @param array|null $valuesArray        [optional] associative array of values to pass to the template, 
     *                                       Keys starting with "_" will not be sanitized.
     * @return static
     * 
     * @throws \InvalidArgumentException     If passed invalid array.
     */
    public function make(string|ViewInterface $viewTemplateFile, array|null $valuesArray = null): static;
}
