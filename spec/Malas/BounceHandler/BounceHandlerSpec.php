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

    function it_should_have_messages_recipient_after_parsing() {
        $msgs = $this->getMessageArray();
        $msg = $this->parseMessage($msgs[0]);

        $msg->getRecipient()->shouldBe('ttt.eepe@lt.g4s.com');
    }

    static function getMessageArray() {
    	return [
    		new Message(
                "Return-path: <>\r\n
      Envelope-to: edemo@mexample.com\r\n
      Delivery-date: Thu, 03 Nov 2016 12:23:49 +0200\r\n
      Received: from mail by texample.com with spam-scanned (Exim 4.72)\r\n
      \tid 1c2FBU-0002ES-TS\r\n
      \tfor edemo@mexample.com; Thu, 03 Nov 2016 12:23:48 +0200\r\n
      Received: from [209.85.215.67] (helo=mail-lf0-f67.google.com)\r\n
      \tby texample.com with esmtps (UNKNOWN:AES128-GCM-SHA256:128)\r\n
      \t(Exim 4.72)\r\n
      \tid 1c2FBU-0002EP-PW\r\n
      \tfor edemo@mexample.com; Thu, 03 Nov 2016 12:23:48 +0200\r\n
      Received: by mail-lf0-f67.google.com with SMTP id n3so2518536lfn.0\r\n
              for <edemo@mexample.com>; Thu, 03 Nov 2016 03:23:48 -0700 (PDT)\r\n
      DKIM-Signature: v=1; a=rsa-sha256; c=relaxed/relaxed;\r\n
              d=googlemail.com; s=20120113;\r\n
              h=mime-version:from:to:auto-submitted:subject:references:in-reply-to\r\n
               :message-id:date;\r\n
              bh=tq3gn+CXp1v+q0jgpP3QxwGL/BpogO4RR8shGPjF+98=;\r\n
              b=clQ5LMimoW+7o8HMHKKBCTmCQ7kKJ5Bs4bTZnYHAvMQrcMdYz3iox1uWK60v+77aCB\r\n
               grkh5t44AHjvDtRNwtOR+8guMAAMWHUpTTtCoR9yjWvjmheJ07m3uM1VMQNgmq2nWHOM\r\n
               relDVVvFby5NWB8cd4VxW9BrtfZyVAhFI+pm4uSBmfvHrDUtYTAR/ZLeGyH6a86Uoc14\r\n
               7HU2uFwo0+twpkV1eRjl9+Gv9rB+KfsWM8Csk5Y28ygA2pw9aAXFbP6GfWPR/DOWriUa\r\n
               /VZqto++Efw/c4QLvrxsi9BBlPj25hpqYZvcjWXCBbOmEx8Njz8xYhidESlZ9ZC7gu8m\r\n
               /uPw==\r\n
      X-Google-DKIM-Signature: v=1; a=rsa-sha256; c=relaxed/relaxed;\r\n
              d=1e100.net; s=20130820;\r\n
              h=x-gm-message-state:mime-version:from:to:auto-submitted:subject\r\n
               :references:in-reply-to:message-id:date;\r\n
              bh=tq3gn+CXp1v+q0jgpP3QxwGL/BpogO4RR8shGPjF+98=;\r\n
              b=YH3i6sC7dvUk7SSmVI8p4hvgB1/rQUvxo1r0Qjci953e2x61mHq/94w2BXIDGG82x/\r\n
               EJLpMrrsi8H3fqg8kP0QKxDqIO3OpoG/t7yFhuezDzh3Zfbi7c93eh7MIyhyxGUnD7VJ\r\n
               TfqUfHhG6gKyva0tysZIS7aZmVB5GgCjBcUKskO/HRiIC46NVX73nbSpyVN+Tergx/q7\r\n
               OKtKrKkslSqFWQTFmBeffinph2qWWnpIbABMbvNmWk34lh0d1xAcW1cZJhcyYbpZSaVa\r\n
               /UiqMWv76ha98zQYpMR+EF6LNjdtX7j7CkRFoDMVVh4OfecYlch7DZ+kr1BSLVM8PfTK\r\n
               FQ6g==\r\n
      X-Gm-Message-State: ABUngvedoeLxg7aDj2Cj+RdunNVuezG2OSs30LkS0WIfk8BlE60557GBLUN7297OOcMk0QyNV6GbQjNZNf9xgKTEFCecCwwZ\r\n
      X-Received: by 10.25.23.101 with SMTP id n98mr4954641lfi.147.1478168628353;\r\n
              Thu, 03 Nov 2016 03:23:48 -0700 (PDT)\r\n
      MIME-Version: 1.0\r\n
      Received: by 10.25.23.101 with SMTP id n98mr4905776lfi.147; Thu, 03 Nov 2016\r\n
       03:23:48 -0700 (PDT)\r\n
      From: Mail Delivery Subsystem <mailer-daemon@googlemail.com>\r\n
      To: edemo@mexample.com\r\n
      Auto-Submitted: auto-replied\r\n
      Subject: Delivery Status Notification (Delay)\r\n
      References: <2fbe11c84fe018c729e0f7869d658273@swift.generated>\r\n
      In-Reply-To: <2fbe11c84fe018c729e0f7869d658273@swift.generated>\r\n
      Message-ID: <001a113fad0202d7cc054062f4a3@google.com>\r\n
      Date: Thu, 03 Nov 2016 10:23:48 +0000\r\n
      Content-Type: text/plain; charset=UTF-8\r\n
      X-Spam-Checked: google.com\r\n
      \r\n", 
      "This is an automatically generated Delivery Status Notification\r\n
      \r\n
      THIS IS A WARNING MESSAGE ONLY.\r\n
      \r\n
      YOU DO NOT NEED TO RESEND YOUR MESSAGE.\r\n
      \r\n
      Delivery to the following recipient has been delayed:\r\n
      \r\n
           ttt.eepe@lt.g4s.com\r\n
      \r\n
      Message will be retried for 0 more day(s)\r\n
      \r\n
      Technical details of temporary failure: \r\n
      The recipient server did not accept our requests to connect. Learn more at https://support.google.com/mail/answer/7720 \r\n
      [mail.lt.g4s.com 193.150.40.9: timed out]\r\n
      \r\n
      ----- Original message -----\r\n
      \r\n
      X-Gm-Message-State: ABUngvei2wACCAFcumkszZYVME3H6sLZf6ccPBa6mhwc5GNQVlXMTppo5xy7sdCZhHg2eeqQPTI9hV3nfxUG1/UJ69t4iXy87Cu3RkrIYWS75Wr4fbb94q3uDrVKNRviJrOdn+0GpUWGo0SgtOK03A==\r\n
      X-Received: by 10.25.15.93 with SMTP id e90mr8219130lfi.147.1477630336094;\r\n
              Thu, 27 Oct 2016 21:52:16 -0700 (PDT)\r\n
      X-Received: by 10.25.15.93 with SMTP id e90mr8219122lfi.147.1477630335743;\r\n
              Thu, 27 Oct 2016 21:52:15 -0700 (PDT)\r\n
      Return-Path: <edemo@mexample.com>\r\n
      Received: from texample.com (texample.com. [109.235.64.142])\r\n
              by mx.google.com with ESMTPS id d186si6974451lfg.83.2016.10.27.21.52.15\r\n
              for <ttt.eepe@lt.g4s.com>\r\n
              (version=TLS1_2 cipher=ECDHE-RSA-AES128-GCM-SHA256 bits=128/128);\r\n
              Thu, 27 Oct 2016 21:52:15 -0700 (PDT)\r\n
      Received-SPF: pass (google.com: domain of edemo@mexample.com designates 109.235.64.142 as permitted sender) client-ip=109.235.64.142;\r\n
      Authentication-Results: mx.google.com;\r\n
             spf=pass (google.com: domain of edemo@mexample.com designates 109.235.64.142 as permitted sender) smtp.mailfrom=edemo@mexample.com\r\n
      Received: from texample.com ([109.235.64.142] helo=[127.0.0.1])\r\n
      \tby texample.com with esmtpa (Exim 4.72)\r\n
      \t(envelope-from <edemo@mexample.com>)\r\n
      \tid 1bzxgD-0003q1-Q2\r\n
      \tfor ttt.eepe@lt.g4s.com; Fri, 28 Oct 2016 06:18:05 +0300\r\n
      Message-ID: <2fbe11c84fe018c729e0f7869d658273@swift.generated>\r\n
      Date: Fri, 28 Oct 2016 06:12:01 +0300\r\n
      Subject: =?utf-8?Q?=C5=A0ios?= dienos nauja informacija [Nemokamas Video] -\r\n
       2016-10-28\r\n
      From: =?utf-8?Q?Mokes=C4=8Di=C5=B3?= SUFLERIS\r\n
       <neatsakineti@mexample.com>\r\n
      To: ttt.eepe@lt.g4s.com\r\n
      MIME-Version: 1.0\r\n
      Content-Type: text/html; charset=utf-8\r\n
      Content-Transfer-Encoding: quoted-printable\r\n
      X-Sender: neatsakineti@mexample.com\r\n
      X-Gm-Spam: 0\r\n
      X-Gm-Phishy: 0\r\n
      \r\n
      ----- End of message -----\r\n
      \r\n"),
    		new Message("Return-path: <>\r\n
      Envelope-to: edemo@mexample.com\r\n
      Delivery-date: Fri, 04 Nov 2016 04:06:05 +0200\r\n
      Received: from mail by texample.com with spam-scanned (Exim 4.72)\r\n
      \tid 1c2TtN-0002Kj-5b\r\n
      \tfor edemo@mexample.com; Fri, 04 Nov 2016 04:06:05 +0200\r\n
      Received: from [194.135.87.24] (helo=lynas.serveriai.lt)\r\n
      \tby texample.com with esmtps (UNKNOWN:DHE-RSA-AES256-GCM-SHA384:256)\r\n
      \t(Exim 4.72)\r\n
      \tid 1c2TtN-0002Kg-4e\r\n
      \tfor edemo@mexample.com; Fri, 04 Nov 2016 04:06:05 +0200\r\n
      Received: from mail by lynas.serveriai.lt with local (Exim 4.84_2)\r\n
      \tid 1c2TtM-0003QR-Py\r\n
      \tfor edemo@mexample.com; Fri, 04 Nov 2016 04:06:04 +0200\r\n
      From: visita@vvvgr.lt\r\n
      To: edemo@mexample.com\r\n
      Subject: Re: Atsakytas Jūsų klausimas\r\n
      In-Reply-To: <c7ee48400922e9a54219fc90695f58bd@swift.generated>\r\n
      References: <c7ee48400922e9a54219fc90695f58bd@swift.generated>\r\n
      Auto-Submitted: auto-replied\r\n
      Content-Type: text/plain; charset=UTF-8\r\n
      Content-Transfer-Encoding: 8bit\r\n
      Message-Id: <E1c2TtM-0003QR-Py@lynas.serveriai.lt>\r\n
      Date: Fri, 04 Nov 2016 04:06:04 +0200\r\n
      X-Sender: \r\n
      \r\n", 
      "Laba diena,\r\n
      Nuo spalio 22 d. iki lapkričio 6 d. atostogauju.\r\n
      Esant svarbiems klausimams kreipkitės el. paštu biuras@vvvgr.lt arba tel. nr. 85 2041543.  \r\n
      \r\n
      _______________________________________\r\n
      \r\n
      \r\n
      I am out of the office until 2016.11.06.\r\n"),
    		new Message("Return-path: <>\r\n
      Envelope-to: edemo@mexample.com\r\n
      Delivery-date: Fri, 04 Nov 2016 06:02:01 +0200\r\n
      Received: from mail by texample.com with local (Exim 4.72)\r\n
      \tid 1c2VhZ-0006zq-Tg\r\n
      \tfor edemo@mexample.com; Fri, 04 Nov 2016 06:02:01 +0200\r\n
      Date: Fri, 04 Nov 2016 06:02:01 +0200\r\n
      Message-Id: <E1c2VhZ-0006zq-Tg@texample.com>\r\n
      X-Failed-Recipients: fnc@ppp.ru\r\n
      Auto-Submitted: auto-replied\r\n
      From: Mail Delivery System <Mailer-Daemon@texample.com>\r\n
      To: edemo@mexample.com\r\n
      Subject: Mail delivery failed: returning message to sender\r\n
      \r\n", 
      "This message was created automatically by mail delivery software.\r\n
      \r\n
      A message that you sent could not be delivered to one or more of its\r\n
      recipients. This is a permanent error. The following address(es) failed:\r\n
      \r\n
        fnc@ppp.ru\r\n
          SMTP error from remote mail server after RCPT TO:<fnc@ppp.ru>:\r\n
          host ch.netsana.lt [86.100.145.152]: 550 relay not permitted\r\n
      \r\n
      ------ This is a copy of the message's headers. ------\r\n
      \r\n
      Return-path: <edemo@mexample.com>\r\n
      Received: from texample.com ([109.235.64.142] helo=[127.0.0.1])\r\n
      \tby texample.com with esmtpa (Exim 4.72)\r\n
      \t(envelope-from <edemo@mexample.com>)\r\n
      \tid 1c2VhZ-0006zf-Lu\r\n
      \tfor fnc@ppp.ru; Fri, 04 Nov 2016 06:02:01 +0200\r\n
      Message-ID: <7d5091f877aecbd08fda2e8b115d1572@swift.generated>\r\n
      Date: Fri, 04 Nov 2016 06:00:02 +0200\r\n
      Subject: =?utf-8?Q?=C5=A0ios?= dienos nauja informacija [Nemokamas Video] -\r\n
       2016-11-04\r\n
      From: =?utf-8?Q?Mokes=C4=8Di=C5=B3?= SUFLERIS\r\n
       <neatsakineti@mexample.com>\r\n
      To: fnc@ppp.ru\r\n
      MIME-Version: 1.0\r\n
      Content-Type: text/html; charset=utf-8\r\n
      Content-Transfer-Encoding: quoted-printable\r\n
      \r\n")
    	];
    }

}
