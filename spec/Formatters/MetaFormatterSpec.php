<?php

namespace spec\kriskbx\mikado\Formatters;

use kriskbx\mikado\Manager;
use PhpSpec\ObjectBehavior;
use DummyData;

class MetaFormatterSpec extends ObjectBehavior
{
    protected $dummyData;

    protected $meta = [
        '_thumbnail_id' => 'thumbnail_id', // Test simple remapping
        '/^acf_field_repeater_([0-9]*)_acf_field_input$/i' => 'test_repeater.$1.input', // Test regex
    ];

    public function let()
    {
        $this->dummyData = new DummyData();
        $this->beConstructedWith($this->meta);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('kriskbx\mikado\Formatters\MetaFormatter');
    }

    public function it_formats_data()
    {
        $post = $this->dummyData->getPost();

        $post->meta->_thumbnail_id = 1;
        $post->meta->acf_field_repeater_0_acf_field_input = 'test_value_1';
        $post->meta->acf_field_repeater_1_acf_field_input = 'test_value_2';
        $post->meta->acf_field_repeater_2_acf_field_input = 'test_value_3';

        $post = Manager::formatAble($post);
        $formattedData = $this->format($post)->toArray();

        $formattedData->shouldHaveKeyWithValue('thumbnail_id', 1);
        $formattedData->shouldHaveKeyWithValue('test_repeater', [
            ['input' => 'test_value_1'],
            ['input' => 'test_value_2'],
            ['input' => 'test_value_3'],
        ]);
    }
}
