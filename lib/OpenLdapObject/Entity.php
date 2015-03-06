<?php

namespace OpenLdapObject;


abstract class Entity {
    private $_dn;
    private $_originData;

    public final function _setDn($dn) {
        $this->_dn = $dn;
    }

    public final function _getDn() {
        return $this->_dn;
    }

    public final function _setOriginData(array $originData) {
        $this->_originData = $originData;
    }

    public final function _getOriginData() {
        return $this->_originData;
    }
} 