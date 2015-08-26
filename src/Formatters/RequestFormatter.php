<?php


namespace kriskbx\mikado\Formatters;

use Illuminate\Http\Request;

/**
 * Class RequestFormatter. Works like the FilterFormatter, but it takes an Illuminate\Http\Request object and filters
 * the given fields by the given request.
 * @package kriskbx\mikado\Formatters
 */
class RequestFormatter extends FilterFormatter
{

    /**
     * RequestFormatter Constructor.
     *
     * @param array $fields
     * @param Request $request
     * @param string $key
     */
    public function __construct($fields, Request $request, $key)
    {
        if (is_array($requestFields = $request->input($key))) {
            foreach ($requestFields as $field) {
                if (in_array($field, $fields))
                    $this->fields[] = $field;
            }
        } else {
            parent::__construct($fields);
        }
    }

}