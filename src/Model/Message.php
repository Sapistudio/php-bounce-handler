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

  protected $status_code;
	protected $status;

	public function __construct($header, $body) {
		$this->header = $header;
		$this->body = $body;
    $this->status_code = '';
		$this->status = self::STATUS_UNKNOWN;
	}

	public function setRecipient($email) {
		$this->recipient = $email;
	}

	public function getRecipient() {
		return $this->recipient;
	}

  public function setStatusCode($status_code) {
		$this->status_code = $status_code;
	}

	public function getStatusCode() {
		return $this->status_code;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getHeader() {
		return $this->header;
	}

	public function getBody() {
		return $this->body;
	}
}
