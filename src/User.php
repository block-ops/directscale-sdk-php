<?php
namespace DirectScale;
/**
 *	@description	
 */
class User extends Model
{
	protected	$distid, $data, $Architect;
	/**
	 *	@description	
	 *	@param	[string|int|class]	Can use backofficeid(varchar), internal id (int), email (string), or
	 *								\DirectScale\Architect to create a customer to fetch user data.
	 *								See next param for internal id.
	 *	@param	[null|bool]	If using and interal id (int), add true (1) to let object know it's calling
	 *						the internal id
	 */
	public	function __construct()
	{
		$args	=	func_get_args();
		
		if(empty($args[0]))
			throw new Exception('A Distributor Id, internal id, or instance of \\DirectScale\\Builder is required in the construct');
		
		if($args[0] instanceof Architect) {
			$this->Architect	=	$args[0];
		}
		elseif(filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
			$data	=	$this->getClient()->doGet('customers/', ['email' => $args[0]]);
			$this->data	=	(!empty($data))? $this->formatReturn($data) : [];
		}
		else {
			if(empty($args[1]))
				$this->distid	=	$args[0];
			else {
				$data	=	$this->getClient()->doGet('customers/'.$args[0]);
				$this->data	=	(!empty($data))? $this->formatReturn($data) : [];
			}
		}
	}
	/**
	 *	@description	
	 */
	public	function create($data)
	{
		return $this->Architect->createUser($data);
	}
	/**
	 *	@description	Fetches distributor information based on their distributor ID<br>
	 *					(rather than primary key)
	 *	@param	$reformat	[bool]	Default set to reformat the returned data to more compatable keys and data grouping
	 */
	public	function getDistInfo($reformat = true) : ?array
	{
		if(empty($this->data)) {
			if(!empty($this->distid)) {
				$this->data		=	$this->getClient()->doGet('customers/?backofficeid='.$this->distid);

				if(empty($this->data))
					return $this->data;

				if(!$reformat) {
					$this->data	=	json_decode(trim($this->data), 1);
					return $this->getData();
				}
			}
			elseif(empty($this->distid)) {
				return $this->data	=	[];
			}
		}
		else {
			if(!empty($this->data['user'])) {
				return $this->data;
			}
		}
		
		$Conv	=	$this->getHelper('Conversion\Data');
		$data	=	(!empty($this->data[0]))? $this->data : $this->formatReturn($this->data);
		$map	=	$Conv->xmlToArray($this->getResourceFile('gsmapping'.DS.'getdistinfo.xml'));
		$bill	=	$Conv->xmlToArray($this->getResourceFile('gsmapping'.DS.'billing.xml'));
		
		if(empty($data['customer_id']) && empty($data[0])) {
			return $this->data	=	[];
		}
		
		$usedata	=	(isset($data['customer_id']))? $data : $data[0];
		
		$shipping	=	\Nubersoft\ArrayWorks::interChangeArrays(array_merge([
			'first_name' => $usedata['first_name'],
			'last_name' => $usedata['last_name']
		], $usedata['default_shipping_address']), $bill);
		
		$billing	=	\Nubersoft\ArrayWorks::interChangeArrays(array_merge([
			'first_name' => $usedata['first_name'],
			'last_name' => $usedata['last_name']
		], $usedata['primary_address']), $bill);
		
		unset($usedata['primary_address'], $usedata['default_shipping_address'], $data);
		
		$main		=	$this->sortUserData(\Nubersoft\ArrayWorks::interChangeArrays($usedata, $map));
		
		$main['billing']	=	$billing;
		$main['shipping']	=	$shipping;
		
		$this->data	=	$main;
		
		return $this->data;
	}
	
	public	function sortUserData($array=false) : array
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
	public	function getData($key = false)
	{
		if($key)
			return $this->getAttr($key);
		
		return $this->data;
	}
	/**
	 *	@description	
	 */
	public	function setAttr($key, $value, $subkey = false) : object
	{
		if(!empty($subkey)) {
			if(!isset($this->data[$key]))
				$this->data[$key]	=	[];
			
			$this->data[$key][$subkey]	=	$value;
		}
		else
			$this->data[$key]	=	$value;
		
		ksort($this->data);
		
		return $this;
	}
	/**
	 *	@description	
	 */
	public	function getAttr(string $key, $subkey = false)
	{
		if(!empty($subkey))
			return (!empty($this->data[$key][$subkey]))? $this->data[$key][$subkey] : false;
		
		return (isset($this->data[$key]))? $this->data[$key] : false;
	}
	/**
	 *	@description	
	 */
	public	function createDataSet(bool $force = false)
	{
		if(empty($this->data) || $force)
			$this->getDistInfo();
		
		return $this;
	}
}