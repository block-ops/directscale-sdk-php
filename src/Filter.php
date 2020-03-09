<?php
namespace DirectScale;
/**
 *	@description	
 */
class Filter extends \Nubersoft\nMarkUp
{
	/**
	 *	@description	
	 */
	public	static  function toWebAlias()
	{
        $args   =   func_get_args();
        return preg_replace('/[^\d\w]/', '', $args[2]).time();
	}
	/**
	 *	@description	
	 */
	public	function multiLevelMapper(array $data, array $map, array $master)
	{
        $new    =   [];
        foreach($map as $key => $value) {
            
            if(is_string($value)) {
                $new[$key]  =   $this->replaceHolder($data, $value);
            }
            else {
                if(isset($value['@attributes'])) {
                    
                    if(!empty($value[0])) {
                        $new[$key]  =   $this->attributesParse(...[
                            $key,
                            $this->replaceHolder($data, $value[0]),
                            $value['@attributes'],
                            $master
                        ]);
                    }
                    else {
                        $new[$key]  =   $this->multiLevelMapper($value, $map, $master);
                    }
                }
                else {
                    $new[$key]  =   $this->multiLevelMapper($data, $value, $master);
                }
            }
        }
        
        return $new;
	}
	/**
	 *	@description	
	 */
	public function replaceHolder($data, $value)
	{
        return preg_replace_callback('/~[^~]+~/', function($v) use ($data){
            $key    =   trim($v[0], '~');
            return ($data[$key])?? null;
        }, $value);
	}
	/**
	 *	@description	
	 */
	public	function attributesParse($key, $value, $attributes, $data)
	{
        foreach($attributes as $type => $settings) {
            if($type == 'alt') {
                if(!(stripos($settings, '~') !== false) && !(stripos($settings, '::') !== false)) {
                    switch($settings){
                        case('true'):
                            $value  = true;
                            break;
                        case('false'):
                            $value  =   false;
                            break;
                        default:
                            $value  = $settings;
                    }
                }
                elseif(stripos($settings, '~') !== false) {
                    $value  =   $this->replaceHolder($data, $settings);
                }
                elseif(stripos($settings, '::') !== false) {
                    $value  =   $this->parseToCallable($settings, $key, $value, $data);
                }
            }
            elseif($type == 'validate') {
                if(!is_array($settings))
                    $settings   =   [$settings];
                
                foreach($settings as $do_vaction) {
                    switch($do_vaction) {
                        case('numeric'):
                            if(!is_numeric($value))
                                throw new \DirectScale\Exception("{$key} must be numeric.");
                            break;
                        case('email'):
                            
                            if(!filter_var($value, FILTER_VALIDATE_EMAIL))
                                throw new \DirectScale\Exception("{$key} must be an email.");
                            break;
                    }
                }
            }
            elseif($type == 'filter') {
                if(stripos($settings, '::') !== false) {
                    $value  =   $this->parseToCallable($settings, $key, $value, $data);
                }
            }
        }
        
        return $value;
	}
    	/**
	 *	@description	
	 */
	public	function parseToCallable($settings, $key = false, $value = false, $data = false)
	{
        if(stripos($settings, 'CLASS::') !== false) {
            $objstr =   ltrim($settings, 'CLASS::');
            $exp    =   explode('->', $objstr);
            $class  =   str_replace('/','\\', $exp[0]);
            $method  =   $exp[1];
            return (new $class())->{$method}(...func_get_args());
        }
        else {
            if(stripos($settings, 'FUNC::') !== false && !preg_match('/\[/', $settings)) {
                $func   =   str_replace('FUNC::','',$settings);
                return $func($value);
            }
            
            return $this->useMarkUp('~'.$settings.'~');
        }
	}
}