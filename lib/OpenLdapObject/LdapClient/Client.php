<?php

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

    function __destruct() {
        if(!is_null($this->connect)) {
            ldap_close($this->connect);
        }
    }
}