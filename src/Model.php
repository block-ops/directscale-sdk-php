<?php
namespace DirectScale;
/**
 *	@description	
 */
class Model extends \Nubersoft\nApp
{
	protected	static	$con;
	private	$endpoint_dev	=	'dsapi-dev';
	/**
	 *	@description	
	 */
	public	function __construct($type = 'dev')
	{
		$url	=	'https://';
		switch($type){
			case('dev'):
				$url	.=	$this->endpoint_dev;
				break;
		}
		$url		.=	'.directscale.com/v1/';
		self::$con	=	new \Nubersoft\nRemote($url, false);
		# Create a define() for the API in your application key like:
		# define('DIRECTSCALE_APIKEY', 'thekey123goeshere321');
		self::$con->addHeader('Ocp-Apim-Subscription-Key', DIRECTSCALE_APIKEY);
	}
	/**
	 *	@description	
	 */
	public	function doService($path, $attr = false, $type = 'get', $func = false)
	{
		$data	=	self::$con->doService($path)->query($attr, false, $type)->getResults(false);
		
		return (is_callable($func))? $func($data) : $data;
	}
	/**
	 *	@description	
	 */
	public	function doPost($path, $attr = false, $func = false)
	{
		return $this->doService($path, $attr, 'post', $func);
	}
	/**
	 *	@description	
	 */
	public	function doGet($path, $attr = false, $func = false)
	{
		return $this->doService($path, $attr, 'get', $func);
	}
	/**
	 *	@description	
	 */
	public	function doDelete($path, $attr = false, $func = false)
	{
		return $this->doService($path, $attr, 'delete', $func);
	}
	/**
	 *	@description	
	 */
	public	function doPut($path, $attr = false, $func = false)
	{
		return $this->doService($path, $attr, 'put', $func);
	}
	/**
	 *	@description	
	 */
	public	function doPatch($path, $attr = false, $func = false)
	{
		return $this->doService($path, $attr, 'patch', $func);
	}
}