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

/**
 * Class Connection
 *
 * Use to send query to the LDAP Server
 *
 * @package OpenLdapObject\LdapConnection
 * @author Toshy62 <yoshi62@live.fr>
 */
class Client {
    private $connect;
    private $baseDn;

    public function __construct($ldapConnect) {
        $this->connect = $ldapConnect;
    }

    public function setBaseDn($baseDn) {
        $this->baseDn = $baseDn;
    }

    public function getBaseDn() {
        return $this->baseDn;
    }

    public function search($filter, array $attributes = array('*'), $limit = 0, $overloadDn = null) {
        $baseDn = '';
        if(!is_null($overloadDn)) {
            $baseDn = $overloadDn . (!is_null($this->baseDn) ? ',' . $this->baseDn : '');
        } else {
            if(!is_null($this->baseDn)) {
                $baseDn = $this->baseDn;
            }
        }
        $result = @ldap_search($this->connect, $baseDn, $filter, $attributes, 0, $limit);
        if(!$result) {
            var_dump(ldap_error($this->connect));
            return null;
        }
        return ldap_get_entries($this->connect, $result);
    }

    public static function cleanResult($input) {
        $output = array();
        for($nbEntry = 0; $nbEntry < $input['count']; $nbEntry++) {
            $entry = array();
            $entry['dn'] = $input[$nbEntry]['dn'];
            $entry['data'] = array();
            for($nbField = 0; $nbField < $input[$nbEntry]['count']; $nbField++) {
                if($input[$nbEntry][$input[$nbEntry][$nbField]]['count'] == 1) {
                    $content = $input[$nbEntry][$input[$nbEntry][$nbField]][0];
                } else {
                    $content = array();
                    for($number = 0; $number < $input[$nbEntry][$input[$nbEntry][$nbField]]['count']; $number++) {
                        $content[] = $input[$nbEntry][$input[$nbEntry][$nbField]][$number];
                    }
                }
                $entry['data'][$input[$nbEntry][$nbField]] = $content;
            }

            $output[] = $entry;
        }
        return $output;
    }

    public function __destruct() {
        if(!is_null($this->connect)) {
            ldap_close($this->connect);
        }
    }

    public function create($dn, $content) {
        ldap_add($this->connect, $dn, $content);
    }

    public function delete($dn) {
        ldap_delete($this->connect, $dn);
    }

    public function rename($oldDn, $newDn) {
        $parent = explode(',', $oldDn);
        unset($parent[0]);
        $parentDn = implode(',', $parent);
        ldap_rename($this->connect, $oldDn, $newDn, $parentDn, true);
    }

    public function update($dn, $data) {
        ldap_modify($this->connect, $dn, $data);
    }

    public function read($dn, array $attributes = array('*'), $limit = 1) {
        $res = @ldap_read($this->connect, $dn, "(objectclass=*)", $attributes, null, $limit);
        if(is_bool($res)) {
            return array('count' => 0);
        }
        return ldap_get_entries($this->connect, $res);
    }
}