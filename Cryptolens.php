<?php
namespace Cryptolens_PHP_Client {
    /**
     * Cryptolens main class
     * 
     * This is the cryptolens main class containing an auto loader. It is also holding the sensitive login information needed for the API.
     * You can define the output to either JSON or Arrays (PHP)
     */
    class Cryptolens {


        public const CRYPTOLENS_OUTPUT_PHP = 1;

        public const CRYPTOLENS_OUTPUT_JSON = 2;

        public const CRYPTOLENS_KEY = "Key";

        public const CRYPTOLENS_AUTH = "Auth";

        public const CRYPTOLENS_PRODUCT = "Product";

        public const CRYPTOLENS_PAYMENTFORM = "PaymentForm";

        public const CRYPTOLENS_MESSAGE = "Message";

        public const CRYPTOLENS_RESELLER = "Reseller";

        public const CRYPTOLENS_SUBSCRIPTION = "Subscription";

        public const CRYPTOLENS_CUSTOMER = "Customer";

        public const CRYPTOLENS_ANALYTICS = "Analytics";

        public const CRYPTOLENS_LICENSE = "License";
        
        private string $token;

        private int $version = 2;

        private int $productId;

        public static int $output;

    
        private function set_token(string $token): void{
            $this->token = $token;
        }
        private function set_product_id($product_id){
            $this->productId = $product_id;
        }

        /**
         * If $output equals CRYPTOLENS_OUTPUT_PHP you recieve arrays, if it's on CRYPTOLENS_OUTPUT_JSON you recieve json
         */
        public function __construct($token, $productid, $output = self::CRYPTOLENS_OUTPUT_PHP){
            self::$output = $output;
            $this->set_token($token);
            $this->set_product_id($productid);
        }


        public function get_token(){
            return $this->token;
        }

      public function get_product_id(){
            return $this->productId;
        }

        public function set_output($output){
            $this->output = $output;
        }

        public static function loader(){
            require_once dirname(__FILE__) . "/classes/Helper.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Errors.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Key.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Endpoints.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Results.endpoints.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Auth.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Product.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/PaymentForm.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Message.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Reseller.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Subscription.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Customer.cryptolens.php";
            require_once dirname(__FILE__) . "/classes/Analytics.cryptolens.php";
            @require_once dirname(__FILE__) . "/classes/License.cryptolens.php";


        }

        public static function outputHelper($data, int $error = 0){
            if(self::$output == self::CRYPTOLENS_OUTPUT_PHP){
                if($error == 1){
                    return [
                        "error" => "An error occured!",
                        "message" => $data["message"]
                    ];
                    
                }
                return (array) $data;
            } elseif(self::$output == self::CRYPTOLENS_OUTPUT_JSON){
                if($error == 1){
                    return [
                        "error" => "An error occured!",
                        "message" => $data["message"]
                    ];
                }
                return json_encode($data);
            }
        }

    }
}



?>
