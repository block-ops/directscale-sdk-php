## PHP Framework for DirectScale
To create a connection, the current method is by creating a `define()` in your application which contains your API key.

This connection method will be updated as the framework matures, but for now it requires a `define()`. It's worth noting that setting `$env` to `'dev'` when creating the connection will set the connection to developer mode. In order to accommodate developer mode, you need to have a developer constant labelled `DIRECTSCALE_DEVAPIKEY`.
Also a note, the [Nubersoft Framework](https://github.com/rasclatt/nubersoft) is required for this framework.

Here is an example of fetching user data:

```php
<?php
# Create define
define('DIRECTSCALE_APIKEY', 'yourkey123here321');
# Create instance
$DirectScale = new \DirectScale\User(54321);
# Get distributor details
print_r($DirectScale->getDistInfo());
````
### Example of common data functions:

```php
<?php
use \DirectScale\ {
	User,
	User\Subscription,
	Orders,
	Products,
	Stores
};

try {
	$User = new User('15F92');
	$Subscription =	new Subscription($User);
	$Orders = new Orders($User);
	$Products = new Products();
	$Stores = new Stores($Products);

	print_r([
		# Notice here that the autoship is appended to the user data
		# when creating instance of Subscription
		$User->getData(),
		# This will just fetch the autoship by itself
		$Subscription->getOrder(),
		# Fetches a list of all products
		$Products->get(),
		# Fetches a specific sku
		$Products->getBySku('EXAMPLESKU123'),
		# Fetches the store regions
		$Stores->getRegions(),
		# Fetches the store cateories
		$Stores->getCategories()
	]);
}
catch(\DirectScale\Exception $e) {
	# It is worth noting that getting a single product by sku requires the "optional" params
	# or it will return an error from DirectScale
	echo $e->getErrorTransactionId();
}
```