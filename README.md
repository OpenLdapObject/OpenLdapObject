# OpenLdapObject
[![Build Status](https://travis-ci.org/OpenLdapObject/OpenLdapObject.svg?branch=master)](https://travis-ci.org/OpenLdapObject/OpenLdapObject)
[![Version](https://img.shields.io/packagist/v/openldapobject/openldapobject.svg?style=flat)](https://packagist.org/packages/openldapobject/openldapobject)
[![Code Climate](https://codeclimate.com/github/OpenLdapObject/OpenLdapObject/badges/gpa.svg)](https://codeclimate.com/github/OpenLdapObject/OpenLdapObject)

Use Object to Read/Write in a LDAP
----------------------------------

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require openldapobject/openldapobject "~1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Or add the bundle in your `composer.json` and launch this command `composer update`
```
...
    "require" : {
        ...
        "openldapobject/openldapobject": "~1.0",
        ...
    },
...
```

Step 2: Configuration
---------------------
Add configuration keys in the `app/config/parameters.yml` and `app/config/parameters.yml.dist` and configure for your openldap :
```
    ldap_hostname: ldap-test.univ.fr
    ldap_base_dn: 'dc=univ,dc=fr'
    ldap_dn: 'cn=login,ou=ldapusers,dc=univ,dc=fr'
    ldap_password: 'password'
```


Step 3: Use the Bundle
----------------------

You can use this bundle like this :
```php
<?php
namespace AppBundle\Controller;

use OpenLdapObject\LdapClient\Connection;
use OpenLdapObject\LdapClient\Client;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/example", name="example")
     */
    public function exampleAction() {

        $ldap = new Connection($this->container->getParameter('ldap_hostname'), 389);

        $ldap->identify($this->container->getParameter('ldap_dn'), $this->container->getParameter('ldap_password'));

        $client = $ldap->connect();

        $client->setBaseDn($this->container->getParameter('ldap_base_dn'));

        $query = "(&(objectclass=*)(sn=Hetru))";

        $accounts = $client->search($query, array('*', 'memberof'), 0, 'ou=accounts');

	dump($accounts);
        ...
    }
}
...
...
```


