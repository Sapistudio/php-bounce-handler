<?php

namespace spec\Malas\BounceHandler\MailImport;

use Malas\BounceHandler\MailImport\IMAPMailImport;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IMAPMailImportSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IMAPMailImport::class);
    }
}
