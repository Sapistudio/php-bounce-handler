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
    // bounce part of parsing
    $message->setStatusCode($this->parseStatusCode($message->getBody()));
		$headers = $this->parseHeaders($message->getHeader());
		if (isset($headers['subject']) && $this->isBounceFromSubject($headers['subject'])) {
      switch ($this->isBounceFromStatusCode($message->getStatusCode())) {
        case 'hard':
          $message->setStatus(Message::STATUS_HARD_BOUNCE);
          break;
        case 'soft':
          $message->setStatus(Message::STATUS_SOFT_BOUNCE);
        break;
        default:
          $message->setStatus(Message::STATUS_UNKNOWN);
      }
		}

    // recipient part of parsing
    // for failed mail delivery bounces we need to fetch original message headers from body
    $original_message_headers = $this->parseHeaders($message->getBody());
    if (isset($original_message_headers['to'])) {
      // message parsed successfully
      $message->setRecipient($original_message_headers['to']);
    }
		return $message;
	}

	/**
	 * Checks if given sibject can tell if the message is a bounced one.
	 * @param  array   $subject     Subject of the message
	 * @return boolean              TRUE if message is a bounce, FALSE otherwise.
	 */
	public function isBounceFromSubject($subject) {
		$subject = strtolower($subject);
		$bounce_matches = 'mail delivery failed|failure notice|warning: message|delivery status notification|delivery failure|delivery problem|returned mail|undeliverable|returned mail|delivery errors|mail status report|mail system error|failure delivery|delivery notification|delivery has failed|undelivered mail|returned email|returning message to sender|returned to sender|message delayed|mdaemon notification|mailserver notification|mail delivery system|mail transaction failed';
		if (preg_match('/'.$bounce_matches.'/', $subject)) {
			return true;
		}
		return false;
	}

  /**
	 * Checks if given status code is a bounced one.
	 * @param  string   $code       Status code of the message in a RFC 3463 form
	 * @return mixed               'hard' or 'soft' if message is a bounce, FALSE otherwise.
	 */
	public function isBounceFromStatusCode($code) {
    if (!$code) return false;
    $code = substr($code, 0, 1);
		if ($code === '5') {
			return 'hard';
		}
    if ($code === '4') {
			return 'soft';
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

  /**
	 * Parses email body and tries to extract RFC 3463 email status code..
	 * @param  string $body    The body of email
	 * @return string          A string representation of RFC 3463 email status code or empty string if status code could not be found.
	 */
	protected function parseStatusCode($body) {
		$result = [];
    if (preg_match('/status: ([2|4|5]\.[0-999]+\.[0-999]+)/i', $body, $result)) {
      return $result[1];
    }
    return '';
	}
}
