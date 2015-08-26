<?php

namespace kriskbx\mikado\Formatters;

use kriskbx\mikado\Contracts\FormatAble;

/**
 * Class FilterFormatter. Filter the given data by the given fields.
 */
class FilterFormatter extends Formatter
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * FilterFormatter Constructor.
     *
     * @param array $fields
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Format the given object or array of objects.
     *
     * @param FormatAble $data
     *
     * @return FormatAble
     */
    public function format($data)
    {
        $data->loopThrough(function ($key, $value) use ($data) {
            if (!in_array($key, $this->fields)) {
                $data->setProperty($key, null);
            }
        });

        $data->unsetNull();

        return $data;
    }
}
