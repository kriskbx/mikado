<?php

namespace spec\kriskbx\mikado\Formatters;

use PhpSpec\ObjectBehavior;
use DummyData;

class FilterFormatterSpec extends ObjectBehavior
{
    protected $dummyData;

    protected $filter = [
        'testProperty',
    ];

    protected $filterWordpress = [
        'post_title', 'post_content',
    ];

    public function let()
    {
        $this->dummyData = new DummyData();
        $this->beConstructedWith($this->filter);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('kriskbx\mikado\Formatters\FilterFormatter');
    }

    public function it_formats_data()
    {
        $this->format($this->dummyData->getStdClass(true))->toArray()->shouldReturn([
            'testProperty' => 'test',
        ]);
    }

    public function it_formats_eloquent_data()
    {
        $this->beConstructedWith($this->filterWordpress);

        $filteredData = $this->format($this->dummyData->getPost(true))->toArray();

        $filteredData->shouldHaveKey('post_title');
        $filteredData->shouldHaveKey('post_content');

        $filteredData->shouldNotHaveKey('post_type');
        $filteredData->shouldNotHaveKey('post_name');
    }
}
