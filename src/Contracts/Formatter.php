<?php

namespace kriskbx\mikado\Contracts;

interface Formatter
{
    /**
     * Format the given object or array of objects.
     *
     * @param FormatAble $data
     *
     * @return FormatAble
     */
    public function format($data);
}
