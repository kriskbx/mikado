<?php

namespace kriskbx\mikado\Formatters;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use kriskbx\mikado\Contracts\FormatAble;

/**
 * Class RemapFormatter. Remaps/Renames properties.
 * @package kriskbx\mikado\Formatters
 */
class RemapFormatter extends Formatter
{
    /**
     * @var array
     */
    protected $rules;

    /**
     * RemapFormatter Constructor.
     *
     * @param array $rules
     */
    public function __construct($rules)
    {
        $this->rules = $rules;
    }

    /**
     * Format the given object or array of objects.
     *
     * @param FormatAble $data [ 'keyInData' => 'keyInOutput',
     *                          '/^keyInData.([0-9]*).subKeyInData$/i' => 'keyInOutput.$1.subKeyInOutput' ]
     *
     * @return FormatAble
     */
    public function format($data)
    {
        foreach ($this->rules as $oldKey => $newKey) {
            if ($this->isRegex($oldKey)) {
                $this->remapRecursively($data, $oldKey, $newKey);
            } else {
                $this->remap($data, $oldKey, $newKey);
            }
        }

        $data->unsetNull();

        return $data;
    }

    /**
     * Remap.
     *
     * @param FormatAble $data
     * @param string $newKey
     * @param string $oldKey
     */
    protected function remap(&$data, $oldKey, $newKey)
    {
        if ($newKey !== null && $newKey !== false) {
        $data->setProperty($newKey, $data->getProperty($oldKey));
    }

        $data->unsetProperty($oldKey);
    }

    /**
     * Remap properties recursively via regex.
     *
     * @param FormatAble $data
     * @param string $oldKey
     * @param string $newKey
     */
    protected function remapRecursively(&$data, $oldKey, $newKey)
    {
        $data->loopThrough(function ($index, $value) use ($data, $oldKey, $newKey) {
            $matches = $this->checkRegexRecursively($value, $oldKey, $index);
            foreach ($matches as $match) {
                $this->remap($data, $match[0], $this->getRegexProperty($match, $newKey));
            }
        });
    }

    /**
     * Loop through the given array|object and check the given regular expression on every single item.
     *
     * @param array $array
     * @param string $pattern
     * @param string $accessKey
     *
     * @return array
     */
    protected function checkRegexRecursively($array, $pattern, $accessKey = null)
    {
        $found = [];

        if (!is_array($array) && !is_object($array)) {
            return [];
        }

        if ($array instanceof Collection || $array instanceof Model)
            $array = $array->toArray();

        foreach ($array as $index => $value) {
            $newAccessKey = ($accessKey ? $accessKey . '.' : '') . $index;

            if (preg_match($pattern, $newAccessKey, $matches)) {
                $found[] = $matches;
            }

            $found = array_merge($found, $this->checkRegexRecursively($value, $pattern, $newAccessKey));
        }

        return $found;
    }
}
