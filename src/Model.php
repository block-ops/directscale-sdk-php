<?php
namespace DirectScale;
/**
 *	@description	
 */
class Model extends \Nubersoft\nApp
{
	protected	$headers, $errors, $response, $fullpath;
	protected	static	$apikey;
	protected	static	$con;
	protected	static	$url		=	false;
	protected	static	$version	=	'v1';
	protected	static	$env		=	'';
	private		$obj				=	[];
	private		static	$Client;
	/**
	 *	@description	
	 */
	public	function __construct()
	{
		//self::setUrl();
	}
	/**
	 *	@description	
	 */
	public final static	function setApiKey($key)
	{
		self::$apikey	=	$key;
	}
	/**
	 *	@description	
	 */
	protected final static	function getApiKey()
	{
		return (!empty(self::$apikey))? self::$apikey : constant("DIRECTSCALE_".strtoupper(self::$env)."APIKEY");
	}
	/**
	 *	@description	
	 */
	public final static function setUrl()
	{	
		$type		=	(!empty(self::$env))? "-".strtolower(self::$env) : self::$env;
		self::$url	=	"https://dsapi{$type}.directscale.com/".self::$version."/";
	}
	/**
	 *	@description	
	 */
	public final static function setVersion($version) : ?string
	{
		self::$version	=	$version;
	}
	/**
	 *	@description	
	 */
	public final static function setMode($env)
	{
		self::$env	=	$env;
	}
	/**
	 *	@description	
	 */
	public final function getEndpoint() : ?string
	{
		return self::getClient()->getUrl();
	}
	/**
	 *	@description	
	 */
	public final function normalizeKeys(array $array) : array
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
	public final function getResourceFile($file)
	{
		return (is_file($file = realpath(__DIR__.DS.'..'.DS.'resources').DS.ltrim($file, DS)))? $file : false;
	}
	/**
	 *	@description	
	 */
	public final function formatReturn($string, $to_array = true)
	{
		if(empty($string))
			return [];
		
		return (is_string($string))? $this->normalizeKeys(json_decode($string, $to_array)) : $string;
	}
	/**
	 *	@description	
	 */
	public final function getFullPath() : ?string
	{
		return $this->enc($this->fullpath);
	}
	/**
	 *	@description	
	 */
	public	function __toString()
	{
		return json_encode($this->data);
	}
	/**
	 *	@description	
	 */
	public final function __call($method, $args =  false)
	{
		$class	=	'\\DirectScale\\'.str_replace(' ','\\',str_replace("_"," ",preg_replace('/^get_/','',$method)));
		return	(is_array($args) && !empty($args))? new $class(...$args) : new $class();
	}
	/**
	 *	@description	
	 */
	public final function getVal($array, $key, $default = null)
	{
		return (isset($array[$key]))? $array[$key] : $default;
	}
	/**
	 *	@description	
	 */
	public final static	function setClient(IClient $Client)
	{
		self::$Client;
	}
	/**
	 *	@description	
	 */
	public final function getClient() : IClient
	{
		if(empty(self::$Client)) {
			
			if(empty(self::$url))
				self::setUrl();
			
			self::$Client	=	new Client(self::$url, self::getApiKey());
		}
		
		return self::$Client;
	}
}