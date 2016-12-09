<?php

namespace spec\Malas\BounceHandler;

use Malas\BounceHandler\BounceHandler;
use Malas\BounceHandler\Model\Message;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BounceHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BounceHandler::class);
    }

    function it_should_not_accept_non_array_data() {
    	$this->shouldThrow('\InvalidArgumentException')->duringParse('ss');
    }

    function it_should_not_parse_non_message_data() {
    	$result = $this->parse([
    		new Message('', ''),
    		null,
    		new Message('', '')
    	]);
    	$result->shouldBeAnInstanceOf('Malas\BounceHandler\Model\Result');
    	$result->getMessagesParsed()->shouldBe(2);
    }

    function it_should_have_new_result_after_each_parse() {
    	$this->parse($this->getMessageArray());

    	$result = $this->parse([]);
    	$result->shouldBeAnInstanceOf('Malas\BounceHandler\Model\Result');
    	$result->getMessagesParsed()->shouldBe(0);
    }

    static function getMessageArray() {
    	return [
    		new Message('', ''),
    		new Message('', ''),
    		new Message('', '')
    	];
    }

}
