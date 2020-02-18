<?php
namespace DirectScale;
/**
 *	@description	
 */
class User extends Model
{
	protected	$distid;
	/**
	 *	@description	
	 */
	public	function __construct()
	{
		$args			=	func_get_args();
		if(empty($args[0]))
			throw new \Exception('A Distributor Id is required in the construct');
		
		$this->distid	=	$args[0];
		
		parent::__construct();
	}
	/**
	 *	@description	
	 */
	public	function getDistInfo()
	{
		return $this->doGet('customers/'.$this->distid);
	}
}