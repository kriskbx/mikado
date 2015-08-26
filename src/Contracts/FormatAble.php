<?php

namespace kriskbx\mikado\Contracts;

interface FormatAble
{
    /**
     * Has Property?
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty($name);

    /**
     * Get Property.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getProperty($name);

    /**
     * Set Property.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function setProperty($name, $value);

    /**
     * Unset Property.
     *
     * @param string $name
     *
     * @return bool
     */
    public function unsetProperty($name);

    /**
     * Resolve punctuation format and return path.
     *
     * @param string $path
     *
     * @return string
     */
    public function resolveArrayPath($path);

    /**
     * Unset null properties and keys.
     */
    public function unsetNull();

    /**
     * Get object.
     *
     * @return array|object
     */
    public function getObject();
}
