<?php

namespace App\Exceptions;

abstract class AbstractException extends \Exception
{
	protected $statusCode;
	protected $errors;

	public function __construct(
		$statusCode, $errors, $message, $code = 0, \Exception $previous = null) {
		parent::__construct($message, $code, $previous);

		$this->statusCode = $statusCode;
		$this->errors = $errors;
	}

	public function getStatusCode() 
	{
		return $this->statusCode;
	}

	public function getErrors() 
	{
		return $this->errors;
	}
}