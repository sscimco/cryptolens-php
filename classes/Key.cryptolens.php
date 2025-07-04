<?php
namespace Cryptolens_PHP_Client {
    /**
     * Key
     * 
     * Allows the use of all Key API endpoints
     * 
     * @author Bryan Böhnke-Avan <bryan@openducks.org>
     * @license MIT
     * @since v0.1
     * @link https://app.cryptolens.io/docs/api/v3/Key
     */
    class Key {
        private Cryptolens $cryptolens;

        /** for future use */
        private string $group;

        public function __construct($cryptolens){
            $this->cryptolens = $cryptolens;
            $this->group = Cryptolens::CRYPTOLENS_KEY;
        }

        /**
         * getMachineId() - Generates a machine ID based on the server's OS, OS version, hostname, architecture, total disk space, unix timestamp of creation of root file system and php's currently installed version
         * 
         * The hash generated strips out any white spaces and commas and replaces them with two underlines "__"
         * The possibility of this method being completely unique is not given, as a update of the php version results in the hash getting invalid.
         * The function `getMachineIdPlain` allows you to retrieve an JSON to cache this value to generate the hash later on again for comparison.
         */
        public static function getMachineId(){
            $fingerprint = [php_uname(), disk_total_space("."), filectime("/"), phpversion()];
            return hash("sha256", json_encode($fingerprint));
        }

        public static function getMachineIdPlain(){
            $fingerprint = [php_uname(), disk_total_space("."), filectime("/"), phpversion()];
            foreach($fingerprint as $key => $value){
                $fingerprint[$key] = str_replace([" ", ",", "\n", "\r", "\t", "\v", "\x00"], "__", $fingerprint[$key]);
            }
            $fingerprint = implode("__", $fingerprint);
            $fingerprint = json_encode($fingerprint);

            return $fingerprint;
        }


        /**
         * activate() - Activates a Key
         * 
         * @param string $key The key to activate
         * @param string $machineid A unique ID for the machine the key is being activated
         * @return bool|array Returns an array with the Cryptolens reponse and the decoded license including informations about the key itself. Returns false on failure.
         * @link https://app.cryptolens.io/docs/api/v3/activate
         */
        public function activate(string $key, string $machineid){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key, $machineid);
            $c = $this->connection($parms, "activate");
            if($c == true){
                $license = json_decode(base64_decode($c["licenseKey"]), true);
                # check for missing license elements
                if(is_null($license) ||
                !array_key_exists("ProductId", $license)||
                !array_key_exists("Key", $license)||
                !array_key_exists("Expires", $license)||
                !array_key_exists("ActivatedMachines", $license)){
                    return false; # Key, productid, expires or activatedMachines are not set
                }

                if($license["ProductId"] !== $this->cryptolens->getProductId() || $license["Key"] !== $key){
                    return Cryptolens::outputHelper([
                        "error" => "An error occurred while activating key - Malformed response received.",
                        "response" => $c
                    ]);
                }

                $m = false;
                foreach($license["ActivatedMachines"] as $machine){
                    if(!array_key_exists("Mid", $machine)){
                        return Cryptolens::outputHelper([
                            "error" => "An error occurred while activating key - The license has already been enabled on this machine.",
                            "response" => $c
                        ]);
                    }
                    if($machine["Mid"] !== $machineid){
                        return Cryptolens::outputHelper([
                            "error" => "An error occurred while activating key - The key can not be activated on this machine as the received machine ID is not the same as sent.",
                            "response" => $c
                        ]);
                    }
                }
                if($license["Expires"] < time()){
                    return Cryptolens::outputHelper([
                        "error" => "An error occurred while activating key - The key has already expired.",
                        "response" => $c
                    ]);
                }

                return Cryptolens::outputHelper([
                    "response" => $c,
                    "license" => $license
                ]);
            } else {
                return Cryptolens::outputHelper($c);
            }
        }

        /**
         * deactivate() - Deactivates the given key
         * 
         * @param string $key The key that should be deactivated
         * @param string $machineid The machine ID the key is mapped to, you can leave this empty, but sometimes you recieve an error. Read more in the documentation.
         * @link https://api.cryptolens.io/api/key/deactivate
         */
        public function deactivate(string $key, string $machineid = null){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key, $machineid);
            $c = $this->connection($parms, "deactivate");

