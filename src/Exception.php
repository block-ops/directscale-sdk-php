<?php
namespace DirectScale;
/**
 *	@description	
 */
class Exception extends \Exception
{
	/**
	 *	@description	
	 */
	public	function getErrorData()
	{
		return json_decode($this->getMessage(), 1);
	}
	/**
	 *	@description	
	 */
	public	function getErrorMessage()
	{
		$data	=	$this->getErrorData();
		return $data['msg'];
	}
	/**
	 *	@description	
	 */
	public	function getErrorCode()
	{
		$data	=	$this->getErrorData();
		return $data['statusCode'];
	}
	/**
	 *	@description	
	 */
	public	function getErrorTransactionId()
	{
		$data	=	$this->getErrorData();
		return $data['activityId'];
	}
	/**
	 *	@description	
	 */
	public	function __toSting()
	{
		$data	=	$this->getErrorData();
		return $data['msg'];
	}
}