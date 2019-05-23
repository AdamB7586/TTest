<?php
namespace TheoryTest\Car;

class RandomTest extends TheoryTest{
    protected $testNo = 15;
    
    protected $scriptVar = 'random';
    
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
        $this->db->delete($this->progressTable, ['user_id' => $this->user->getUserID(), 'test_id' => $testNo]);
        $questions = $this->db->query("SELECT * FROM ((SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '1' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 2)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '2' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 3)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '3' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 4)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '4' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 3)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '5' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 5)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '6' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 4)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '7' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 2)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '8' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 4)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '9' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 3)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '10' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 4)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '11' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 6)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '12' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 1)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '13' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 3)
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `dsacat` = '14' AND `carquestion` = 'Y' AND `alertcasestudy` IS NULL LIMIT 1) ORDER BY RAND()) as a
UNION (SELECT `prim` FROM `{$this->questionsTable}` WHERE `casestudyno` = '".rand(1, 28)."');");
         
        $q = 1;
        unset($_SESSION['test'.$this->getTest()]);
        if(is_array($questions)){
            foreach($questions as $question){
                $this->questions[$q] = $question['prim'];
                $q++;
            }
        }
        return $this->db->insert($this->progressTable, ['user_id' => $this->user->getUserID(), 'questions' => serialize($this->questions), 'answers' => serialize([]), 'test_id' => $testNo, 'started' => date('Y-m-d H:i:s'), 'status' => 0, 'type' => strtolower($this->getTestType())]);
    }
    
    /**
     * Sets the current test name
     * @param string $name This should be the name of the test you wish to set it to if left blank will just be Theory Test plus test number
     */
    protected function setTestName($name = '') {
        if(!empty($name)) {
            $this->testName = $name;
        }
        else{
            $this->testName = 'Random Theory Test';
        }
    }
}
