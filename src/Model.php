<?php
namespace DirectScale;
/**
 *	@description	
 */
class Model extends \Nubersoft\nApp
{
	protected	static	$con;
	protected	static	$url		=	false;
	protected	static	$version	=	'v1';
	protected	static	$env		=	'';
	/**
	 *	@description	
	 */
	public	function __construct()
	{
		self::setUrl();
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
		self::$con	=	new \Nubersoft\nRemote(self::$url, false);
		# Create a define() for the API in your application key like:
		# define('DIRECTSCALE_APIKEY', 'thekey123goeshere321');
		self::$con->addHeader(...[
			'Ocp-Apim-Subscription-Key',
			constant("DIRECTSCALE_".strtoupper(self::$env)."APIKEY")
		]);
		
		$data	=	self::$con->{__FUNCTION__}($path)->query($attr, false, $type)->getResults(false);
		
		if(preg_match('/statusCode/', $data)) {
			throw new Exception($data);
		}
		
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
		# Append query string
		if($attr)
			$path	.=	'?'.http_build_query($attr);
		# Send get
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
		return self::$url;
	}
	/**
	 *	@description	
	 */
	public	function normalizeKeys($array)
	{
		$new	=	[];
		foreach($array as $key => $value) {
			$vkey	=	ltrim(implode('_', array_map('strtolower', preg_split('/(?=[A-Z])/', $key))), '_');
			
			if(preg_match('/_i_d$/', $vkey)) {
				$vkey	=	str_replace(['distributor_id'],['distid'],preg_replace('/([A-Z_]+)(_i_d)$/i', '$1', $vkey).'_id');
			}
			elseif(preg_match('/_c_v$/', $vkey)) {
				$vkey	=	preg_replace('/([A-Z_]+)(_c_v)$/i', '$1', $vkey).'_cv';
			}
			elseif(preg_match('/_q_v$/', $vkey)) {
				$vkey	=	preg_replace('/([A-Z_]+)(_q_v)$/i', '$1', $vkey).'_qv';
			}
			else {
				switch($vkey) {
					case('s_k_u'):
						$vkey	=	'sku';
						break;
					case('i_d'):
						$vkey	=	'id';
						break;
					case('q_v'):
						$vkey	=	'qv';
						break;
					case('c_v'):
						$vkey	=	'cv';
						break;
				}
			}
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
	/**
	 *	@description	
	 */
	public	function formatReturn($string, $array = true)
	{
		if(empty($string))
			return [];
		
		return $this->normalizeKeys(json_decode($string, $array));
	}
}