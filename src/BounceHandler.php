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
		if ($this->isBounceFromHeader($headers)) {
			// for failed mail delivery bounces we need to fetch original message headers from body
			$original_message_headers = $this->parseHeaders($message->getBody());
			if (isset($original_message_headers['to'])) {
				// message parsed successfully
				$message->setRecipient($original_message_headers['to']);
				// only set status to bounce, when all crucial information is present, otherwise it should remain status unknown
				$message->setStatus(Message::STATUS_HARD_BOUNCE);
			}
		}
		return $message;
	}

	/**
	 * Checks if given header array can tell if the message is a bounced one.
	 * @param  array   $headerArray Array of message headers
	 * @return boolean              TRUE if message can is a bounce, FALSE otherwise.
	 */
	public function isBounceFromHeader($headerArray) {
		if (!isset($headerArray['subject'])) {
			throw new \InvalidArgumentException('Subject header must be present');
		}
		$subject = strtolower($headerArray['subject']);
		$bounce_matches = 'mail delivery failed|failure notice|warning: message|delivery status notification|delivery failure|delivery problem|returned mail|undeliverable|returned mail|delivery errors|mail status report|mail system error|failure delivery|delivery notification|delivery has failed|undelivered mail|returned email|returning message to sender|returned to sender|message delayed|mdaemon notification|mailserver notification|mail delivery system|mail transaction failed';
		if (preg_match('/'.$bounce_matches.'/', $subject)) {
			return true;
		}
		return false;
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
