<?php 

namespace Malas\BounceHandler\Model;

/**
 * Result class will contain the result of email bounce parsing. 
 */
class Result {

	/**
	 * Overal processed messages count.
	 * @var integer
	 */
	protected $parsed;

	/**
	 * Soft bounce messages. One can try sending messages to these recipients. however if soft bounces continue, you should stop sending emails to the recipient in order to maintain good sender reputation.
	 * @var [type]
	 */
	protected $soft_bounced;

	/**
	 * Hard bounced messages. One should stop sending emails to these recipients.
	 * @var array
	 */
	protected $hard_bounced;

	/**
	 * Unsuccessfully parsed messages. These should be examined manually. Preferably new rules to the library should be added in order to parse these automatically next time.
	 * @var array
	 */
	protected $unknown;

	/**
	 * Messages which were identified as autoreplies, manual replies, vacation autoreplies, etc. In short messages which are not a bounce of any kind and were parsed successfully.
	 * @var array
	 */
	protected $other;

	public function __construct() {
		$this->parsed = 0;
		$this->soft_bounced = [];
		$this->hard_bounced = [];
		$this->unknown = [];
		$this->other = [];
	}

	public function getMessagesParsed() {
		return $this->parsed;
	}

	public function addParsedResult(Message $message) {
		if ($message->getStatus() == Message::STATUS_HARD_BOUNCE) {
			$this->addHardBounced($message);
		} elseif ($message->getStatus() == Message::STATUS_SOFT_BOUNCE) {
			$this->addSoftBounced($message);
		} elseif ($message->getStatus() == Message::STATUS_OK) {
			$this->addOther($message);
		} else {
			$this->addUnknown($message);
		}
		$this->addMessagesParsed(1);
	}

	public function addSoftBounced(Message $message) {
		$this->soft_bounced[] = $message;
	}

	public function addHardBounced(Message $message) {
		$this->hard_bounced[] = $message;
	}	

	public function addUnknown(Message $message) {
		$this->unknown[] = $message;
	}

	public function addOther(Message $message) {
		$this->other[] = $message;
	}

	public function addMessagesParsed($count) {
		$this->parsed += $count;
	}

	public function getSoftBounced() {
		return $this->soft_bounced;
	}

	public function getHardBounced() {
		return $this->hard_bounced;
	}

	public function getUnknown() {
		return $this->unknown;
	}

	public function getOther() {
		return $this->other;
	}
}
