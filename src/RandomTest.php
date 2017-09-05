<?php
namespace TheoryTest\Car;

use DBAL\Database;
use Smarty;
use UserAuth\User;

class RandomTest extends TheoryTest{
    protected $testName = 'Random <span class="hidden-xs"> Theory</span> Test';
    protected $testNo = 15;
    
    protected $scriptVar = 'random';


    /**
     * Set up all of the components needed to create a Theory Test
     * @param Database $db This should be an instance of Database
     * @param Smarty $layout This needs to be an instance of Smarty Templating
     * @param User $user This should be and instance if the User Class
     * @param false|int $userID If you wish to emulate a user set this value to the users ID else set to false
     */
    public function __construct(Database $db, Smarty $layout, User $user, $userID = false) {
        parent::__construct($db, $layout, $user, $userID);
    }
    
    /**
     * Create a new Random Theory Test for the test number given
     * @param int $theorytest Should be the test number
     * @return string Returns the HTML for a test
     */
    public function createNewTest($theorytest = 15){
        $this->clearSettings();
        $this->setTest($this->testNo);
        $this->setTestName($this->testName);
        if($this->anyExisting() === false){
            $this->chooseQuestions($this->testNo);
        }
        return $this->buildTest();
    }
    
    /**
     * Chooses the random questions for the test and inserts them into the database
     * @param int $testNo This should be the test number you which to get the questions for
     * @return boolean If the questions are inserted into the database will return true else returns false
     */
    protected function chooseQuestions($testNo) {
        $this->db->delete($this->progressTable, array('user_id' => self::$user->getUserID(), 'test_id' => $testNo));
        $questions = $this->db->query("SELECT * FROM ((SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '1' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 2)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '2' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 3)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '3' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 4)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '4' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 3)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '5' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 5)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '6' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 4)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '7' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 2)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '8' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 4)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '9' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 3)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '10' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 4)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '11' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 6)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '12' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 1)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '13' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 3)
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `dsacat` = '14' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 1) ORDER BY RAND()) as a
UNION (SELECT `prim` FROM `".$this->questionsTable."` WHERE `casestudyno` = '".rand(1, 28)."');");
         
        $q = 1;
        foreach($questions as $question){
            $this->questions[$q] = $question['prim'];
            $q++;
        }
        return $this->db->insert($this->progressTable, array('user_id' => self::$user->getUserID(), 'questions' => serialize($this->questions), 'answers' => serialize(array()), 'test_id' => $testNo, 'started' => date('Y-m-d H:i:s'), 'status' => 0, 'type' => strtolower($this->getTestType())));
    }
}
