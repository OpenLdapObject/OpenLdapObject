<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Pierre Pélisset
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

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
class Connection
{
    /**
     * @var resource The Ldap connection ressource
     */
    private $connect;

    private $hostname;
    private $port;
    private $username;
    private $password;

    public function __construct($hostname, $port = 389)
    {
        $this->hostname = $hostname;
        $this->port = $port;
    }

    public function identify($username, $password)
    {
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
    public function connect()
    {
        $this->connect = ldap_connect($this->hostname, $this->port);

        if (!$this->connect) {
            throw new ConnectionException($this);
        }

        // Set Ldap Version to 3
        ldap_set_option($this->connect, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (!is_null($this->username) && !is_null($this->password)) {
            if (!@ldap_bind($this->connect, $this->username, $this->password)) {
                throw new BadIdentificationException($this);
            }
        }

        return new Client($this->connect);
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}