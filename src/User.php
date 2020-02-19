<?php
namespace DirectScale;
/**
 *	@description	
 */
class User extends Model
{
	protected	$distid, $data;
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
	 *	@description	Fetches distributor information based on their distributor ID (rather than primary key)
	 *	@param	$reformat	[bool]	Default set to reformat the returned data to more compatable keys and data grouping
	 */
	public	function getDistInfo($reformat = true)
	{
		$this->data	=
		$data		=	$this->doGet('customers/?backofficeid='.$this->distid);
		
		if(empty($data))
			return $data;
		
		if(!$reformat)
			return $this->getData();
		
		$Conv	=	$this->getHelper('Conversion\Data');
		$data	=	$this->normalizeKeys(json_decode($data, 1));
		$map	=	$Conv->xmlToArray($this->getResourceFile('gsmapping'.DS.'getdistinfo.xml'));
		$bill	=	$Conv->xmlToArray($this->getResourceFile('gsmapping'.DS.'billing.xml'));
		
		$shipping	=	\Nubersoft\ArrayWorks::interChangeArrays(array_merge(['first_name' => $data[0]['first_name'], 'last_name' => $data[0]['last_name']], $data[0]['default_shipping_address']), $bill);
		$billing	=	\Nubersoft\ArrayWorks::interChangeArrays(array_merge(['first_name' => $data[0]['first_name'], 'last_name' => $data[0]['last_name']], $data[0]['primary_address']), $bill);
		
		unset($data[0]['primary_address'], $data[0]['default_shipping_address']);
		
		$main		=	$this->sortUserData(\Nubersoft\ArrayWorks::interChangeArrays($data[0], $map));
		
		$main['billing']	=	$billing;
		$main['shipping']	=	$shipping;
		
		return $main;
	}
	
	public	function sortUserData($array=false)
	{
		$new	=	[];
		$array	=	(is_array($array))? $array : $this->raw_user;
			
		foreach($array as $key => $value) {
			if(preg_match('/^user/', $key) || in_array($key, ['internal_id','join_date','highest_achieved_rank','current_rank','avatar','first_name','last_name','full_name','member_type','email_address','night_phone','cell_phone'])) {
				if($key == 'internal_id')
					$new['user']['distid']	=	$value;
				else
					$new['user'][$key]	=	$value;
			}
			elseif(preg_match('/^billing_/', $key)) {
				$new['billing'][str_replace('billing_','',$key)]	=	$value;
			}
			elseif(preg_match('/^shipping_/', $key)) {
				$new['shipping'][str_replace('shipping_','',$key)]	=	$value;
			}
			elseif(preg_match('/^cc_/', $key) || preg_match('/^exp_/', $key) || preg_match('/_card/', $key)) {
				$new['credit_card'][str_replace(['cc_', 'billing_'],'',$key)]	=	$value;
			}
			elseif(preg_match('/^autoship_/', $key)) {
				
				$new['autoship']	=	true;
			}/*
			elseif(preg_match('/_qv/', $key)) {
				if(stripos($key, 'left') !== false)
					$new['volume']['leg_left'][$key]	=	$value;
				else
					$new['volume']['leg_right'][$key]	=	$value;
			}*/
			else
				$new['general'][$key]	=	$value;
		}
		
		ksort($new);
		
		return $new;
	}
	/**
	 *	@description	
	 */
	public	function getData()
	{
		return $this->data;
	}
}