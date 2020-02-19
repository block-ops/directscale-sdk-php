## PHP Framework for DirectScale
To create a connection, the current method is by creating a `define()` in your application which contains your API key. Here is an example of fetching user data:

```php
<?php
# Create define
define('DIRECTSCALE_APIKEY', 'yourkey123here321');
# Create instance
$DirectScale = new \DirectScale\User(54321);
# Get distributor details
print_r($DirectScale->getDistInfo());
````

This connection method will be updated as the framework matures, but for now it requires a `define()`. It's worth noting that setting `$env` to `'dev'` when creating the connection will set the connection to developer mode. In order to accommodate developer mode, you need to have a developer constant labelled `DIRECTSCALE_DEVAPIKEY`.
Also a note, the [Nubersoft Framework](https://github.com/rasclatt/nubersoft) is required for this framework.
