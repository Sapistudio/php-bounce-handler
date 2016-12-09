<?php 

namespace Malas\BounceHandler\Model;

/**
 * Message class represents a single email message. 
 */
class Message {

	protected $header;
	protected $body;

	public function __construct($header, $body) {
		$this->header = $header;
		$this->body = $body;
	}
}
