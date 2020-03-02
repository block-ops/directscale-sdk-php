<?php
namespace DirectScale;
/**
 *	@description	
 */
class Architect extends Model
{
	/**
	 *	@description	
	 */
	public	function createUser($data)
	{
		$settings	=	[
			"BackOfficeId" => $this->getVal($data, 'distid'),
			"BirthDate" => $this->getVal($data, 'birth_date'),
			"CompanyName" => $this->getVal($data, 'company'),
			"CustomerStatus" => $this->getVal($data, 'status', 1),
			"CustomerType" => $this->getVal($data, 'type', 1),
			"DefaultShippingAddress" => [
				"Street1" => $this->getVal($data, 'shipping_line_1'),
				"Street2" => $this->getVal($data, 'shipping_line_2'),
				"Street3" => $this->getVal($data, 'shipping_line_3'),
				"PostalCode" => $this->getVal($data, 'shipping_zip'),
				"City" => $this->getVal($data, 'shipping_city'),
				"Region" => strtoupper($this->getVal($data, 'shipping_state')),
				"CountryCode" => strtoupper($this->getVal($data, 'shipping_country'))
			],
			"EmailAddress" => $this->getVal($data, 'email'),
			"ExternalReferenceId" => $this->getVal($data, 'external_reference'),
			"FirstName" => $this->getVal($data, 'first_name'),
			"LastName" => $this->getVal($data, 'last_name'),
			"LanguageCode" => strtolower($this->getVal($data, 'language', 'en')),
			"Password" => $this->getVal($data, 'password'),
			"PrimaryAddress" => [
				"Street1" => $this->getVal($data, 'billing_line_1'),
				"Street2" => $this->getVal($data, 'billing_line_2'),
				"Street3" => $this->getVal($data, 'billing_line_3'),
				"PostalCode" => $this->getVal($data, 'billing_zip'),
				"City" => $this->getVal($data, 'billing_city'),
				"Region" => strtoupper($this->getVal($data, 'billing_state')),
				"CountryCode" => strtoupper($this->getVal($data, 'billing_country'))
			],
			"PrimaryPhone" => $this->getVal($data, 'phone_1'),
			"SecondaryPhone" => $this->getVal($data, 'phone_2'),
			"SendEmails" => $this->getVal($data, 'email_opt_in', true),
			"SignUpDate" => $this->getVal($data, 'join_date', date('Y-m-d')),
			"SponsorId" => $this->getVal($data, 'sponsor_id'),
			"TaxExemptId" => $this->getVal($data, 'tax_exempt_id'),
			"TaxId" => $this->getVal($data, 'tax_id'),
			"TermsAccepted" => $this->getVal($data, 'terms_accepted', true),
			"TextPhone" => $this->getVal($data, 'phone_cell'),
			"Username" => $username = $this->getVal($data, 'username', $this->getVal($data, 'email')),
			"WebAlias" => preg_replace('/[^\d\w]/', '', $username).time()
		];
		
		return $this->getClient()->doPost('customers/', $settings);
	}
	/**
	 *	@description	
	 */
	public	function usernameExists($username)
	{
		return $this->getClient()->doGet("validate/username/{$username}");
	}
}