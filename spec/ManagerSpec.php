<?php

namespace spec\kriskbx\mikado;

use DummyData;
use kriskbx\mikado\Formatters\NullFormatter;
use PhpSpec\ObjectBehavior;

class ManagerSpec extends ObjectBehavior
{
    protected $dummy;

    protected $formatter;

    public function let()
    {
        $this->dummy = new DummyData();
        $this->formatter = new NullFormatter();
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('kriskbx\mikado\Manager');
    }

    public function it_holds_an_array_of_formatters()
    {
        $this->add($this->formatter)->shouldReturn($this);
    }

    public function it_calls_format_on_the_formatters()
    {
        $this->add($this->formatter);
        $this->format($this->dummy->getStdClass());
    }

    public function it_can_create_a_formattable_object_out_of_the_given_data()
    {
        $this->formatAble($this->dummy->getStdClass())->shouldImplement('kriskbx\mikado\Contracts\FormatAble');
    }

    public function it_knows_how_to_process_collections()
    {
        $this->add($this->formatter);
        $this->format($this->dummy->getMultiplePosts());
    }
}
