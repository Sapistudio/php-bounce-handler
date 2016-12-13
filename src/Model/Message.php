<?php 

namespace Malas\BounceHandler\Model;

/**
 * Message class represents a single email message. 
 */
class Message {

	const STATUS_UNKNOWN = 1;
	const STATUS_HARD_BOUNCE = 2;
	const STATUS_SOFT_BOUNCE = 3;
	const STATUS_OK = 4;

	protected $header;
	protected $body;
	protected $recipient;

	protected $status;

	public function __construct($header, $body) {
		$this->header = $header;
		$this->body = $body;
		$this->status = self::STATUS_UNKNOWN;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function getStatus() {
		return $this->status;
	}
}
