<?php
namespace Cryptolens_PHP_Client {
    /**
     * Auth
     * 
     * Allows the use of all Auth endpoints
     * 
     * @author Bryan Böhnke-Avan <bryan@openducks.org
     * @license MIT
     * @since v0.4
     * @link https://app.cryptolens.io/docs/api/v3/AuthMethods
     */
    class Auth {

        private Cryptolens $cryptolens;

        private string $group;

        public function __construct($cryptolens){
            $this->cryptolens = $cryptolens;
            $this->group = Cryptolens::CRYPTOLENS_AUTH;
        }

        /**
         * key_lock() - Allows you to generate a access token bound to the specified key
         * 
         * @param string $key The license key
         * @return array|bool Returns an array on success, false on failure
         */
        public function key_lock($key){
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key);
            $c = Helper::connection($parms, "keyLock", $this->group);
            if($c == true){
                if(Helper::check_rm($c)){
                    return Cryptolens::outputHelper($c);
                } else {
                    return Cryptolens::outputHelper($c, 1);
                }
            } else {
                return false;
            }

        }
    }
}
