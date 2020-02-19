<?php
namespace DirectScale\User;
/**
 *	@description	
 */
class Subscription
{
	private	$User, $data;
	/**
	 *	@description	
	 */
	public	function __construct(\DirectScale\User $User)
	{
		$this->User	=	$User;
		$this->data	=	(empty($this->User->getData()))? $this->User->getDistInfo(false) : $this->User->getData();
		
		
		if(!empty($this->data[0]['CustomerId'])) {
			return false;
		}
	}
	/**
	 *	@description	
	 */
	public	function getOrder()
	{
		if(empty($this->data[0]['CustomerId']))
			return false;
		
		$autoships	=	$this->User->doGet('customers/'.$this->data[0]['CustomerId'].'/autoships');
		$this->User->setAttr('autoship', (empty($autoships))? [] : $autoships);
		return $autoships;
	}
}