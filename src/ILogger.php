<?php
namespace DirectScale;
/**
 *	@description	
 */
abstract class ILogger
{
    protected   static  $paths;
	/**
	 *	@description	
	 */
	public	function error($msg)
    {
    }
	/**
	 *	@description	
	 */
	public	function warning($msg)
    {
    }
	/**
	 *	@description	
	 */
	public	function addPath($path, $name)
    {
        self::$paths[$name] =   $path;
        return $this;
    }
}