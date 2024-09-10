<?php
namespace Cryptolens_PHP_Client {
    use OpenSSLAsymmetricKey;

    class License
    {

        private string $group;

        private string $publicKeyFile;

        private bool $publicKeyIsXMLFormat;

        public function __construct(bool $publicKeyIsXMLFormat = false, string $publicKeyFile = null)
        {
            $this->group = Cryptolens::CRYPTOLENS_LICENSE;

            if ($publicKeyFile != null) {
                $this->publicKeyFile = $publicKeyFile;
            } else {
                $this->publicKeyFile = dirname(__FILE__, 1) . "/key.pub";
            }

            $this->publicKeyIsXMLFormat = $publicKeyIsXMLFormat;
        }

        /**
         * `validateLicense()`- Validates a licenseKey against given signature.
         * @param string $licenseKey The license key
         * @param string $signature The signature
         * @return bool True if signature is valid and false otherwise
         */
        public function validateLicense(string $licenseKey, string $signature)
        {
            $licenseKey = base64_decode($licenseKey);
            $signature = base64_decode($signature);

            $verified = openssl_verify($licenseKey, $signature, $this->getPublicKey($this->publicKeyFile, $this->publicKeyIsXMLFormat), OPENSSL_ALGO_SHA256);
            if ($verified == 1) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * `validateLicenseFromFileContent` - Validates the licenseKey against signature from given license file content
         * @param string $fileContent The file content as string
         * @return bool True if signature is valid and false otherwise
         */
        public function validateLicenseFromFileContent(string $fileContent)
        {
            $fileContent = json_decode($fileContent, true);
            $licenseKey = base64_decode($fileContent["licenseKey"]);
            $signature = base64_decode($fileContent["signature"]);

            $verified = openssl_verify($licenseKey, $signature, $this->getPublicKey($this->publicKeyFile, $this->publicKeyIsXMLFormat), OPENSSL_ALGO_SHA256);
            if ($verified == 1) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * `validateLicenseFromFile()` - Validates the licenseKey against a signature from given license key file path
         * @param string $fileContent The file path of the license
         * @return bool True if signature is valid and false otherwise
         */
        public function validateLicenseFromFile(string $fileContent)
        {
            $fileContent = $this->readLicenseFile($fileContent);
            $licenseKey = base64_decode($fileContent["licenseKey"]);
            $signature = base64_decode($fileContent["signature"]);

            $verified = openssl_verify($licenseKey, $signature, $this->getPublicKey($this->publicKeyFile, $this->publicKeyIsXMLFormat), OPENSSL_ALGO_SHA256);
            if ($verified == 1) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * `getPublicKey()` - Reads the public key from your specified file or from default ./classes/key.pub
         * @param string $pathToKey
         * @return bool|\OpenSSLAsymmetricKey
         */
        public function getPublicKey(string $pathToKey = null, bool $xml = false)
        {
            if ($pathToKey == null) {
                $pathToKey = dirname(__FILE__, 1) . "/key.pub";
            }


            if ($xml == true) {
                return openssl_pkey_get_public(file_get_contents($pathToKey));
            } else {
                return openssl_pkey_get_public(file_get_contents($pathToKey));
            }
        }

        /**
         * 
         * @param string $filePath
         * @return mixed
         */
        public function readLicenseFile(string $filePath)
        {
            return json_decode(file_get_contents($filePath), true);
        }
    }
}



