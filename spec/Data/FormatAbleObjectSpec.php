<?php

namespace spec\kriskbx\mikado\Data;

use PhpSpec\ObjectBehavior;
use DummyData;
use stdClass;

class FormatAbleObjectSpec extends ObjectBehavior
{
    protected $dummyData;

    public function let()
    {
        $this->dummyData = new DummyData();
        $this->beConstructedWith($this->dummyData->getStdClass());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('kriskbx\mikado\Data\FormatAbleObject');
    }

    public function it_can_resolve_an_array_path_in_punctuation_format()
    {
        $this->resolveArrayPath('arrayProperty.level1')->shouldReturn('$this->object->arrayProperty["level1"]');
        $this->resolveArrayPath('arrayProperty.level1.level2')->shouldReturn('$this->object->arrayProperty["level1"]["level2"]');
    }

    public function it_can_test_if_a_property_exists()
    {
        $this->hasProperty('testProperty')->shouldReturn(true);
        $this->hasProperty('notExistingProperty')->shouldReturn(false);
        $this->hasProperty('arrayProperty.level1')->shouldReturn(true);
        $this->hasProperty('arrayProperty.level1.level2')->shouldReturn(true);
        $this->hasProperty('arrayProperty.level1.level2.level3')->shouldReturn(true);
        $this->hasProperty('arrayProperty.level1.level2.level3.level4')->shouldReturn(false);
    }

    public function it_can_test_if_a_property_exists_on_eloquent_objects()
    {
        $this->beConstructedWith($this->dummyData->getPost());
        $this->hasProperty('post_title')->shouldReturn(true);
        $this->hasProperty('not_defined_property')->shouldReturn(false);
    }

    public function it_gets_a_property()
    {
        $this->getProperty('testProperty')->shouldReturn($this->dummyData->getStdClass()->testProperty);
        $this->getProperty('notExistingProperty')->shouldReturn(null);
        $this->getProperty('arrayProperty.level1')->shouldReturn($this->dummyData->getStdClass()->arrayProperty['level1']);
        $this->getProperty('arrayProperty.level1.level2')->shouldReturn($this->dummyData->getStdClass()->arrayProperty['level1']['level2']);
        $this->getProperty('arrayProperty.level1.level2.level3')->shouldReturn($this->dummyData->getStdClass()->arrayProperty['level1']['level2']['level3']);
        $this->getProperty('arrayProperty.level1.level2.level3.level4')->shouldReturn(null);
    }

    public function it_gets_a_property_from_an_eloquent_object()
    {
        $this->beConstructedWith($this->dummyData->getPost());
        $this->getProperty('post_title')->shouldReturn($this->dummyData->getPost()->post_title);
    }

    public function it_sets_a_property()
    {
        $this->setProperty('testProperty', $testValue = 'awesome-test-value')->shouldReturn(true);
        $this->getProperty('testProperty')->shouldReturn($testValue);

        $this->setProperty('notExistingProperty', $testValue)->shouldReturn(true);
        $this->getProperty('notExistingProperty')->shouldReturn($testValue);

        $this->setProperty('arrayProperty.level1.level2.level3.level4', $testValue)->shouldReturn(true);
        $this->getProperty('arrayProperty.level1.level2.level3.level4')->shouldReturn($testValue);

        $this->setProperty('arrayProperty.level1.level2.level3', ['level4' => $testValue])->shouldReturn(true);
        $this->getProperty('arrayProperty.level1.level2.level3.level4')->shouldReturn($testValue);

        $this->setProperty('arrayProperty.level1.level2.level3', $testValue)->shouldReturn(true);
        $this->getProperty('arrayProperty.level1.level2.level3')->shouldReturn($testValue);

        $this->setProperty('arrayProperty.level1.level2', $testValue)->shouldReturn(true);
        $this->getProperty('arrayProperty.level1.level2')->shouldReturn($testValue);

        $this->setProperty('arrayProperty.level1', $testValue)->shouldReturn(true);
        $this->getProperty('arrayProperty.level1')->shouldReturn($testValue);
    }

    public function it_doesnt_overwrite_properties()
    {
        $this->setProperty('arrayProperty.level1.level2', $testValue = 'awesome-test-value')->shouldReturn(true);
        $this->getProperty('arrayProperty.level1.level2')->shouldReturn($testValue);

        $this->setProperty('arrayProperty.level1.level3', $testValue)->shouldReturn(true);
        $this->getProperty('arrayProperty.level1.level3')->shouldReturn($testValue);

        $this->getProperty('arrayProperty.level1.level2')->shouldReturn($testValue);
    }

    public function it_sets_a_property_on_an_eloquent_model()
    {
        $this->beConstructedWith($this->dummyData->getPost());

        $this->setProperty('testProperty', $testValue = 'awesome-test-value')->shouldReturn(true);
        $this->getProperty('testProperty')->shouldReturn($testValue);

        $this->setProperty('post_title', $testValue = 'awesome-test-value')->shouldReturn(true);
        $this->getProperty('post_title')->shouldReturn($testValue);

        $this->setProperty('array.level1.level2', $testValue)->shouldReturn(true);
        $this->getProperty('array.level1.level2')->shouldReturn($testValue);
    }

    public function it_prevents_bad_code_injections()
    {
        $injection = 'testProperty); $this->object->testProperty = stdClass(); echo . (true';
        $this->hasProperty($injection)->shouldReturn(false);
        $this->getProperty('testProperty')->shouldReturn('test');
    }

    public function it_can_be_represented_as_array()
    {
        $this->toArray()->shouldReturn((array) $this->dummyData->getStdClass());
    }

    public function it_can_be_represented_as_array_when_it_has_eloquent_data()
    {
        $this->beConstructedWith($this->dummyData->getPost());

        $this->toArray()->shouldReturn($this->dummyData->getPost()->toArray());
    }

    public function it_can_be_represented_as_json()
    {
        $this->toJson()->shouldReturn(json_encode($this->dummyData->getStdClass()));
    }

    public function it_can_be_represented_as_json_when_it_has_eloquent_data()
    {
        $this->beConstructedWith($this->dummyData->getPost());

        $this->toJson()->shouldReturn($this->dummyData->getPost()->toJson());
    }

    public function it_can_unset_null_properties_and_keys_recursively()
    {
        $this->unsetNull();
        $this->toArray()->shouldReturn([
            'testProperty' => 'test',
            'arrayProperty' => [
                'level1' => [
                    'level2' => [
                        'level3' => 'value',
                    ],
                ],
            ],
        ]);
    }

    public function it_can_unset_null_properties_on_eloquent_data()
    {
        $wordpress = $this->dummyData->getPost();
        $wordpress->post_title = null;

        $this->beConstructedWith($wordpress);
        $this->unsetNull();

        $this->toArray()->shouldNotHaveKey('post_title');
    }
}
