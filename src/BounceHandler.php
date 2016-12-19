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

	/**
	 * Main method to parse the messages.
	 * @param  array $messages Messages to be parsed.
	 * @return Result          Result object with parsed messages.
	 */
	public function parse($messages) {
		if (!is_array($messages)) throw new \InvalidArgumentException();
		$this->resetResult();
		foreach($messages as $message) {
			if ($message instanceof Message) $this->result->addParsedResult($this->parseMessage($message));
		}
		return $this->result;
	}

	public function parseMessage(Message $message) {
		return $this->parseRecipient($message);
	}

	protected function resetResult() {
		$this->result = new Result();
	}

	/**
	 * Tries to parse recipient.
	 * @param  Message $message Message to be parsed.
	 * @return Message          The same Message given to parse, but with recipient set if the parsing was successfull. 
	 */
	protected function parseRecipient(Message $message) {
		return $message;
	}
}
