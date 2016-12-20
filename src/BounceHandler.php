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
		// get original message headers
		$headers = $this->parseHeaders($message->getHeader());
		// check subject of message. Mail delivery failure
		$subject = isset($headers['subject']) ? $headers['subject'] : null;
		// get original recipient
		$original_message_headers = $this->parseHeaders($message->getBody());
		
		if (isset($original_message_headers['to'])) {
			// message parsed successfully
			$message->setRecipient($original_message_headers['to']);
			$message->setStatus(Message::STATUS_OK);
		}		
		return $message;
	}

	protected function resetResult() {
		$this->result = new Result();
	}

	/**
	 * Parses email header like content into key => value array.
	 * @param  string $headers Header like content
	 * @return array           Header key => value array
	 */
	protected function parseHeaders($headers) {
		// split headers string into separate lines
		$headerLines = explode("\r\n", $headers);
		$result = [];
		foreach ($headerLines as $line) {
            if (preg_match('/^([^\s.]*):\s*(.*)\s*/', $line, $matches)) {
            	// line has a format of KEY: VALUE, so store the key and its value
                $key = strtolower($matches[1]);
                if (!isset($result[$key])) {
                	// new key, so store its value
                    $result[$key] = trim($matches[2]);
                } elseif ($key && $matches[2] && $matches[2] != $result[$key]) {
                	// key was already defined, so if the value is different from stored, append it to the previous one
                    $result[$key] .= '|'.trim($matches[2]);
                }
            } elseif (preg_match('/^\s+(.+)\s*/', $line) && isset($key)) {
            	// line is a continuation of previous line value so we append it to previously defined key
            	// this can occur when the header value is too long to fit in one line
            	// for example DKIM signature
                $result[$key] .= ' '.$line;
            }
        }
        return $result;
	}
}
