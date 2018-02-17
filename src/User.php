<?php

namespace TheoryTest\Car;

class User extends \UserAuth\User{
    
    /**
     * Returns the users name if logged in else return false
     * @return string|boolean
     */
    public function getUsername(){
        if($this->getUserID() !== 0){
            $this->getFirstname();
        }
        return false;
    }
    
    /**
     * Returns the users first name from the users information if they are logged in
     * @return string This should be the users first name
     */
    public function getFirstname(){
        if(!isset($this->userInfo)){$this->getUserInfo();}
        return $this->userInfo['first_name'];
    }
    
    /**
     * Returns the users last name from the users information if they are logged in
     * @return string This should be the users last name
     */
    public function getLastname(){
        if(!isset($this->userInfo)){$this->getUserInfo();}
        return $this->userInfo['last_name'];
    }
    
    /**
     * Checks to see if the user has upgraded their account and has access to the given test/learning section
     * @param int $testID This should be the test ID you are checking if the user has access to
     * @return boolean|void If the user has access will return try else will redirect the user to the upgrade page
     */
    public function checkUserAccess($testID = 100, $type = 'account'){
        return true;
    }
    
    /**
     * Returns any stored settings from the database that the user may have
     * @param int|false $userID If you wish to get settings for a specific user set this here else to get settings for current user leave this blank or set to false
     * @return array 
     */
    public function getUserSettings($userID = false){
        if($userID === false){$userID = $this->getUserID();}
        $this->getUserInfo($userID);
        return unserialize($this->userInfo['settings']);
    }
    
    /**
     * Sets the stored settings in the database for the given user
     * @param array $vars This should be an array of any settings you wish to add the the user
     * @param int $userID This should be the user ID that you are applying the settings update to
     * @return boolean If the settings are updated successfully will return true else returns false
     */
    public function setUserSettings($vars, $userID = false){
        if($userID === false){$userID = $this->getUserID();}
        if(is_array($vars)){
            return $this->db->update($this->table_users, array('settings' => serialize(array_filter($vars))), array('id' => $userID), 1);
        }
        return false;
    }
}
