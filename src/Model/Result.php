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

	protected $soft_bounced;

	protected $hard_bounced;

	protected $unknown;

	public function __construct() {
		$this->parsed = 0;
		$this->soft_bounced = [];
		$this->hard_bounced = [];
		$this->unknown = [];
	}

	public function getMessagesParsed() {
		return $this->parsed;
	}

	public function addParsedResult() {
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

	public function addMessagesParsed($count) {
		$this->parsed += $count;
	}
}
