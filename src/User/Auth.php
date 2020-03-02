<?php
namespace DirectScale\User;
/**
 *	@description	
 */
class Auth extends \DirectScale\User
{
	/**
	 *	@description	
	 */
	public	function __construct()
	{
		$args	=	func_get_args();
		if(!empty($args)) {
			parent::__construct(...$args);
		}
	}
	/**
	 *	@description	
	 */
	public	function validate($password) : bool
	{
		return $this->doTry(function() use ($password) {
			return $this->getClient()->doGet('validate/login', [
				'username' => $this->createDataSet()->getData('user')['username'],
				'password' => $password
			]);
		});
	}
	/**
	 *	@description	
	 */
	public	function usernameExists($username) : bool
	{
		return $this->doTry(function() use ($username) {
			return $this->Architect->usernameExists($username);
		});
	}
	/**
	 *	@description	
	 */
	public	function emailExists($email)
	{
		return $this->doTry(function() use ($email){
			return $this->getClient()->doGet("validate/email-address/{$email}");
		}) == 'True';
	}
	/**
	 *	@description	
	 */
	public	function doTry($func)
	{
		# Throws an exception at the base model level, so has to be caught here
		try {
			return $func();
		}
		catch (\DirectScale\Exception $e) {
			return false;
		}
	}
}