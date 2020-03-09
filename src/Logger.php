<?php
namespace DirectScale;
/**
 *	@description	
 */
class Logger extends ILogger
{
	/**
	 *	@description	
	 */
	public	function error($msg)
    {
        $this->write(__FUNCTION__, $msg);
        return $this;
    }
	/**
	 *	@description	
	 */
	public	function warning($msg)
    {
        $this->write(__FUNCTION__, $msg);
        return $this;
    }
	/**
	 *	@description	
	 */
	public	function addPath($path, $name)
    {
        self::$paths[$name] =   $path;
        return $this;
    }
	/**
	 *	@description	
	 */
	public	function removeFile($name)
	{
        if(is_file(self::$paths[$name]))
            unlink(self::$paths[$name]);
        
        return is_file(self::$paths[$name]);
	}
	/**
	 *	@description	
	 */
	private	function write($name, $msg)
	{
        file_put_contents(self::$paths[$name], $msg.PHP_EOL, FILE_APPEND);
	}
}