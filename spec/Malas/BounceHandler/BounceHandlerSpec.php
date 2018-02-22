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
        $messages = $this->getMessageArray();
    	$result = $this->parse([
    		null,
    	]);
    	$result->shouldBeAnInstanceOf('Malas\BounceHandler\Model\Result');
    	$result->getMessagesParsed()->shouldBe(0);
    }

    function it_should_have_new_result_after_each_parse() {
    	$this->parse($this->getMessageArray());

    	$result = $this->parse([]);
    	$result->shouldBeAnInstanceOf('Malas\BounceHandler\Model\Result');
    	$result->getMessagesParsed()->shouldBe(0);
        $result->getSoftBounced()->shouldHaveCount(0);
        $result->getHardBounced()->shouldHaveCount(0);
        $result->getUnknown()->shouldHaveCount(0);
        $result->getOther()->shouldHaveCount(0);
    }

    function it_should_have_messages_recipient_after_parsing_gmail_bounce() {
        $msgs = $this->getMessageArray();
        $msg = $this->parseMessage($msgs[0]);

        $msg->getRecipient()->shouldBe('ttt.eepe@lt.g4s.com');
    }

    function it_should_have_messages_recipient_after_parsing_delivery_failure_bounce() {
        $msgs = $this->getMessageArray();
        $msg = $this->parseMessage($msgs[2]);

        $msg->getRecipient()->shouldBe('fnc@ppp.ru');
    }

    function it_should_have_message_status_ok_after_parsing_vacation_autoreply() {
        $msgs = $this->getMessageArray();
        $msg = $this->parseMessage($msgs[1]);

        $msg->getStatus()->shouldBe(Message::STATUS_UNKNOWN);
    }

    function it_should_detect_bounce_from_subject_mail_delivery_failed() {
        $this->isBounceFromSubject('Mail Delivery Failed')->shouldBe(true);
    }

    function it_should_detect_bounce_from_subject_delivery_status_notification() {
        $this->isBounceFromSubject('Delivery status notification')->shouldBe(true);
    }

    function it_should_not_detect_bounce_from_subject_delivery_status_notification() {
        $this->isBounceFromSubject('Random subject failed life')->shouldBe(false);
    }

    function it_should_have_parsed_messages_in_correct_result_array() {
        $messageArray = $this->getMessageArray();
        $result = $this->parse($messageArray);
        $result->shouldBeAnInstanceOf('Malas\BounceHandler\Model\Result');
        $result->getMessagesParsed()->shouldBe(count($messageArray));
        $result->getSoftBounced()->shouldHaveCount(1);
        $result->getHardBounced()->shouldHaveCount(1);
        $result->getUnknown()->shouldHaveCount(3);
        $result->getOther()->shouldHaveCount(0);
    }

    /**
     * Helper method returns an array of semi real world messages.
     * @return array Messages
     */
    static function getMessageArray() {
    	return [
    		new Message(
            "Return-path: <>\r\nEnvelope-to: edemo@mexample.com\r\nDelivery-date: Thu, 03 Nov 2016 12:23:49 +0200\r\nReceived: from mail by texample.com with spam-scanned (Exim 4.72)\r\n\tid 1c2FBU-0002ES-TS\r\n\tfor edemo@mexample.com; Thu, 03 Nov 2016 12:23:48 +0200\r\nReceived: from [209.85.215.67] (helo=mail-lf0-f67.google.com)\r\n\tby texample.com with esmtps (UNKNOWN:AES128-GCM-SHA256:128)\r\n\t(Exim 4.72)\r\n\tid 1c2FBU-0002EP-PW\r\n\tfor edemo@mexample.com; Thu, 03 Nov 2016 12:23:48 +0200\r\nReceived: by mail-lf0-f67.google.com with SMTP id n3so2518536lfn.0\r\nfor <edemo@mexample.com>; Thu, 03 Nov 2016 03:23:48 -0700 (PDT)\r\nDKIM-Signature: v=1; a=rsa-sha256; c=relaxed/relaxed;\r\nd=googlemail.com; s=20120113;\r\nh=mime-version:from:to:auto-submitted:subject:references:in-reply-to\r\n:message-id:date;\r\nbh=tq3gn+CXp1v+q0jgpP3QxwGL/BpogO4RR8shGPjF+98=;\r\nb=clQ5LMimoW+7o8HMHKKBCTmCQ7kKJ5Bs4bTZnYHAvMQrcMdYz3iox1uWK60v+77aCB\r\ngrkh5t44AHjvDtRNwtOR+8guMAAMWHUpTTtCoR9yjWvjmheJ07m3uM1VMQNgmq2nWHOM\r\nrelDVVvFby5NWB8cd4VxW9BrtfZyVAhFI+pm4uSBmfvHrDUtYTAR/ZLeGyH6a86Uoc14\r\n7HU2uFwo0+twpkV1eRjl9+Gv9rB+KfsWM8Csk5Y28ygA2pw9aAXFbP6GfWPR/DOWriUa\r\n/VZqto++Efw/4QLvrxsi9BBlPj25hpqYZvcjWXCBbOmEx8Njz8xYhidESlZ9ZC7gu8m\r\n/uPw==\r\nX-Google-DKIM-Signature: v=1; a=rsa-sha256; c=relaxed/relaxed;\r\nd=1e100.net; s=20130820;\r\nh=x-gm-message-state:mime-version:from:to:auto-submitted:subject\r\n:references:in-reply-to:message-id:date;\r\nbh=tq3gn+CXp1v+q0jgpP3QxwGL/BpogO4RR8shGPjF+98=;\r\nb=YH3i6sC7dvUk7SSmVI8p4hvgB1/rQUvxo1r0Qjci953e2x61mHq/94w2BXIDGG82x/\r\nEJLpMrrsi8H3fqg8kP0QKxDqIO3OpoG/t7yFhuezDzh3Zfbi7c93eh7MIyhyxGUnD7VJ\r\nTfqUfHhG6gKyva0tysZIS7aZmVB5GgCjBcUKskO/HRiIC46NVX73nbSpyVN+Tergx/q7\r\nOKtKrKkslSqFWQTFmBeffinph2qWWnpIbABMbvNmWk34lh0d1xAcW1cZJhcyYbpZSaVa\r\n/UiqMWv76ha98zQYpMR+EF6LNjdtX7j7CkRFoDMVVh4OfecYlch7DZ+kr1BSLVM8PfTK\r\nFQ6g==\r\nX-Gm-Message-State: ABUngvedoeLxg7aDj2Cj+RdunNVuezG2OSs30LkS0WIfk8BlE60557GBLUN7297OOcMk0QyNV6GbQjNZNf9xgKTEFCecCwwZ\r\nX-Received: by 10.25.23.101 with SMTP id n98mr4954641lfi.147.1478168628353;\r\nThu, 03 Nov 2016 03:23:48 -0700 (PDT)\r\nMIME-Version: 1.0\r\nReceived: by 10.25.23.101 with SMTP id n98mr4905776lfi.147; Thu, 03 Nov 2016\r\n03:23:48 -0700 (PDT)\r\nFrom: Mail Delivery Subsystem <mailer-daemon@googlemail.com>\r\nTo: edemo@mexample.com\r\nAuto-Submitted: auto-replied\r\nSubject: Delivery Status Notification (Delay)\r\nReferences: <2fbe11c84fe018c729e0f7869d658273@swift.generated>\r\nIn-Reply-To: <2fbe11c84fe018c729e0f7869d658273@swift.generated>\r\nMessage-ID: <001a113fad0202d7cc054062f4a3@google.com>\r\nDate: Thu, 03 Nov 2016 10:23:48 +0000\r\nContent-Type: text/plain; charset=UTF-8\r\nX-Spam-Checked: google.com\r\n\r\n",
            "This is an automatically generated Delivery Status Notification\r\n\r\nTHIS IS A WARNING MESSAGE ONLY.\r\n\r\nYOU DO NOT NEED TO RESEND YOUR MESSAGE.\r\n\r\nDelivery to the following recipient has been delayed:\r\n\r\nttt.eepe@lt.g4s.com\r\n\r\nMessage will be retried for 0 more day(s)\r\n\r\nTechnical details of temporary failure: \r\nThe recipient server did not accept our requests to connect. Learn more at https://support.google.com/mail/answer/7720 \r\n[mail.lt.g4s.com 193.150.40.9: timed out]\r\n\r\n----- Original message -----\r\n\r\nX-Gm-Message-State: ABUngvei2wACCAFcumkszZYVME3H6sLZf6ccPBa6mhwc5GNQVlXMTppo5xy7sdCZhHg2eeqQPTI9hV3nfxUG1/UJ69t4iXy87Cu3RkrIYWS75Wr4fbb94q3uDrVKNRviJrOdn+0GpUWGo0SgtOK03A==\r\nX-Received: by 10.25.15.93 with SMTP id e90mr8219130lfi.147.1477630336094;\r\nThu, 27 Oct 2016 21:52:16 -0700 (PDT)\r\nX-Received: by 10.25.15.93 with SMTP id e90mr8219122lfi.147.1477630335743;\r\nThu, 27 Oct 2016 21:52:15 -0700 (PDT)\r\nReturn-Path: <edemo@mexample.com>\r\nReceived: from texample.com (texample.com. [109.235.64.142])\r\nby mx.google.com with ESMTPS id d186si6974451lfg.83.2016.10.27.21.52.15\r\nfor <ttt.eepe@lt.g4s.com>\r\n(version=TLS1_2 cipher=ECDHE-RSA-AES128-GCM-SHA256 bits=128/128);\r\nThu, 27 Oct 2016 21:52:15 -0700 (PDT)\r\nReceived-SPF: pass (google.com: domain of edemo@mexample.com designates 109.235.64.142 as permitted sender) client-ip=109.235.64.142;\r\nAuthentication-Results: mx.google.com;\r\nspf=pass (google.com: domain of edemo@mexample.com designates 109.235.64.142 as permitted sender) smtp.mailfrom=edemo@mexample.com\r\nReceived: from texample.com ([109.235.64.142] helo=[127.0.0.1])\r\n\tby texample.com with esmtpa (Exim 4.72)\r\n\t(envelope-from <edemo@mexample.com>)\r\n\tid 1bzxgD-0003q1-Q2\r\n\tfor ttt.eepe@lt.g4s.com; Fri, 28 Oct 2016 06:18:05 +0300\r\nMessage-ID: <2fbe11c84fe018c729e0f7869d658273@swift.generated>\r\nDate: Fri, 28 Oct 2016 06:12:01 +0300\r\nSubject: =?utf-8?Q?=C5=A0ios?= dienos nauja informacija [Nemokamas Video] -\r\n2016-10-28\r\nFrom: =?utf-8?Q?Mokes=C4=8Di=C5=B3?= SUFLERIS\r\n<neatsakineti@mexample.com>\r\nTo: ttt.eepe@lt.g4s.com\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\nX-Sender: neatsakineti@mexample.com\r\nX-Gm-Spam: 0\r\nX-Gm-Phishy: 0\r\n\r\n----- End of message -----\r\n\r\n"
            ),
    		new Message(
            "Return-path: <>\r\nEnvelope-to: edemo@mexample.com\r\nDelivery-date: Fri, 04 Nov 2016 04:06:05 +0200\r\nReceived: from mail by texample.com with spam-scanned (Exim 4.72)\r\n\tid 1c2TtN-0002Kj-5b\r\n\tfor edemo@mexample.com; Fri, 04 Nov 2016 04:06:05 +0200\r\nReceived: from [194.135.87.24] (helo=lynas.serveriai.lt)\r\n\tby texample.com with esmtps (UNKNOWN:DHE-RSA-AES256-GCM-SHA384:256)\r\n\t(Exim 4.72)\r\n\tid 1c2TtN-0002Kg-4e\r\n\tfor edemo@mexample.com; Fri, 04 Nov 2016 04:06:05 +0200\r\nReceived: from mail by lynas.serveriai.lt with local (Exim 4.84_2)\r\n\tid 1c2TtM-0003QR-Py\r\n\tfor edemo@mexample.com; Fri, 04 Nov 2016 04:06:04 +0200\r\nFrom: visita@vvvgr.lt\r\nTo: edemo@mexample.com\r\nSubject: Re: Atsakytas Jūsų klausimas\r\nIn-Reply-To: <c7ee48400922e9a54219fc90695f58bd@swift.generated>\r\nReferences: <c7ee48400922e9a54219fc90695f58bd@swift.generated>\r\nAuto-Submitted: auto-replied\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\nMessage-Id: <E1c2TtM-0003QR-Py@lynas.serveriai.lt>\r\nDate: Fri, 04 Nov 2016 04:06:04 +0200\r\nX-Sender: \r\n\r\n",
            "Laba diena,\r\nNuo spalio 22 d. iki lapkričio 6 d. atostogauju.\r\nEsant svarbiems klausimams kreipkitės el. paštu biuras@vvvgr.lt arba tel. nr. 85 2041543.  \r\n\r\n\r\n\r\n\r\nI am out of the office until 2016.11.06.\r\n"
            ),
    		new Message(
            "Return-path: <>\r\nEnvelope-to: edemo@mexample.com\r\nDelivery-date: Fri, 04 Nov 2016 06:02:01 +0200\r\nReceived: from mail by texample.com with local (Exim 4.72)\r\n\tid 1c2VhZ-0006zq-Tg\r\n\tfor edemo@mexample.com; Fri, 04 Nov 2016 06:02:01 +0200\r\nDate: Fri, 04 Nov 2016 06:02:01 +0200\r\nMessage-Id: <E1c2VhZ-0006zq-Tg@texample.com>\r\nX-Failed-Recipients: fnc@ppp.ru\r\nAuto-Submitted: auto-replied\r\nFrom: Mail Delivery System <Mailer-Daemon@texample.com>\r\nTo: edemo@mexample.com\r\nSubject: Mail delivery failed: returning message to sender\r\n\r\n",
            "This message was created automatically by mail delivery software.\r\n\r\nA message that you sent could not be delivered to one or more of its\r\nrecipients. This is a permanent error. The following address(es) failed:\r\n\r\nfnc@ppp.ru\r\nSMTP error from remote mail server after RCPT TO:<fnc@ppp.ru>:\r\nhost ch.netsana.lt [86.100.145.152]: 550 relay not permitted\r\n\r\n------ This is a copy of the message's headers. ------\r\n\r\nReturn-path: <edemo@mexample.com>\r\nReceived: from texample.com ([109.235.64.142] helo=[127.0.0.1])\r\n\tby texample.com with esmtpa (Exim 4.72)\r\n\t(envelope-from <edemo@mexample.com>)\r\n\tid 1c2VhZ-0006zf-Lu\r\n\tfor fnc@ppp.ru; Fri, 04 Nov 2016 06:02:01 +0200\r\nMessage-ID: 877aecbd08fda2e8b115d1572@swift.generated>\r\nDate: Fri, 04 Nov 2016 06:00:02 +0200\r\nSubject: =?utf-8?Q?=C5=A0ios?= dienos nauja informacija [Nemokamas Video] -\r\n      2016-11-04\r\nFrom: =?utf-8?Q?Mokes=C4=8Di=C5=B3?= SUFLERIS\r\n<neatsakineti@mexample.com>\r\nTo: fnc@ppp.ru\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n"
          ),
        new Message(
            "Return-path: <>\r\nEnvelope-to: edemo@example.com\r\nDelivery-date: Mon, 12 Feb 2018 14:15:09 +0200\r\nReceived: from mail by xxx.example.com with local (Exim 4.89)\r\n	id 1elD0n-00006Z-3S\r\n	for example@example.com; Mon, 12 Feb 2018 14:15:09 +0200\r\nX-Failed-Recipients: failed@example.com\r\nAuto-Submitted: auto-replied\r\nFrom: Mail Delivery System <Mailer-Daemon@example.com>\r\nTo: edemo@example.com\r\nContent-Type: multipart/report;\r\nreport-type=delivery-status; boundary=1518437709-eximdsn-982303089\r\nMIME-Version: 1.0\r\nSubject: Mail delivery failed: returning message to sender\r\nMessage-Id: <E1elD0n-00006Z-3S@e-xxx.example.com>\r\nDate: Mon, 12 Feb 2018 14:15:09 +0200",
            "--1518437709-eximdsn-982303089\r\nContent-type: text/plain; charset=us-ascii\r\n\r\nThis message was created automatically by mail delivery software.\r\nA message that you sent could not be delivered to one or more of its\r\nrecipients. This is a permanent error. The following address(es) failed:\r\n\r\n  receiver@example.com\r\n    host remote.jak.lt [84.15.106.55]\r\n    SMTP error from remote mail server after RCPT TO:<receiver@example.com>:\r\n    550 5.1.1 User unknown\r\n\r\n--1518437709-eximdsn-982303089\r\nContent-type: message/delivery-status\r\n\r\nReporting-MTA: dns; example.com\r\n\r\nAction: failed\r\nFinal-Recipient: rfc822;receiver@example.com\r\nStatus: 4.0.0\r\nRemote-MTA: dns; remote.jak.lt\r\nDiagnostic-Code: smtp; 550 5.1.1 User unknown\r\n\r\n--1518437709-eximdsn-982303089\r\nContent-type: text/rfc822-headers\r\n\r\nReturn-path: <edemo@example.com>\r\nReceived: from [109.235.64.142] (helo=example.com)\r\n\r\nid 1elD0g-00006D-Uf; Mon\r\n"
          ),
        new Message(
            "Return-path: <>\r\nEnvelope-to: edemo@example.com\r\nDelivery-date: Thu, 22 Feb 2018 06:31:42 +0200\r\nReceived: from mail by example.com with local (Exim 4.89)\r\n	id 1eoiXm-0006kL-3Q\r\n	for edemo@example.com; Thu, 22 Feb 2018 06:31:42 +0200\r\nX-Failed-Recipients: receiver@example.com\r\nAuto-Submitted: auto-replied\r\nFrom: Mail Delivery System <Mailer-Daemon@example.com>\r\nTo: edemo@example.com\r\nContent-Type: multipart/report;\r\nreport-type=delivery-status; boundary=1519273902-eximdsn-970628841\r\nMIME-Version: 1.0\r\nSubject: Mail delivery failed: returning message to sender\r\nMessage-Id: <E1eoiXm-0006kL-3Q@example.com>\r\nDate: Thu, 22 Feb 2018 06:31:42 +0200",
            "--1519273902-eximdsn-970628841\r\nContent-type: text/plain; charset=us-ascii\r\n\r\nThis message was created automatically by mail delivery software.\r\n\r\nA message that you sent could not be delivered to one or more of its\r\nrecipients. This is a permanent error. The following address(es) failed:\r\n\r\n  receiver@example.com\r\n    host pastas.e-box.lt [213.226.147.44]\r\n    SMTP error from remote mail server after RCPT TO:<receiver@example.com>:\r\n    550 5.1.1\r\n<receiver@example.com>: Recipient address rejected:\r\n    User unknown in virtual mailbox table\r\n\r\n--1519273902-eximdsn-970628841\r\nContent-type: message/delivery-status\r\n\r\nReporting-MTA: dns; example.com\r\n\r\nAction: failed\r\nFinal-Recipient: rfc822;receiver@example.com\r\nStatus: 5.0.0\r\nRemote-MTA: dns; pastas.e-box.lt\r\nDiagnostic-Code: smtp; 550 5.1.1 <receiver@example.com>: Recipient address rejected: User unknown in virtual mailbox table\r\n\r\n--1519273902-eximdsn-970628841\r\nContent-type: text/rfc822-headers\r\n\r\nReturn-path: <edemo@example.com>\r\nReceived: from [109.235.64.142] (helo=example.com)\r\n	by example.com with esmtpa (Exim 4.89)\r\n	(envelope-from <edemo@example.com>)\r\n	id 1eoiMY-0006F0-NM\r\n	for receiver@example.com; Thu, 22 Feb 2018 06:20:06 +0200\r\nMessage-ID: <327866e73df9c6249a0c259bcf4232ad@swift.generated>\r\nDate: Thu, 22 Feb 2018 06:14:01 +0200\r\nSubject: =?utf-8?Q?=C5=A0ios?= dienos nauja informacija - 2018-02-22\r\nFrom: =?utf-8?Q?Mokes=C4=8Di=C5=B3?= SUFLERIS\r\n <neatsakineti@example.com>\r\nTo: receiver@example.com\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n--1519273902-eximdsn-970628841--"
          )
    	];
    }

}
