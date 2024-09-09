<?php
namespace Cryptolens_PHP_Client {

    class License {
        private Cryptolens $cryptolens;

        private string $group;



        public function __construct(Cryptolens $cryptolens){
            $this->cryptolens = $cryptolens;
            $this->group = Cryptolens::CRYPTOLENS_LICENSE;
        }

        public function validateLicense(string $licenseKey, string $signature){
            $licenseKey = base64_decode($licenseKey);
            $signature = base64_decode($signature);

            $verified = openssl_verify($licenseKey, $signature, $this->getPublicKey(), OPENSSL_ALGO_SHA256);
            if($verified == 1){
                return true;
            } else {
                return false;
            } 
        }

        public function validateLicenseFromFileContent(string $fileContent){
            $fileContent = json_decode($fileContent, true);
            $licenseKey = base64_decode($fileContent["licenseKey"]);
            $signature = base64_decode($fileContent["signature"]);

            $verified = openssl_verify($licenseKey, $signature, $this->getPublicKey(), OPENSSL_ALGO_SHA256);
            if($verified == 1){
                return true;
            } else {
                return false;
            }
        }

        public function validateLicenseFromFile(string $fileContent){
            $fileContent = json_decode(file_get_contents($fileContent), true);
            $licenseKey = base64_decode($fileContent["licenseKey"]);
            $signature = base64_decode($fileContent["signature"]);

            $verified = openssl_verify($licenseKey, $signature, $this->getPublicKey(), OPENSSL_ALGO_SHA256);
            if($verified == 1){
                return true;
            } else {
                return false;
            }
        }

        public function getPublicKey(string $pathToKey = null){
            if($pathToKey == null){
                $pathToKey = dirname(__FILE__, 1) . "/key.pub";
            }
            return openssl_pkey_get_public(file_get_contents($pathToKey));
        }

    }
}



