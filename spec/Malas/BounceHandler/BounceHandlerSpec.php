<?php

namespace spec\Malas\BounceHandler;

use Malas\BounceHandler\BounceHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BounceHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BounceHandler::class);
    }
}
