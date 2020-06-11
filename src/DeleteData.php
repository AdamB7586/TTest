<?php

namespace TheoryTest\Car;

use DBAL\Database;
use Configuration\Config;

class DeleteData {
    
    protected $db;
    protected $config;
    protected $user;

    public $learningProgressTable;
    public $progressTable;
    
    /**
     * Connects to the database and passes the user class
     * @param Database $db This should e an instance of the Database class
     * @param type $user This should be an instance of the user class
     */
    public function __construct(Database $db, Config $config, $user) {
        $this->db = $db;
        $this->config = $config;
        $this->user = $user;
        $this->setTables();
    }
    
    /**
     * Sets the tables
     */
    protected function setTables(){
        $this->learningProgressTable = $this->config->table_users_progress;
        $this->progressTable = $this->config->table_users_test_progress;
    }
    
    /**
     * Deletes all of the learning section data but not the tests
     * @param int|false $userID This should be the users ID if not deleting data for the current user else set to false
     * @return boolean If the information is deleted will return true else returns false
     */
    public function deleteOnlyLearningProgress($userID = false) {
        if($userID === false){$userID = $this->user->getUserID();}
        if(is_numeric($userID)){
            return $this->db->delete($this->learningProgressTable, ['user_id' => $userID]);
        }
        return false;
    }
    
    /**
     * Deletes all of the test data but not the learning sections
     * @param int|false $userID This should be the users ID if not deleting data for the current user else set to false
     * @return boolean If the information is deleted will return true else returns false
     */
    public function deleteOnlyTestData($userID = false) {
        if($userID === false){$userID = $this->user->getUserID();}
        if(is_numeric($userID)){
            return $this->db->delete($this->progressTable, ['user_id' => $userID]);
        }
        return false;
    }

    /**
     * Deletes all of the theory test data for a given user, if the user is not assigned will delete data for the current user
     * @param int|false $userID This should be the users ID if not deleting data for the current user else set to false
     * @return boolean If the information is deleted will return true else returns false
     */
    public function deleteData($userID = false) {
        if($this->deleteOnlyLearningProgress($userID) || $this->deleteOnlyTestData($userID)){
            return true;
        }
        return false;
    }
}
