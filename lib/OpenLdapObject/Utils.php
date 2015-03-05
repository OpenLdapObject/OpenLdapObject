<?php

namespace OpenLdapObject;


abstract class Utils {
    public static function capitalize($str) {
        return strtoupper(substr($str, 0, 1)) . substr($str, 1);
    }
} 