<?php
namespace Cryptolens_PHP_Client {
    /**
     * User
     * 
     * Allows the use of all User Authentication methods
     * 
     * @author Bryan Böhnke-Avan <bryan@openducks.org>
     * @license MIT
     * @since v1.1
     * @link https://app.cryptolens.io/docs/api/v3/UserAuth
     */
    class User {

        public Cryptolens $cryptolens;

        public string $group;

        public function __construct(Cryptolens $cryptolens){
            $this->cryptolens = $cryptolens;
            $this->group = CRYPTOLENS::CRYPTOLENS_USER;
        }

        /**
         * `login()` - Login the user and get all licenses that belong to it.
         * @param string $username The username
         * @param string $password The password
         * @link https://app.cryptolens.io/docs/api/v3/Login
         * @return array|bool|string
         */
        public function login(string $username, string $password){
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, [
                "Username" => $username,
                "Password" => $password
            ]);

            $c = Helper::connection($parms, "login", $this->group);
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
         * `register()` - Register a new user
         * @param string $username The username
         * @param string $password The password
         * @param string $customerId The customer object ID to associate the user to
         * @param string $email The email
         * @link https://app.cryptolens.io/docs/api/v3/Register
         * @return array|bool|string
         */
        public function register(string $username, string $password, string $customerId = null, string $email = null){
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, [
                "Username" => $username,
                "Password" => $password,
                "CustomerId" => $customerId,
                "Email" => $email
            ]);

            $c = Helper::connection($parms, "register", $this->group);
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
         * `associate()` - Associates a user with a customer object.
         * @param string $username Username to associate customer object with
         * @param string $customerId The customer object ID to associate to (this value is optional, but what's the point behind calling this function w/ a customer ID?)
         * @link https://app.cryptolens.io/docs/api/v3/Associate
         * @return array|bool
         */
        public function associate(string $username, string $customerId = null){
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, [
                "Username" => $username,
                "CustomerId" => $customerId,
            ]);

            $c = Helper::connection($parms, "associate", $this->group);
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
         * `dissociate()` - Dissociates a user from a customer object
         * @param string $username The username to dissociate the customer object with
         * @link https://app.cryptolens.io/docs/api/v3/Dissociate
         * @return array|bool
         */
        public function dissociate(string $username){
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, [
                "Username" => $username
            ]);

            $c = Helper::connection($parms, "dissociate", $this->group);
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
         * `getUsers` - Returns a list of all users
         * @param string $customerId optional customer object ID to get all associates users from
         * @link https://app.cryptolens.io/docs/api/v3/GetUsers
         * @return array|bool
         */
        public function getUsers(string $customerId = null){
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, [
                "customerId" => $customerId,
            ]);

            $c = Helper::connection($parms, "getUsers", $this->group);
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
         * `changePassword()` - Change the Password of a specific user
         * @param string $username The username
         * @param string $newPassword The new password
         * @param string $oldPassword optional old password if available
         * @param string $passwordResetToken optional password reset token if available (irrelevant, as the API client requires the token to be set)
         * @param bool $adminMode optional boolean to specify if user has admin mode enabled
         * @link https://app.cryptolens.io/docs/api/v3/ChangePassword
         * @return array|bool
         */
        public function changePassword(string $username, string $newPassword, string $oldPassword = null, string $passwordResetToken = null, bool $adminMode = false){
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, [
                "Username" => $username,
                "OldPassword" => $oldPassword,
                "NewPassword" => $newPassword,
                "PasswordResetToken" => $passwordResetToken,
                "AdminMode" => $adminMode
            ]);

            $c = Helper::connection($parms, "changePassword", $this->group);
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
         * `resetPasswordToken()` - Get the user's password reset token to perform the `changePassword` method on (irrelevant, as the API client requires the token to be set)
         * @param string $username The username to get the reset token
         * @link https://app.cryptolens.io/docs/api/v3/ResetPasswordToken
         * @return array|bool
         */
        public function resetPasswordToken(string $username){
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, [
                "Username" => $username,
            ]);

            $c = Helper::connection($parms, "resetPasswordToken", $this->group);
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
         * `removeUser()` - Removes a specified user
         * @param string $username The username
         * @link https://app.cryptolens.io/docs/api/v3/RemoveUser
         * @return array|bool
         */
        public function removeUser(string $username){
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, [
                "Username" => $username,
            ]);

            $c = Helper::connection($parms, "removeUser", $this->group);
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
