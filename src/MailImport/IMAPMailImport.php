<?php 

namespace Malas\BounceHandler\MailImport;

use Malas\BounceHandler\MailImportInterface;
use Malas\BounceHandler\Model\Message;

/**
 * IMAP mail import implementation of MailImportInterface. All parameters are direct match with imap_open function. See http://php.net/manual/en/function.imap-open.php for more details.
 */
class IMAPMailImport implements MailImportInterface {

	/**
	 * Mailbox option. For example "{imap.example.org:143}".
	 * @var String
	 */
	protected $mailbox;

	protected $username;

	protected $password;

	/**
	 * Marks if fetched messages should be deleted from mailbox.
	 * @var boolean
	 */
	protected $delete_mail;

	/**
	 * IMAP options. For example CL_EXPUNGE.
	 */
	protected $options;

	public function __construct($options) {
		if (!isset($options['mailbox'])) throw new \InvalidArgumentException('Mailbox must be defined');
		$this->mailbox = $options['mailbox'];
		$this->username = isset($options['username']) ? $options['username'] : '';
		$this->password = isset($options['password']) ? $options['password'] : '';
		$this->options = isset($options['options']) ? $options['options'] : null;
		$this->delete_mail = isset($options['delete_mail']) ? $options['delete_mail'] : false;
	}

	public function import($parse_limit = 100) {
		$imap = $this->connect();
		$mails = $this->getMails($imap, $parse_limit);
		return $mails;
	}

	protected function connect() {
        $imap = @imap_open($this->getMailbox(), $this->getUsername(), $this->getPassword(), $this->getOptions());
        if (!$imap) {
        	throw new \InvalidArgumentException('Could not connect: '.imap_last_error());
        } 
        return $imap;
	}

	protected function getMails($imap, $limit) {        
        // maximum parsed messages should not exceed given $limit
        $total = imap_num_msg($imap);
        $limit = $total > $limit ? $limit : $total;
        
        $result = [];
        for ($i = 1; $i <= $limit; $i++) {
            $header = imap_fetchheader($imap, $i);
            $body = imap_body($imap, $i);
            $result[] = new Message($header, $body);
            if ($this->getDeleteMail()) {
            	imap_delete($imap, $i);
            }
        }
        if ($this->getDeleteMail()) {
        	imap_expunge($imap);
        }
        $error = imap_errors();
        // insecure connection should not print out notice.
		if (count($error) > 1 || $error[0] != 'SECURITY PROBLEM: insecure server advertised AUTH=PLAIN') {
		  // More than 1 error or not the expected error, throw an exception
		  throw new \Exception('IMAP error detected');
		}
        imap_close($imap);
        return $result;
	}

	public function getMailbox() {
		return $this->mailbox;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getPassword() {
		return $this->password;
	}
	public function getDeleteMail() {
		return $this->delete_mail;
	}

	public function getOptions() {
		return $this->options;
	}
}
