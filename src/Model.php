<?php
namespace DirectScale;
use \DirectScale\Exception as DSException;
/**
 * @description    
 */
class Model extends \Nubersoft\nApp
{
    protected $headers, $errors, $response, $fullpath;
    private $obj = [];
    private static $Client;
    const DS    =   DIRECTORY_SEPARATOR;
    /**
     * @description    
     */
    public final function getEndpoint() : ?string
    {
        return self::getHttpClient()->getUrl();
    }
    /**
     * @description    
     */
    public final function normalizeKeys(array $array) : array
    {
        $new    =    [];
        foreach($array as $key => $value) {
            $vkey    =    ltrim(implode('_', array_map('strtolower', preg_split('/(?=[A-Z])/', $key))), '_');
            
            if(preg_match('/_i_d$/', $vkey)) {
                $vkey    =    str_replace(['distributor_id'],['distid'],preg_replace('/([A-Z_]+)(_i_d)$/i', '$1', $vkey).'_id');
            }
            elseif(preg_match('/_c_v$/', $vkey)) {
                $vkey    =    preg_replace('/([A-Z_]+)(_c_v)$/i', '$1', $vkey).'_cv';
            }
            elseif(preg_match('/_q_v$/', $vkey)) {
                $vkey    =    preg_replace('/([A-Z_]+)(_q_v)$/i', '$1', $vkey).'_qv';
            }
            else {
                switch($vkey) {
                    case('s_k_u'):
                        $vkey    =    'sku';
                        break;
                    case('i_d'):
                        $vkey    =    'id';
                        break;
                    case('q_v'):
                        $vkey    =    'qv';
                        break;
                    case('c_v'):
                        $vkey    =    'cv';
                        break;
                }
            }
            if(!is_array($value)) {
                $new[$vkey]    =    $value;
            }
            else {
                $new[$vkey]    =    $this->normalizeKeys($value);
            }
        }
        
        return $new;
    }
    /**
     * @description    
     */
    public final function getResourceFile($file)
    {
        $DS =   self::DS;
        $file = realpath(__DIR__."{$DS}..{$DS}resources").$DS.ltrim($file, $DS);
        return (is_file($file))? $file : false;
    }
    /**
     * @description    
     */
    public final function formatReturn($string, $to_array = true)
    {
        if(empty($string))
            return [];
        
        return (is_string($string))? $this->normalizeKeys(json_decode($string, $to_array)) : $string;
    }
    /**
     * @description    
     */
    public final function getFullPath() : ?string
    {
        return $this->enc($this->fullpath);
    }
    /**
     * @description    
     */
    public final function getVal(array $array, string $key, $default = null)
    {
        return (isset($array[$key]))? $array[$key] : $default;
    }
    /**
     * @description    
     */
    public final static function setHttpClient(IClient $Client)
    {
        self::$Client   =   $Client;
    }
    /**
     * @description    
     */
    public final function getHttpClient() : IClient
    {
        if(empty(self::$Client)) {
            self::$Client    =    new Client();
        }
        
        return self::$Client;
    }
    /**
     * @description    
     */
    public    function doTry($func)
    {
        if(!is_callable($func))
            throw new DSException("You must use a function to encapsulate your call.");
        # Throws an exception at the base model level, so has to be caught here
        try {
            return $func();
        }
        catch (DSException $e) {
            return false;
        }
    }
	/**
	 *	@description	
	 */
	public function xmlToWorkflow($string)
	{
        $file   =   (is_string($string))? simplexml_load_file($this->getResourceFile($string)) : $string;
        
        $result =   [];
        foreach($file as $key => $row) {
            
            $attr   =   $row->attributes();
            
            if($attr)
                $attr   =   json_decode(json_encode($attr), 1);
                                        
            $count  =   $row->count();
            
            if($count) {
                 $result[$row->getName()]    =   $this->xmlToWorkflow($row->children());
                
                if($attr)
                    $result[$row->getName()]    =   array_merge($result[$row->getName()], $attr);
            }
            else {
                if($attr) {
                    $result[$row->getName()]  = array_merge([$row->__toString()], $attr);
                }
                else {
                    $result[$row->getName()]    =   $row->__toString();
                }
            }
        }
        
        return $result;
	}
    /**
     * @description    
     */
    public    function __toString()
    {
        return json_encode($this->data);
    }
    /**
     * @description    
     */
    public final function __call($method, $args =  false)
    {
        $class    =    '\\DirectScale\\'.str_replace(' ','\\',str_replace("_"," ",preg_replace('/^get_/','',$method)));
        return    (is_array($args) && !empty($args))? new $class(...$args) : new $class();
    }
}