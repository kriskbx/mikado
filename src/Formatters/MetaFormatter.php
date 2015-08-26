<?php

namespace kriskbx\mikado\Formatters;

use kriskbx\mikado\Contracts\FormatAble;

/**
 * Class MetaFormatter. Formats jgrossi/corcel and WordPress specific post_meta. Don't use this elsewhere.
 */
class MetaFormatter extends Formatter
{
    /**
     * @var array
     */
    protected $metaFields;

    /**
     * @var array
     */
    protected $regex;

    /**
     * MetaFormatter constructor.
     *
     * @param array $metaFields [ 'keyInData' => 'keyInOutput',
     *                          '/^keyInData.([0-9]*).subKeyInData$/i' => 'keyInOutput.$1.subKeyInOutput' ]
     */
    public function __construct($metaFields)
    {
        $this->metaFields = $metaFields;
        $this->regex = $this->regex($metaFields);
    }

    /**
     * Format the meta fields from the given data.
     *
     * @param FormatAble $data
     *
     * @return FormatAble
     */
    public function format($data)
    {
        if (!$data->hasProperty('meta')) {
            return $data;
        }

        // Loop through the meta arrays/objects/whatever
        foreach ($data->getProperty('meta') as $meta) {
            $regex = false;

            // Check if the meta array is valid
            if (!$this->metaKeyExists($meta) && !($regex = $this->metaKeyIsRegex($meta))) {
                continue;
            }

            // Get the property
            if ($regex) {
                $property = $this->getRegexProperty($regex, $regex['value']);
            } else {
                $property = $this->metaFields[$meta['meta_key']];
            }

            // Get the value
            $value = $this->getUnserializedValue($meta);

            // Set it
            $data->setProperty($property, $value);
        }

        $data->unsetProperty('meta');

        return $data;
    }

    /**
     * Meta key exists?
     *
     * @param array $meta
     *
     * @return bool
     */
    protected function metaKeyExists($meta)
    {
        if (!isset($meta['meta_key'])) {
            return false;
        }

        return array_key_exists($meta['meta_key'], $this->metaFields);
    }

    /**
     * Meta key is regex?
     *
     * @param array $meta
     *
     * @return array|bool
     */
    protected function metaKeyIsRegex($meta)
    {
        if (!isset($meta['meta_key'])) {
            return false;
        }

        return $this->pregMatchArray($meta['meta_key'], $this->regex);
    }

    /**
     * Get unserialized value.
     *
     * @param array $meta
     *
     * @return mixed
     */
    protected function getUnserializedValue($meta)
    {
        $value = $meta['meta_value'];

        if ($unserializedValue = @unserialize($value)) {
            $value = $unserializedValue;
        }

        return $value;
    }
}
