<?php
namespace DirectScale\User;

use \DirectScale\ {
    IArchitect,
    Model,
    User
};
/**
 * @description    
 */
class Auth extends Model
{
    protected   $Architect;
    /**
	 *	@description	
	 */
	public	function __construct(IArchitect $Architect)
	{
        $this->Architect    =   $Architect;
	}
    /**
     * @description    
     */
    public    function validate(string $username, string $password) : bool
    {
        return $this->doTry(function() use ($username, $password) {
            return $this->getHttpClient()->doGet('validate/login', [
                'username' => $username,
                'password' => $password
            ]);
        });
    }
    /**
     * @description    
     */
    public    function usernameExists($username) : bool
    {
        return $this->doTry(function() use ($username) {
            return $this->Architect->exists($username);
        });
    }
    /**
     * @description    
     */
    public    function emailExists($email) : bool
    {
        return $this->doTry(function() use ($email){
            return $this->Architect->exists($email, 'email-address');
        }) == 'True';
    }
	/**
	 *	@description	
	 */
	public	function distidExists(User $User)
	{
        return $User->getDistInfo();
	}
}