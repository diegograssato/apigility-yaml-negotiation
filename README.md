apigility-yaml-negotiation
=========================

YamlNegotiation module for Apigility.

Response type is based on *Accept* header :

- request that specifies **text/yaml** (or **text/yml**) get the content in YAML
- **application/hal+json** (or **application/\*+json**) request get the content in HalJson as usual.

### Installation
Install composer in your project

    curl -s http://getcomposer.org/installer | php

Add dependency in composer.json.


    composer require diegograssato/apigility-yaml-negotiation

 

### Usage
- Add *ZF\\ContentNegotiation\\YAML* to application.config.php:

```php
return [
    'modules' => [
        ...
        'ZF\\ContentNegotiation\\YAML',
        ....
    ]
]    
```

- Go to admin, select your API and change *Content Negotiation Selector* to **HalJsonYAML**
- Add **text/yaml** to *Accept whitelist* and *Content-Type whitelist*. Add other headers if needed.
- Save configuration
 