<?php

namespace kriskbx\mikado\Formatters;

use kriskbx\mikado\Contracts\FormatAble;

/**
 * Class NullFormatter. Does no formatting at all. This is intended to test the abstract base class.
 */
class NullFormatter extends Formatter
{
    /**
     * Format the given object or array of objects.
     *
     * @param FormatAble $data
     *
     * @return FormatAble
     */
    public function format($data)
    {
        return $data;
    }
}
