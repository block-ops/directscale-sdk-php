<?php
namespace DirectScale\Architect;

use \DirectScale\ {
    Filter,
    Model,
    IArchitect,
    Exception as DSException
};
/**
 * @description    
 */
class User extends Model implements IArchitect
{
    /**
     * @description    
     */
    public function create()
    {
        $args   =   func_get_args();
        $data   =   ($args[0])?? null;
        # Stop if no data is present
        if(!$data)
            throw new DSException("Creation data is required");
        # Fetch the user create map
        $map    =   $this->xmlToWorkflow("creation".DS."user.xml");
        # Parse all the fields from the template
        $settings   =   (new Filter())->multiLevelMapper($data, $map, $data);
        # Throw into the call
        return $this->getHttpClient()->doPost('customers/', $settings);
    }
    /**
     * @description    
     */
    public  function exists()
    {
        $args   =   func_get_args();
        $username   =   ($args[0])?? null;
        $type   =   ($args[1])?? 'username';
        
        if(!$username)
            throw new DSException("Can not be empty username");
        
        return $this->getHttpClient()->doGet("validate/{$type}/{$username}");
    }
	/**
	 *	@description	
	 */
	public	function delete()
	{
        $args   =   func_get_args();
        $uid   =   ($args[0])?? null;
        if(!$uid)
            throw new DSException("Can not be empty user id");
        
        return $this->getHttpClient()->doDelete("customers/{$uid}");
	}
	/**
	 *	@description	
	 */
	public	function getTypeByTitle($name)
	{
        $name   =   strtolower($name);
        
        if(in_array($name, ['distributor', 'member']))
            return 1;
        elseif(in_array($name, ['customer', 'retail']))
            return 2;
        
        throw new DSException("You need a name (string) to fetch the type numeric.");
	}
}