<?php

namespace kriskbx\mikado;

use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use kriskbx\mikado\Contracts\FormatAble;
use kriskbx\mikado\Contracts\Formatter as FormatterContract;
use kriskbx\mikado\Data\FormatAbleObject;

class Manager
{
    /**
     * @var FormatterContract[]
     */
    protected $formatters = [];

    /**
     * Add Formatter.
     *
     * @param FormatterContract $formatter
     *
     * @return $this
     */
    public function add(FormatterContract $formatter)
    {
        $this->formatters[] = $formatter;

        return $this;
    }

    /**
     * Format the given data.
     *
     * @param array|object $data
     * @param bool         $multiple
     *
     * @return array|object
     */
    public function format($data, $multiple = false)
    {
        if ($multiple === true || $data instanceof Collection) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->runFormatters($value);
            }
        } else {
            $data = $this->runFormatters($data);
        }

        return $data;
    }

    /**
     * Make an object formatAble.
     *
     * @param object|array $object
     *
     * @return FormatAble
     *
     * @throws InvalidArgumentException
     */
    public static function formatAble($object)
    {
        if (!is_object($object) && !is_array($object)) {
            throw new InvalidArgumentException('The given argument is not an object/array.');
        }

        if ($object instanceof FormatAble) {
            return $object;
        }

        return new FormatAbleObject($object);
    }

    /**
     * Run the Formatters on the given data.
     *
     * @param array|object $data
     *
     * @return FormatAble|object
     */
    protected function runFormatters($data)
    {
        $data = self::formatAble($data);

        foreach ($this->formatters as $formatter) {
            $data = $formatter->format($data);
        }

        return $data->getObject();
    }
}
