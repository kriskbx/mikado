<?php

namespace spec\kriskbx\mikado\Formatters;

use PhpSpec\ObjectBehavior;

class NullFormatterSpec extends ObjectBehavior
{
    protected $regex = [
        '/^acf_field_abc_([0-9]*)_acf_field_xyz$/i' => 'regex',
        '/^acf_field_def_([0-9]*)_acf_field_uvw$/i' => 'regex',
        'test' => 'not-a-regex',
    ];

    public function it_is_initializable()
    {
        $this->shouldHaveType('kriskbx\mikado\Formatters\NullFormatter');
    }

    public function it_can_filter_regex_keys_in_an_array()
    {
        $this->regex($this->regex)->shouldReturn([
            '/^acf_field_abc_([0-9]*)_acf_field_xyz$/i' => 'regex',
            '/^acf_field_def_([0-9]*)_acf_field_uvw$/i' => 'regex',
        ]);
    }
}
