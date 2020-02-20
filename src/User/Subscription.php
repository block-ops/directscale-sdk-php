<?php
namespace DirectScale\User;
/**
 *	@description	
 */
class Subscription
{
	private	$User, $data, $autoships;
	/**
	 *	@description	
	 */
	public	function __construct(\DirectScale\User $User)
	{
		$this->User	=	$User;
		$this->data	=	(empty($this->User->getData()))? $this->User->getDistInfo() : $this->User->getData();
		
		if(empty($this->data['general']['uid'])) {
			return false;
		}
		
		$this->autoships	=	$this->User->doGet('customers/'.$this->data['general']['uid'].'/autoships');
		$this->User->setAttr('autoship', (empty($this->autoships))? [] : $this->autoships);
	}
	/**
	 *	@description	
	 */
	public	function getOrder()
	{
		return $this->autoships;
	}
}