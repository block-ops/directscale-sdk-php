<?php
namespace DirectScale\User;

use \DirectScale\ {
    Exception as DSException,
    IArchitect,
    Model
};
/**
 * @description    
 */
class Subscription extends Model implements IArchitect
{
    private $User, $data, $autoships;
    /**
     * @description    
     */
    public    function __construct(\DirectScale\User $User)
    {
        $this->User    =    $User;
        $this->data    =    (empty($this->User->getData()))? $this->User->getDistInfo() : $this->User->getData();
        
        if(!$this->exists()) {
            return false;
        }
        
        $this->autoships    =    $this->getHttpClient()->doGet('customers/'.$this->data['general']['uid'].'/autoships');
        $this->User->setAttr('autoship', (empty($this->autoships))? [] : $this->autoships);
    }
	/**
	 *	@description	
	 */
	public	function exists()
	{
        return (!empty($this->data['general']['uid']));
	}
	/**
	 *	@description	
	 */
	public	function create()
	{
        
	}
    /**
     * @description    
     */
    public    function delete()
    {
        $args   =   func_get_args();
        $id = ($args[0])?? false;
        
        $count  =   count($this->autoships);
        if($count == 1) {
            # Delete
            $this->getHttpClient()->doDelete('orders/autoship/'.$this->autoships[0]['id']);
            # Reset the autoships
            $this->__construct($this->User);
        }
        elseif($count > 1) {
            foreach($this->autoships as $key => $as) {
                # See if id is supplied and matches
                $idmatch    =    (!empty($id) && ($as['id'] == $id));
                # See if an id match OR there is no specific id to delete
                if($idmatch || empty($id)) {
                    # Delete the autoship if set
                    $this->User->getHttpClient()->doDelete('orders/autoship/'.$as['id']);
                    # Stop if an id is supplied
                    if(!empty($id))
                        break;
                }
            }
            # Reset the autoships
            $this->__construct($this->User);
        }
        
        return $this->autoships;
    }
    /**
     * @description    
     */
    public    function getOrder()
    {
        return $this->autoships;
    }
}