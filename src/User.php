<?php

namespace TheoryTest\Car;

class User extends \UserAuth\User
{
    
    /**
     * Returns the users name if logged in else return false
     * @return string|boolean
     */
    public function getUsername($user_id = false)
    {
        if ($this->getUserID() !== 0 || is_numeric($user_id)) {
            return $this->getFirstname($user_id);
        }
        return false;
    }
    
    /**
     * Returns the users first name from the users information if they are logged in
     * @return string|false This should be the users first name or false if they don't exist
     */
    public function getFirstname($user_id = false)
    {
        if (empty($this->userInfo) || is_numeric($user_id)) {
            $userInfo = $this->getUserInfo($user_id);
            if (is_array($userInfo)) {
                return $userInfo['first_name'];
            }
            return false;
        }
        return $this->userInfo['first_name'];
    }
    
    /**
     * Returns the users last name from the users information if they are logged in
     * @return string|false This should be the users last name or false if they don't exist
     */
    public function getLastname($user_id = false)
    {
        if (empty($this->userInfo) || is_numeric($user_id)) {
            $userInfo = $this->getUserInfo($user_id);
            if (is_array($userInfo)) {
                return $userInfo['last_name'];
            }
            return false;
        }
        return $this->userInfo['last_name'];
    }
    
    /**
     * Checks to see if the user has upgraded their account and has access to the given test/learning section
     * @param int $testID This should be the test ID you are checking if the user has access to
     * @return boolean|void If the user has access will return try else will redirect the user to the upgrade page
     */
    public function checkUserAccess($testID = 100, $type = 'account')
    {
        return true;
    }
    
    /**
     * Returns any stored settings from the database that the user may have
     * @param int|false $userID Set this to get settings for user set to user ID or false for current user
     * @return array
     */
    public function getUserSettings($userID = false)
    {
        if ($userID === false) {
            $userID = $this->getUserID();
        }
        $userInfo = $this->getUserInfo($userID);
        if (is_string($userInfo['settings']) && !empty($userInfo['settings'])) {
            return unserialize($userInfo['settings']);
        }
        return [];
    }
    
    /**
     * Sets the stored settings in the database for the given user
     * @param array $vars This should be an array of any settings you wish to add the the user
     * @param int $userID This should be the user ID that you are applying the settings update to
     * @return boolean If the settings are updated successfully will return true else returns false
     */
    public function setUserSettings($vars, $userID = false)
    {
        if ($userID === false) {
            $userID = $this->getUserID();
        }
        if (is_array($vars)) {
            $settings = array_filter($vars);
            return $this->db->update($this->table_users, [
                'settings' => (empty($settings) ? 'NULL' : serialize($settings))
            ], ['id' => $userID], 1);
        }
        return false;
    }
}
