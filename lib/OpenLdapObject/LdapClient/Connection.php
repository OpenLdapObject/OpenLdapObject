<?php

namespace OpenLdapObject\LdapClient;

use OpenLdapObject\Exception\BadIdentificationException;
use OpenLdapObject\Exception\ConnectionException;

/**
 * Class Connection
 *
 * Use to connect to the LDAP Server
 *
 * @package OpenLdapObject\LdapConnection
 * @author Toshy62 <yoshi62@live.fr>
 */
class Connection {
    /**
     * @var resource The Ldap connection ressource
     */
    private $connect;

    private $hostname;
    private $port;
    private $username;
    private $password;

    public function __construct($hostname, $port = 389) {
        $this->hostname = $hostname;
        $this->port = $port;
    }

    public function identify($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Connect to the Ldap Server and get a client to execute Query
     *
     * @return Client
     * @throws \OpenLdapObject\Exception\ConnectionException
     * @throws \OpenLdapObject\Exception\BadIdentificationException
     */
    public function connect() {
        $this->connect = ldap_connect($this->hostname, $this->port);

        if(!$this->connect) {
            throw new ConnectionException($this);
        }

        // Set Ldap Version to 3
        ldap_set_option($this->connect, LDAP_OPT_PROTOCOL_VERSION, 3);

        if(!is_null($this->username) && !is_null($this->password)) {
            if(!@ldap_bind($this->connect, $this->username, $this->password)) {
                throw new BadIdentificationException($this);
            }
        }

        return new Client($this->connect);
    }

    /**
     * @return string
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * @return int
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }
}