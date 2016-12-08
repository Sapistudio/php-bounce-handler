<?php 

namespace Malas\BounceHandler;

use Malas\BounceHandler\Model\Result;
use Malas\BounceHandler\Model\Message;

/**
 * Main class for handling the bounced emails. 
 */
class BounceHandler {

	protected $result;

	public function __construct() {
		$this->result = new Result();
	}

	public function parse($messages) {
		if (!is_array($messages)) throw new \InvalidArgumentException();
		$this->resetResult();
		foreach($messages as $message) {
			if ($message instanceof Message) $this->parseMessage($message);
		}
		return $this->result;
	}

	protected function parseMessage(Message $message) {
		$this->result->addParsedResult();
	}

	protected function resetResult() {
		$this->result = new Result();
	}
}
