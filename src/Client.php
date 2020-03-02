<?php
namespace DirectScale;
/**
 * @description    
 */
class Client implements IClient
{
    private    $apikey, $fullpath, $response, $headers, $errors, $url;
    /**
     * @description    
     */
    public    function __construct(string $url, string $apikey)
    {
        $this->url    =    $url;
        $this->apikey    =    $apikey;
    }
    /**
     * @description    
     */
    public    function getAttr($type)
    {
        return (isset($this->{$attr}))? $this->{$attr} : false;
    }
    /**
     * @description    
     */
    public    function doService()
    {
        $args    =    func_get_args();
        
        $path    =    ($args[0])?? false;
        $attr    =    ($args[1])?? false;
        $type    =    ($args[2])?? 'get';
        $func    =    ($args[3])?? false;
        
        if(empty($path))
            throw new Exception("Service path can not be left empty.");
        
        if($type != 'get') {
            $content    =    (!empty($attr))? 'Content-type: application/json' : null;
        }
        
        $opts = [
            "http" => [
                'ignore_errors' => true,
                "method" => strtoupper($type),
                "header" => implode(PHP_EOL, [
                    'Ocp-Apim-Subscription-Key: '.$this->apikey,
                    # Only create content if not doing a get
                    ((!empty($content))? $content : null)
                    
                ])
            ]
        ];
        
        # Save json if not doing get
        if(!empty($attr) && $type != 'get')
            $opts['http']['content']    =    json_encode($attr);
        
        $attr_str    =    ($type == 'get' && !empty($attr))? '?'.http_build_query($attr) : '';
        # Fetch
        $this->response    =    file_get_contents(...[
            $this->fullpath = $this->url.$path.$attr_str,
            false,
            stream_context_create($opts)
        ]);
        
        $this->headers    =    $http_response_header;
        
        $this->errors    =    false;
        
        foreach($this->headers as $hds) {
            if(preg_match('/X-DirectScale-Message/i', $hds)) {
                $this->errors    =    str_replace('X-DirectScale-Message:', '', $hds);
            }
        }
        
        if(!empty($this->errors)) {
            if(empty($this->response))
                $this->response    =    "[]";
            
            throw new Exception(json_encode(array_merge(['msg' => $this->errors], json_decode($this->response, 1))));
        }
        
        return (is_callable($func))? $func($this->response) : $this->response;
    }
    /**
     * @description    
     */
    public    function getErrors()
    {
        return $this->errors;
    }
    /**
     * @description    
     */
    public    function getResponseHeaders()
    {
        return $this->headers;
    }
    /**
     * @description    
     */
    public    function getUrl()
    {
        return $this->fullpath;
    }
    /**
     * @description    
     */
    public    function doPost($path, $attr = false, $func = false)
    {
        return $this->doService($path, $attr, 'post', $func);
    }
    /**
     * @description    
     */
    public    function doGet($path, $attr = false, $func = false)
    {
        # Send get
        return $this->doService($path, $attr, 'get', $func);
    }
    /**
     * @description    
     */
    public    function doDelete($path, $attr = false, $func = false)
    {
        return $this->doService($path, $attr, 'delete', $func);
    }
    /**
     * @description    
     */
    public    function doPut($path, $attr = false, $func = false)
    {
        return $this->doService($path, $attr, 'put', $func);
    }
    /**
     * @description    
     */
    public    function doPatch($path, $attr = false, $func = false)
    {
        return $this->doService($path, $attr, 'patch', $func);
    }
}