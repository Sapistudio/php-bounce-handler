<?php

namespace spec\Malas\BounceHandler\MailImport;

use Malas\BounceHandler\MailImport\IMAPMailImport;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IMAPMailImportSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
    	$this->beConstructedWith(['mailbox' => '{localhost:143/imap/notls}']);
        $this->shouldHaveType(IMAPMailImport::class);
    }

    function it_should_not_be_initialized_without_mailbox_option() {
    	$this->beConstructedWith(['random_option' => '']);
    	$this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_have_all_properties_set()
    {
    	$this->beConstructedWith([
    		'mailbox' => "{localhost:143/imap/notls}INBOX",
            'username' => 'examplelocalhost',
            'password' => 'p@$$word',
            'delete_mail' => false,
            'options' => 32580,
    		]);
        $this->getMailbox()->shouldBe('{localhost:143/imap/notls}INBOX');
        $this->getUsername()->shouldBe('examplelocalhost');
        $this->getPassword()->shouldBe('p@$$word');
        $this->getDeleteMail()->shouldBe(false);
        $this->getOptions()->shouldBe(32580);
    }
}
