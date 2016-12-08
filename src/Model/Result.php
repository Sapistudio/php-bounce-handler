<?php 

namespace Malas\BounceHandler\Model;

/**
 * Result class will contain the result of email bounce parsing. 
 */
class Result {

	protected $messages_parsed;

	public function __construct() {
		$this->messages_parsed = 0;
	}

	public function getMessagesParsed() {
		return $this->messages_parsed;
	}

	public function addParsedResult() {
		$this->addMessagesParsed(1);
	}

	public function addMessagesParsed($count) {
		$this->messages_parsed += $count;
	}
}