            if($c == true){
                # check response values
                if($c["result"] == 0){
                    return true;
                } else {
                    return Cryptolens::outputHelper($c);
                }
            }
        }

        /**
         * create_key() - Creates a key for a specific project
         * 
         * @param array $additional_flags Allows to add more than the required parameters e.g. Period, F1-8, Notes, Block, CustomerId, NewCustomer, AddOrUseExistingCustomer, TrialActivations, MaxNoOfMachines, AllowedMachines, ResellerId, NoOfKeys*
         * @return array|bool Returns an array with the keys "Key", "CustomerId" (only if "NewCustomer" or "AddOrUseExistingCustomer" is set), "Result" and "Message". The key "Key" gets renamed to "Keys" if "NoOfKeys" is greater than 1. On error it returns the Cryptolens response, with the "error" and "response" key
         * @link https://api.cryptolens.io/api/key/createKey
         */
        public function create_key(array $additional_flags = null){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, $additional_flags);
            $c = $this->connection($parms, "createKey");
            if($c == true){
                switch($c){
                    case $c["result"] != 0:
                        return Cryptolens::outputHelper([
                            "error" => "An error occurred.",
                            "response" => $c
                        ]);
                    case $c["key"] == null && $c["keys"] == null:
                        return Cryptolens::outputHelper([
                            "error" => "Key is empty.",
                            "response" => $c
                        ]);
                };
                return Cryptolens::outputHelper($c);
            } else {
                return false;
            }
        }

        /**
         * create_trial_key() - Creates a trial key optionally locked to a machineId
         * 
         * @param string [optional] Lock the new generated key to a machineId
         */
        public function create_trial_key(string $machineId = null){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, $machineId);
            $c = $this->connection($parms, "createTrialKey");
            if($c == true){
                switch($c){
                    case $c["result"] != 0:
                        return Cryptolens::outputHelper([
                            "error" => "An error occurred.",
                            "response" => $c
                        ]);
                    case $c["result"] == 0 && $c["key"] == null:
                        return Cryptolens::outputHelper([
                            "error" => "The key response is empty even though the Result returned 0 (success).",
                            "response" => $c
                        ]);
                }
                return Cryptolens::outputHelper($c);
            } else {
                return false;
            }
        }

        /**
         * create_key_from_template() Allows you to create a key from an existing template
         * 
         * @param int $template The template ID, can be obtained from the Products Dashboard > License Templates > Edit and then the number from the URI
         * @return array|bool Returns an array with the response or bool on failure
         */
        public function create_key_from_template(int $template){
            $parms = $this->build_params($this->cryptolens->getToken(), null, null, null, ["LicenseTemplateId" => $template]);
            $c = $this->connection($parms, "createKeyFromTemplate");
            if($c == true){
                if($c["result"] != 0){
                    return Cryptolens::outputHelper([
                        "error" => "An error occurred.",
                        "response" => $c
                    ]);
                } else {
                    return Cryptolens::outputHelper($c);
                }
            } else {
                return false;
            }
        }

        /**
         * get_key() - Allows you to get more information about a key, returns the same data as `activate()`
         * 
         * @param string $key The key you want to get the data from
         * @param array $additional_flags Allows you to set more options, like e.g. metadata, fieldstoreturn, floatingtimeinterval and modelversion
         * @return array|bool Returns the key "response" and "licenseKey" (base64 decoded JSON array)
         */
        public function get_key(string $key, array $additional_flags = null){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key, null, $additional_flags);
            $c = $this->connection($parms, "getKey");
            if($c == true){
                switch($c){
                    case $c["result"] != 0:
                        return Cryptolens::outputHelper([
                            "error" => "An error occurred.",
                            "response" => $c
                        ]);
                    case $c["licenseKey"] == null:
                        return Cryptolens::outputHelper([
                            "error" => "License Key object empty!",
                            "response" => $c
                        ]);
                }

                return Cryptolens::outputHelper([
                    "response" => $c,
                    "licenseKey" => base64_decode($c["licenseKey"])
                ]);
            } else {
                return false;
            }
        }

        /**
         * add_feature() - Enables a certain feature (F1-8) for a specified key.
         * 
         * @param string $key The key to enable the feature
         * @param int $feature The feature number 1 - 8 to enable
         * @return array|bool Returns the response on success and a bool on failure
         * 
         * @note When using SKGL a new key will be generated and included inside the "message" key, otherwise it will be empty
         */
        public function add_feature(string $key, int $feature){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key, null, ["Feature" => $feature]);
            $c = $this->connection($parms, "addFeature");
            if($c == true || $this->check_rm($c)){
                return Cryptolens::outputHelper($c);
            } else {
                return Cryptolens::outputHelper([
                    "error" => "An error occurred",
                    "response" => $c
                ]);
            }
        }

        public function block_key(string $key){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key);
            $c = $this->connection($parms, "blockKey");
            if($c == true){
                if($c["result"] == 0){
                    return true;
                } else {
                    return Cryptolens::outputHelper([
                        "error" => "An error occurred.",
                        "response" => $c
                    ]);
                }
            } else {
                return false;
            }
        }

        /**
         * extend_license() - Allows you to extend a license by $days.
         * 
         * @param string $key The license to extend
         * @param int $days The amount of (x) days to extend the license
         * 
         * @note If the key is already expired from the current date the key is x days valid. If the key has not expired and has e.g. 15 days left, the key will be valid 15 + x days
         * If the SKGL algorithm is used, the "message" key contains the new key, otherwise you can use the same key.
         */
        public function extend_license(string $key, int $days){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key, null, ["NoOfDays" => $days]);
            $c = $this->connection($parms, "extendLicense");
            if($c == true){
                if($c["result"] == 0){
                    return Cryptolens::outputHelper($c);
                } else {
                    return Cryptolens::outputHelper([
                        "error" => "An error occurred.",
                        "response" => $c
                    ]);
                }
            } else {
                return false;
            }
        }
        # returns new key if skgl
        public function remove_feature(string $key, int $feature){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key, null, ["Feature" => $feature]);
            $c = $this->connection($parms, "removeFeature");
            if($c == true || $this->check_rm($c)){
                return Cryptolens::outputHelper($c);
            } else {
                Cryptolens::outputHelper([
                    "error" => "An error occurred",
                    "response" => $c
                ]);
            }
        }

        public function unblock_key(string $key){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key);
            $c = $this->connection($parms, "unblockKey");
            if($c == true || $this->check_rm($c)){
                return Cryptolens::outputHelper($c);
            } else {
                return Cryptolens::outputHelper([
                    "error" => "An error occurred",
                    "response" => $c
                ]);
            }
        }

        public function machine_lock_limit(string $key, int $machines){
            $parms = $this->build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key, null, ["NumberOfMachines" => $machines]);
            $c = $this->connection($parms, "machineLockLimit");
            if($c == true || $this->check_rm($c)){
                return Cryptolens::outputHelper($c);
            } else {
                Cryptolens::outputHelper([
                    "error" => "An error occurred",
                    "response" => $c
                ]);
            }
        }

        /** 
         * build_params() - Internal helper function building the parameters for the cURL request
         */
        private function build_params($token, $product_id, $key = null, $machineid = null, array $additional_flags = null){
            $parms = array(
                "token" => $token,
                "ProductId" => $product_id,
                "Sign" => "True",
                "v" => 1,
                "SignMethod" => 1
            );
            if($key != null){
                $parms["Key"] = $key;
            }
            if($machineid != null){
                $parms["MachineCode"] = "";# empty string to prevent error
            }
            if($additional_flags != null){
                if(is_array($additional_flags)){
                    foreach($additional_flags as $key => $value){
                        if(is_array($value)){
                            $value = implode(",", $value);
                        } elseif(is_bool($value)){
                            if($value == true){
                                $value = "true";
                            } else {
                                $value = "false";
                            }
                        } elseif(!is_string($value)){
                            $value = (string) $value;
                        }
                        $parms[$key] = $value;
                    }
                } else {
                    echo "Parsed \$additional_flags not as an array!";
                }
            }
            $postfields = ''; $first = true;
            foreach($parms as $i => $x){
                if (is_array($x)) {
                    // detect array an skip
                    continue;
                }
                if($first) { $first = false; } else { $postfields .= '&';}

                $postfields .= urlencode($i) . "=" . urlencode($x);

            }
            unset($i, $x);
            return $postfields;
        }

        private function connection($post, $endpoint){
            $c = curl_init();
            curl_setopt_array($c, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => Endpoints::get_endpoint($endpoint),
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $post
            ));
            $res = curl_exec($c);
            if($res == false){
                return curl_error($c);
            }
            curl_close($c);

            $resp = json_decode($res, true);
            if($this->check_response($resp, $endpoint) == true){
                return $resp;
            } else {
                return "Could not validate response";
            }
        }

        private function check_response($res, $endpoint){
            if(is_null($res)){
                return "Res == null";
            }

            if(in_array($endpoint, Endpoints::$no_response_check)){
                return true;
            }

            $actual_keys = array_keys($res); // e.g., ["metadata", "licenseKey", "signature", ...]
            $expected_keys = Results::get_results()["Key"][$endpoint]; // e.g., ["LicenseKey", "Signature", ...]

            foreach($expected_keys as $expected_key){
                $found = false;
                foreach($actual_keys as $actual_key){
                    if(strcasecmp($expected_key, $actual_key) === 0){
                        $found = true;
                        break;
                    }
                }

                if(!$found){
                    return "Missing expected key: $expected_key";
                }
            }

            return true;
        }

        private function check_rm($data){
            if($data["result"] == 0){
                return true;
            } else {
                return false;
            }
        }
    }
}
