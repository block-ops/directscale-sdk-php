<?php
namespace DirectScale;
/**
 * @description    
 */
class Genealogy extends Model
{
    protected    $data, $User;
    private $ids;
    /**
     * @description    
     */
    public    function __construct(User $User)
    {
        $this->User    =    $User;
    }
    /**
     * @description    
     */
    public    function getDownline($start = 1, $end = false)
    {
        $this->fillIds();
        
        if(empty($this->ids)) {
            return $this->data    =    [];
        }
        
        $i  =   1;
        foreach($this->ids as $id) {
            
            if($start != 1) {
                if($start > $i) {
                    $i++;
                    continue;
                }
            }
            
            if(is_numeric($end)) {
                if($i > $end) {
                    break;
                }
            }
            
            $this->getDistInfoFromId($id, false);
            
            $i++;
        }
        
        return $this->data;
    }
    /**
     * @description    
     */
    public    function getDownlineIds()
    {
        $data    =    $this->User->getDistInfo();
        
        if(empty($data['general']['uid']))
            return [];
        
        $data    =    $this->getHttpClient()->doGet('customers/GetDownlineIds', [
            'associateId' => $data['general']['uid']
        ]);
        
        return $this->ids   =   (!empty($data))? $this->formatReturn($data) : [];
    }
	/**
	 *	@description	
	 */
	public	function inDownLine($id) : bool
	{
        $this->fillIds();
        
        if(empty($this->ids))
            return false;
        
        return in_array($id, $this->ids);
	}
	/**
	 *	@description	
	 */
	protected	function fillIds()
	{
        if(empty($this->ids))
            $this->ids    =    $this->getDownlineIds();
        
        return $this;
	}
	/**
	 *	@description	
	 */
	public	function getDistInfoFromId($id, $clear = true)
	{
        if(!is_array($id))
            $id =   [$id];
        
        if($clear || empty($this->ids))
            $this->data =   [];
        
        if(empty($this->ids))
            return $this;
        
        foreach($id as $idf) {
            if(!in_array($idf, $this->ids))
                continue;
            $User   =   new User($idf, true);
            $this->data[]   =   $User->getDistInfo(true);
        }
        
        return $this;
	}
	/**
	 *	@description	
	 */
	public	function __call($method, $args = false)
	{
        switch(strtolower($method)) {
            case('get'):
                if(!empty($this->ids)) {
                    $search =   (!empty($args[0]));
                    $id =   ($search && (is_numeric($args[0]) || is_array($args[0])))? $args[0] : $this->ids;
                    return $this->getDistInfoFromId($id, true)->data;
                }
                return [];
            case('getcount'):
                return (is_array($this->ids))? count($this->ids) : 0;
            case('getdata'):
                return $this->data;
            case('getall'):
                return [
                    'data' => $this->data,
                    'ids' => $this->ids,
                    'count' => $this->getCount()
                ];
        }
	}
}