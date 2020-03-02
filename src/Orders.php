<?php
namespace DirectScale;
/**
 *	@description	
 */
class Orders extends Model
{
	private $User, $orders;
	/**
	 *	@description	
	 */
	public	function __construct(User $User)
	{
		$this->User	=	$User;
		
		$str	=	implode('/', [
			'customers',
			$this->User->createDataSet()->getAttr('general', 'uid'),
			'orders'
		]);
		
		$this->orders	=	$this->getClient()->doGet($str);
		
		if(empty($this->orders)) {
			$this->User->setAttr('orders', []);
		}
		else {
			$this->orders	=	array_map(function($v){
				ksort($v);
				return $v;
			}, $this->normalizeKeys(json_decode($this->orders, 1)));
			$this->User->setAttr('orders', $this->orders);
		}
	}
	/**
	 *	@description	
	 */
	public	function getOrders()
	{
		$args		=	func_get_args();
		$ordernum	=	(!empty($args[0]))? $args[0] : false;
		$key		=	(!empty($args[1]))? $args[1] : 'order_number';
		
		if($ordernum) {
			foreach($this->orders as $order) {
				if(isset($order[$key]) && $order[$key] == $ordernum) {
					return $order;
				}
			}
			return [];
		}
		
		return $this->orders;
	}
}