<?php

namespace spec\kriskbx\mikado\Formatters;

use DummyData;
use PhpSpec\ObjectBehavior;

class RemapFormatterSpec extends ObjectBehavior
{
    protected $dummyData;

    protected $remappingRules = [
        'testProperty' => 'test',
        'arrayProperty' => 'array',
    ];

    protected $arrayRules = [
        '/^array.(.*).key$/i' => 'array.$1.keyneu',
        '/^array.(.*).key2$/i' => 'array.$1.key2neu',
    ];

    protected $wordpressRules = [
        'post_title' => 'title',
        'post_content' => 'content',
    ];

    public function let()
    {
        $this->dummyData = new DummyData();
        $this->beConstructedWith($this->remappingRules);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('kriskbx\mikado\Formatters\RemapFormatter');
    }

    public function it_formats_data()
    {
        $formatted = $this->format($this->dummyData->getStdClass(true))->toArray();
        $formatted->shouldHaveKey('test');
        $formatted->shouldHaveKey('array');
        $formatted->shouldNotHaveKey('testProperty');
        $formatted->shouldNotHaveKey('arrayProperty');
    }

    public function it_formats_wordpress_data()
    {
        $this->beConstructedWith($this->wordpressRules);

        $formatted = $this->format($this->dummyData->getPost(true))->toArray();
        $formatted->shouldHaveKey('title');
        $formatted->shouldHaveKey('content');
        $formatted->shouldNotHaveKey('post_title');
        $formatted->shouldNotHaveKey('post_content');
    }

    public function it_formats_arrays()
    {
        $this->beConstructedWith($this->arrayRules);

        $formatted = $this->format($this->dummyData->getStdClassWithArray(true))->toArray();
        $formatted->shouldHaveKeyWithValue('array', [
           [
               'keyneu' => 'value',
               'key2neu' => 'value2',
           ],
        ]);
    }
}
