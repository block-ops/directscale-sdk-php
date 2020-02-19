<?php
namespace DirectScale;
/**
 *	@description	
 */
class Model extends \Nubersoft\nApp
{
	protected	static	$con;
	private		static	$url		=	false;
	private		static	$version	=	'v1';
	private		static	$env		=	'';
	/**
	 *	@description	
	 */
	public	function __construct()
	{
		self::setUrl();
		self::$con	=	new \Nubersoft\nRemote(self::$url, false);
		# Create a define() for the API in your application key like:
		# define('DIRECTSCALE_APIKEY', 'thekey123goeshere321');
		self::$con->addHeader(...[
			'Ocp-Apim-Subscription-Key',
			constant("DIRECTSCALE_".strtoupper(self::$env)."APIKEY")
		]);
	}
	/**
	 *	@description	
	 */
	public	static function setUrl()
	{	
		$type		=	(!empty(self::$env))? "-".strtolower(self::$env) : self::$env;
		self::$url	=	"https://dsapi{$type}.directscale.com/".self::$version."/";
	}
	/**
	 *	@description	
	 */
	public	static function setVersion($version)
	{
		self::$version	=	$version;
	}
	/**
	 *	@description	
	 */
	public	static function setMode($env)
	{
		self::$env	=	$env;
	}
	/**
	 *	@description	
	 */
	public	function doService($path, $attr = false, $type = 'get', $func = false)
	{
		$data	=	self::$con->{__FUNCTION__}($path)->query($attr, false, $type)->getResults(false);
		
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
	/**
	 *	@description	
	 */
	public	function getEndpoint()
	{
		return $this->url;
	}
	/**
	 *	@description	
	 */
	public	function normalizeKeys($array)
	{
		$new	=	[];
		foreach($array as $key => $value) {
			$vkey	=	ltrim(implode('_', array_map('strtolower', preg_split('/(?=[A-Z])/', $key))), '_');
			if(!is_array($value)) {
				$new[$vkey]	=	$value;
			}
			else {
				$new[$vkey]	=	$this->normalizeKeys($value);
			}
		}
		
		return $new;
	}
	/**
	 *	@description	
	 */
	public	function getResourceFile($file)
	{
		return (is_file($file = realpath(__DIR__.DS.'..'.DS.'resources').DS.ltrim($file, DS)))? $file : false;
	}
}