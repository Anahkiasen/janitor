<?php
namespace Janitor\Exceptions;

use RuntimeException;

class UndefinedSubjectException extends RuntimeException
{
	/**
	 * @type string
	 */
	protected $message = "You haven't defined any files to analyze";
}
