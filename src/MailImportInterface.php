<?php 

namespace Malas\BounceHandler;

/**
 * Interface for defining MailImport classes. 
 */
interface MailImportInterface {

	/**
	 * Returns imported from whatever source Mail array.
	 * @return array An array of imported Mail objects.
	 */
	public function import();
}
