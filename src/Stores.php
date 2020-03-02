<?php
namespace DirectScale;
/**
 * @description    
 */
class Stores extends Model
{
    protected    $stores,$regions,$Products;
    /**
     * @description    
     */
    public    function __construct(Products $Products)
    {
        $this->Products    =    $Products;
        $stores            =    $this->getClient()->doGet('products/stores');
        $stores            =    (!empty($stores))? $this->formatReturn($stores) : [];
        
        foreach($stores as $store) {
            $this->stores[$store['id']]    =    $store['description'];
        }
        
        if(empty($this->stores))
            $this->stores    =    $stores;
    }
    /**
     * @description    
     */
    public    function getStores()
    {
        $args    =    func_get_args();
        
        if(!empty($args[0])) {
            if(is_numeric($args[0])) {
                return (isset($this->stores[$args[0]]))? $this->stores[$args[0]] : false;
            }
            elseif(is_string($args[0])) {
                $key    =    array_search($args[0], $this->stores);
                if($key === false)
                    return false;
                
                return $key;
            }
            else
                return false;
        }
        
        return $this->stores;
    }
    /**
     * @description    
     */
    public    function getStoreId($name)
    {
        $key    =    array_search($name, $this->stores);
        
        if($key !== false)
            return $key;
        
        return false;
    }
    /**
     * @description    
     */
    public    function getStoreName($id)
    {
        return (isset($this->stores[$id]))? $this->stores[$id] : false;
    }
    /**
     * @description    
     */
    public    function getCategories($sid = false)
    {
        # This will fetch all categories
        if(empty($sid)) {
            $new    =    [];
            foreach($this->getStores() as $sid => $name) {
                $cat    =    $this->getClient()->doGet("products/store/{$sid}/categories");
                if(empty($cat) || trim($cat) == '[]')
                    continue;
                
                $new[$sid]    =    $this->formatReturn($cat);
            }
            
            return $new;
        }
        # This will fetch a specfic category
        else {
            $data    =    $this->getClient()->doGet("products/store/{$sid}/categories");

            if(empty($data) || trim($data) == '[]') {
                return [];
            }

            return $this->formatReturn($data);
        }
    }
    /**
     * @description    
     */
    public    function getRegions()
    {
        $data    =    $this->getClient()->doGet('products/regions');
        
        if(empty($data)) {
            return $this->regions    =    [];
        }
        
        $this->regions    =    $this->formatReturn($data);
        
        usort($this->regions, function($a, $b){
            return ($a['id'] > $b['id']);
        });
        
        return $this->regions;
    }
}