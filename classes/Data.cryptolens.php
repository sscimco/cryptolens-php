<?php
namespace Cryptolens_PHP_Client {
    class Data {
        
        private Cryptolens $cryptolens;

        private string $group;

        public function __construct(Cryptolens $cryptolens){
            $this->cryptolens = $cryptolens;
            $this->group = CRYPTOLENS::CRYPTOLENS_DATA;
        }

        /**
         * `addDataObject()` - Adds a new Data Object to either a license key, product or your entire account.
         * The Cryptolens API provides 3 ways  to add a Data object for each type (license, product or account).
         * 
         * @param string $name Name of the DataObject, up to 100 characters
         * @param string $value A string to store, up to 10k characters
         * @param string $int optional integer to store, default = 0
         * @param int $referencerType Where to assign the data object: license key (2), product (1) or User (0), default = 0
         * @param string $referencerId ID of the referencer, e.g. the license key or product ID. Not required if "User" (0) is referencerType
         * @param bool $checkForDuplicates If set to true, checking for data objects with the same name
         * @link https://app.cryptolens.io/docs/api/v3/addDataObject
         * @return array|bool
         */
        public function addDataObject(string $name, string $value, int $int = 0, int $referencerType = 0, string $referencerId = "0", bool $checkForDuplicates = false){
            $parms = Helper::build_params($this->cryptolens->get_token(), $this->cryptolens->get_product_id(), null, null, [
                "Name" => $name,
                "StringValue" => $value,
                "IntValue" => $int,
                "ReferencerType" => $referencerType,
                "ReferencerId" => $referencerId,
                "CheckForDuplicates" => $checkForDuplicates
            ]);

            $c = Helper::connection($parms, "addDataObject", $this->group);
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

        /**
         * `listDataObjects()` - Returns all data objects associated with a key, product or account
         * @param int $referencerType Where to assign the data object: license key (2), product (1) or User (0), default = 0
         * @param string $referencerId ID of the referencer, e.g. the license key or product ID. Not required if "User" (0) is referencerType
         * @param string $contains A string, if set only returns Data object where "name" == $contains, default = ""
         * @param bool $showAll If set to `true` if returns both license key, product and account specific data objects all at once. Within response it contains the referencerType and Id.
         * @link https://app.cryptolens.io/docs/api/v3/ListDataObjects
         * @return array|bool Array on success and false on failure
         */
        public function listDataObjects(int $referencerType = 0, string $referencerId = "0", string $contains = "", bool $showAll = true){
            $parms = Helper::build_params($this->cryptolens->get_token(), $this->cryptolens->get_product_id(), null, null, [
                "ReferencerType" => $referencerType,
                "ReferencerId" => $referencerId,
                "Contains" => $contains,
                "ShowAll" => $showAll
            ]);
            $c = Helper::connection($parms, "listDataObjects", $this->group);
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

        /**
        * `incrementIntValue()` - Increment the Int value of an Data Object
        * @param int $id Unique ID of the data object
        * @param int $value The value to be incremented on top (if e.g. set to 5 and your existing int is 2 the new one will be 7)
        * @param bool $enableBound If set to true, you can set a upper bound, e.g. 10 - if you reach 10 an error occurs.
        * @param int $bond Upper bound to enforce if $enableBond has been enabled
        * @link https://app.cryptolens.io/docs/api/v3/incrementIntValue
        * @return array|bool Array on success and false on failure
        */
       public function incrementIntValue(int $id, int $value = 0, bool $enableBound = false, int $bound = 0){
           $parms = Helper::build_params($this->cryptolens->get_token(), $this->cryptolens->get_product_id(), null, null, [
               "Id" => $id,
               "IntValue" => $value,
               "EnableBound" => $enableBound,
               "Bound" => $bound
           ]);
           $c = Helper::connection($parms, "incrementIntValue", $this->group);
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

        /**
        * `decrementIntValue()` - Increment the Int value of an Data Object
        * @param int $id Unique ID of the data object
        * @param int $value The value to decrement(if e.g. set to 2 and your existing int is 5 the new one will be 3)
        * @param bool $enableBound If set to true, you can set a lower bound, e.g. 10 - if you reach 10 an error occurs.
        * @param int $bond Lower bound to enforce if $enableBond has been enabled
        * @link https://app.cryptolens.io/docs/api/v3/decrementIntValue
        * @return array|bool Array on success and false on failure
        */
        public function decrementIntValue(int $id, int $value = 0, bool $enableBound = false, int $bound = 0){
            $parms = Helper::build_params($this->cryptolens->get_token(), $this->cryptolens->get_product_id(), null, null, [
                "Id" => $id,
                "IntValue" => $value,
                "EnableBound" => $enableBound,
                "Bound" => $bound
            ]);
            $c = Helper::connection($parms, "decrementIntValue", $this->group);
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

        /**
         * `setStringValue()` - Set the string value of a data object
         * @param int $id Unique ID of the data object
         * @param string $value String value to set
         * @link https://app.cryptolens.io/docs/api/v3/setStringValue
         * @return array|bool
         */
        public function setStringValue(int $id, string $value = null){
            $parms = Helper::build_params($this->cryptolens->get_token(), $this->cryptolens->get_product_id(), null, null, [
                "Id" => $id,
                "StringValue" => $value
            ]);
            $c = Helper::connection($parms, "setStringValue", $this->group);
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

        /**
         * `setIntValue()` - Set the int value of a data object
         * @param int $id Unique ID of the data object
         * @param string $value String value to set
         * @link https://app.cryptolens.io/docs/api/v3/setIntValue
         * @return array|bool
         */
        public function setIntValue(int $id, int $value = null){
            $parms = Helper::build_params($this->cryptolens->get_token(), $this->cryptolens->get_product_id(), null, null, [
                "Id" => $id,
                "IntValue" => $value
            ]);
            $c = Helper::connection($parms, "setIntValue", $this->group);
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

        /**
         * `removeDataObject()` - Remove a data object
         * @param int $id Unique ID of the data object
         * @link https://app.cryptolens.io/docs/api/v3/removeDataObject
         * @return array|bool
         */
        public function removeDataObject(int $id){
            $parms = Helper::build_params($this->cryptolens->get_token(), $this->cryptolens->get_product_id(), null, null, [
                "Id" => $id
            ]);
            $c = Helper::connection($parms, "removeDataObject", $this->group);
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

                /**
         * `uploadValues()` - Upload values from a Cryptolens log file to either increment or decrement a data object
         * @param int $id Unique ID of the data object
         * @param string $key Serial Key
         * @param string $data String in base64 created by Cryptolens SDK that has recorded data object usage.
         * @link https://app.cryptolens.io/docs/api/v3/removeDataObject
         * @return array|bool
         */
        public function uploadValues(int $id, string $key, string $data){
            $parms = Helper::build_params($this->cryptolens->get_token(), $this->cryptolens->get_product_id(), $key, null, [
                "Id" => $id,
                "Data" => $data
            ]);
            $c = Helper::connection($parms, "removeDataObject", $this->group);
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


?>