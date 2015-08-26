<?php

namespace spec\kriskbx\mikado\Formatters;

use DummyData;
use Illuminate\Http\Request;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestFormatterSpec extends ObjectBehavior
{
    protected $requestFields = [
        'post_title',
        'post_content'
    ];

    protected $fields = [
        'post_title',
        'post_content',
        'post_excerpt',
        'post_name'
    ];

    protected $dummyData;

    function let(Request $request) {
        $this->dummyData = new DummyData();
        $request->input('fields')->shouldBeCalled()->willReturn($this->requestFields);
        $this->beConstructedWith($this->fields, $request, 'fields');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('kriskbx\mikado\Formatters\RequestFormatter');
    }

    function it_formats_data() {
        $formatted = $this->format($this->dummyData->getPost(true))->toArray();

        $formatted->shouldHaveKey('post_title');
        $formatted->shouldHaveKey('post_content');
        $formatted->shouldNotHaveKey('post_excerpt');
        $formatted->shouldNotHaveKey('post_name');
    }
}
