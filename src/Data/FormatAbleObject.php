<?php

namespace kriskbx\mikado\Data;

use ArrayAccess;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use kriskbx\mikado\Contracts\FormatAble;

class FormatAbleObject implements FormatAble, Jsonable, Arrayable
{
    /**
     * @var object
     */
    protected $object;

    /**
     * Constructor.
     *
     * @param object|array $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Has Property?
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty($name)
    {
        if (strstr($name, '.')) {
            $exists = false;
            @eval('$exists = @isset('.$this->resolveArrayPath($name).');');

            return $exists;
        }

        if ($this->isArray()) {
            return @isset($this->object[$name]);
        } else {
            return @isset($this->object->$name);
        }
    }

    /**
     * Get Property.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getProperty($name)
    {
        if (!$this->hasProperty($name)) {
            return;
        }

        if (strstr($name, '.')) {
            $value = false;
            @eval('$value = '.$this->resolveArrayPath($name).';');

            return $value;
        }

        if ($this->isArray()) {
            return $this->object[$name];
        } else {
            return $this->object->$name;
        }
    }

    /**
     * Set Property.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function setProperty($name, $value)
    {
        if (strstr($name, '.')) {
            $this->setPropertyByPath($name, $value);

            return true;
        }

        if ($this->isArray()) {
            $this->object[$name] = $value;
        } else {
            $this->object->$name = $value;
        }

        return true;
    }

    /**
     * Set a property by looping through.
     *
     * @param string $path
     */
    protected function setPropertyByPath($path, $value)
    {
        $path = explode('.', $path);
        $root = $path[0];
        unset($path[0]);

        $array = ($this->hasProperty($root) ? $this->getProperty($root) : []);
        $pointer = &$array;

        // Loop through the array path and set array parts
        foreach ($path as $part) {
            if (!isset($pointer[$part])) {
                $pointer[$part] = [];
            }

            if (!is_array($pointer[$part])) {
                $pointer[$part] = [];
            }

            $pointer = &$pointer[$part];
        }

        $pointer = $value;
        unset($pointer);

        $this->setProperty($root, $array);
    }

    /**
     * Unset Property.
     *
     * @param string $name
     *
     * @return bool
     */
    public function unsetProperty($name)
    {
        if (!$this->hasProperty($name)) {
            return false;
        }

        if (strstr($name, '.')) {
            @eval('unset('.$this->resolveArrayPath($name).');');

            return true;
        }

        if ($this->isArray()) {
            unset($this->object[$name]);
        } else {
            unset($this->object->$name);
        }

        return true;
    }

    /**
     * Resolve punctuation format and return path.
     *
     * @param string $path
     *
     * @return string
     */
    public function resolveArrayPath($path)
    {
        $path = explode('.', $this->escape($path));
        $root = $path[0];
        unset($path[0]);

        $arrayPath = '["'.implode('"]["', $path).'"]';

        $arrayBracket = '->';
        $arrayBracketEnd = '';

        if ($this->isArray()) {
            $arrayBracket = '["';
            $arrayBracketEnd = '"]';
        }

        return '$this->object'.$arrayBracket.$root.$arrayBracketEnd.$arrayPath;
    }

    /**
     * Unset null properties and keys.
     */
    public function unsetNull()
    {
        $this->object = $this->unsetNullRecursively($this->object, $this->getReference());
    }

    /**
     * Loop through the stored data.
     *
     * @param Closure $callable
     */
    public function loopThrough(Closure $callable)
    {
        $reference = $this->getReference();

        if (is_null($reference)) {
            $reference = $this->object;
        }

        $this->loop($reference, $callable);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->object instanceof Arrayable) {
            return $this->object->toArray();
        }

        return (array) $this->object;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        if ($this->object instanceof Jsonable) {
            return $this->object->toJson();
        }

        return json_encode($this->object);
    }

    /**
     * Get object.
     *
     * @return array|object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Loop through the given data and call the given function.
     *
     * @param array|object $data
     * @param Closure      $callable
     */
    protected function loop($data, Closure $callable)
    {
        if (!is_array($data) && !is_object($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            call_user_func_array($callable, [$key, $value]);
        }
    }

    /**
     * Unset null properties and keys of the given data.
     *
     * @param array|object $data
     *
     * @return array|object
     */
    protected function unsetNullRecursively($data, $reference = null)
    {
        if (is_null($reference)) {
            $reference = $data;
        }

        $this->loop($reference, function ($key, $value) use (&$data) {
            if (is_array($data)) {
                if (is_null($value)) {
                    unset($data[$key]);
                } else {
                    $data[$key] = $this->unsetNullRecursively($value);
                }
            }

            if (is_object($data)) {
                if (is_null($value)) {
                    unset($data->$key);
                } else {
                    $data->$key = $this->unsetNullRecursively($value);
                }
            }
        });

        return $data;
    }

    /**
     * Is array?
     *
     * @return bool
     */
    protected function isArray()
    {
        return (is_array($this->object) || $this->object instanceof ArrayAccess);
    }

    /**
     * Get the reference for looping.
     *
     * @return array|null
     */
    protected function getReference()
    {
        if ($this->object instanceof Model) {
            return $this->object->toArray();
        }

        return;
    }

    /**
     * Prevent injections of bad code.
     *
     * @param string $path
     *
     * @return string
     */
    protected function escape($path)
    {
        $replace = ['<?', '?>', '"', "'", '(', ')', ';', '='];

        return str_ireplace($replace, '', $path);
    }
}
