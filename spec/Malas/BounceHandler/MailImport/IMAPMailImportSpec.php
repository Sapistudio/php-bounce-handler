<?php

namespace spec\Malas\BounceHandler\MailImport;

use Malas\BounceHandler\MailImport\IMAPMailImport;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IMAPMailImportSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
    	$this->beConstructedWith(['mailbox' => '']);
        $this->shouldHaveType(IMAPMailImport::class);
    }

    function it_should_not_be_initialized_without_mailbox_option() {
    	$this->beConstructedWith(['random_option' => '']);
    	$this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }
}
