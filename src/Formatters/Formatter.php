<?php

namespace kriskbx\mikado\Formatters;

use InvalidArgumentException;
use kriskbx\mikado\Contracts\FormatAble;
use kriskbx\mikado\Contracts\Formatter as FormatterContract;
use kriskbx\mikado\Data\FormatAbleObject;

/**
 * Class Formatter. Base formatter class. Shares useful methods across other formatters.
 */
abstract class Formatter implements FormatterContract
{
    /**
     * Make an object formatAble.
     *
     * @param object|array $object
     *
     * @return object
     */
    protected function formatAble(&$object)
    {
        if (!is_object($object) || is_array($object)) {
            throw new InvalidArgumentException('The given argument is not an object/array.');
        }

        if (!is_object($object) && $object instanceof FormatAble) {
            return;
        }

        $object = new FormatAbleObject(clone($object));
    }

    /**
     * Filter an array where the key is a regex string.
     *
     * @param array $array
     *
     * @return array
     */
    public function regex($array = [])
    {
        $filtered = [];
        foreach ($array as $key => $value) {
            if ($this->isRegex($key)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Is the given string a regex?
     *
     * @param $string
     *
     * @return bool
     */
    protected function isRegex($string)
    {
        try {
            preg_match($string, '');
        } catch(\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get regex property.
     *
     * @param array  $matches
     * @param string $value
     *
     * @return string
     */
    protected function getRegexProperty($matches, $value)
    {
        foreach ($matches as $index => $match) {
            if (is_numeric($index) && $index != 0) {
                $value = str_replace('$'.$index, $match, $value);
            }
        }

        return $value;
    }

    /**
     * Applies the regular expressions stored in the keys of the given array to the given input string.
     * Returns the first match including the matching value.
     *
     * @param string $input
     * @param array  $rules
     * @param int    $flags
     *
     * @return array|bool
     */
    protected function pregMatchArray($input, $rules, $flags = 0)
    {
        if (!is_array($rules)) {
            return false;
        }

        foreach ($rules as $rule => $value) {
            if (preg_match($rule, $input, $matches, $flags)) {
                return array_merge($matches, ['value' => $value]);
            }
        }

        return false;
    }
}
