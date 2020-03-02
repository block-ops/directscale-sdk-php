<?php
namespace DirectScale;
/**
 * @description    
 */
class Genealogy extends Model
{
    protected    $data, $User;
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
    public    function getDownline()
    {
        $ids    =    $this->getDownlineIds();
        
        if(empty($ids)) {
            return $this->data    =    [];
        }
        
        foreach($ids as $id) {
            $User            =    new User($id, true);
            $this->data[]    =    $User->getDistInfo(true);
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
        
        $data    =    $this->getClient()->doGet('customers/GetDownlineIds', [
            'associateId' => $data['general']['uid']
        ]);
        
        return (!empty($data))? $this->formatReturn($data) : [];
    }
}