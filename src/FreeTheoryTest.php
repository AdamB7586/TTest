<?php
namespace TheoryTest\Car;

class FreeTheoryTest extends TheoryTest{
    
    protected $scriptVar = 'free';
    
    /**
     * Create a new Theory Test for the test number given
     * @param int $theorytest Should be the test number
     */
    public function createNewTest($theorytest = 1){
        $this->clearCookies($theorytest);
        $this->setTest($theorytest);
        $this->setTestName();
        $this->chooseQuestions($theorytest);
        return $this->buildTest();
    }
    
    /**
     * Clears any settings store in the cookies to start a new test
     * @param int $testNo The current test number
     * @return void nothing is returned
     */
    protected function clearCookies($testNo){
        unset($_SESSION['test'.$testNo.'q']);
        unset($_SESSION['test'.$testNo]);
        unset($_SESSION['current_test']);
        unset($_SESSION['question_no']);
        unset($_COOKIE['testinfo']);
        $this->clearSettings();
    }
    
    /**
     * Sets the current test number
     * @param int $testNo Sets the current test number
     */
    public function setTest($testNo){
        $this->testNo = $testNo;
        $_SESSION['current_test'] = $this->testNo;
    }
    
    /**
     * Returns the current test number
     * @return int Returns the current test number
     */
    public function getTest(){
        if(!is_numeric($this->testNo)){
            $this->testNo = $_SESSION['current_test'];
        }
        return $this->testNo;
    }
    
    /**
     * Returns the current users settings which they have enabled
     * @param boolean $new If it is a new test or not
     * @return array Returns the current users settings which they have enabled
     */
    protected function checkSettings($new = false) {
        $settings = unserialize($_SESSION['settings']);
        if($new !== true){
            if($settings['review'] == 'all'){$this->review = (string) 'all';}
            elseif($settings['review'] == 'flagged'){$this->review = (string) 'flagged';}
            elseif($settings['review'] == 'incomplete'){$this->review = (string) 'incomplete';}
            elseif($settings['review'] == 'answers'){$this->review = (string) 'answers';}
        }
        else{$this->review = false;}
        if($settings['audio'] == 'on'){$this->audioEnabled = true;}
        return $settings;
    }
    
    /**
     * Clears the current settings from the users session
     * @return void Nothing is returned
     */
    protected function clearSettings(){
        unset($_SESSION['settings']);
        unset($_SESSION['time_remaining']);
        unset($_SESSION['time_taken']);
    }
    
    /**
     * Changed the settings to change the review type for the current test
     * @param string $type This is the type of question which is about to be reviewed (e.g. flagged, all, incomplete, etc)
     * @return void Nothing is returned
     */
    public function reviewOnly($type = 'all'){
        $settings = $this->checkSettings();
        $settings['review'] = $type;
        $_SESSION['settings'] = serialize($settings);
    }
    
    /**
     * Changes if the audio is displayed or not
     * @param string $status Should be set to either on or off
     * @return void Nothing is returned
     */
    public function audioEnable($status = 'on'){
        if($status == 'on'){$this->audioEnabled = true;}else{$this->audioEnabled = false;}
        $settings = $this->checkSettings();
        $settings['audio'] = $status;
        $_SESSION['settings'] = serialize($settings);
    }
    
    /**
     * Sets the Test Name
     * @param string $name Sets the current test name
     */
    protected function setTestName($name = ''){
        if(!empty($name)){
            $this->testName = $name;
        }
        else{
            $this->testName = 'Free Theory Test '.$this->getTest();
        }
    }

    /**
     * Chooses the test questions an inserts them into the session
     * @param int $testNo The current test number
     * @return boolean Returns true
     */
    protected function chooseQuestions($testNo){
        $questions = $this->db->selectAll($this->questionsTable, ['mocktestcarno' => $testNo], ['prim'], ['mocktestcarqposition' => 'ASC']);
        $q = 1;
        foreach($questions as $question){
            $this->questions[$q] = $question['prim'];
            $q++;
        }
        $_SESSION['test'.$testNo.'q'] = serialize($this->questions);
        return true;
    }
    
    /**
     * Gets the questions array from the database if $this->questions is not set
     * @return array Returns the questions array
     */
    public function getQuestions(){
        if(!isset($this->questions)){
            $this->questions = unserialize($_SESSION['test'.$this->getTest().'q']);
        }
        return $this->questions;
    }
    
    /**
     * Returns the current users answers for the current test
     * @return array Returns the current users answers for the current test
     */
    public function getUserAnswers() {
        if(!isset($this->useranswers) && isset($_SESSION['test'.$this->getTest()])){
            $this->useranswers = $_SESSION['test'.$this->getTest()];
        }
        return $this->useranswers;
    }
    
    /**
     * Updates the useranswers field in the progress table in the database
     * @return void Nothing is returned
     */
    protected function updateAnswers(){
        return false;
    }
    
    /**
     * Sets and returns the current question number
     * @return int Returns the current question number
     */
    protected function currentQuestion(){
        if(!isset($this->current) && isset($_SESSION['question_no']['free'])){
            $this->current = $_SESSION['question_no']['free'];
        }
        elseif(!isset($this->current)){
            $this->current = 1;
        }
        return $this->current;
    }
    
    /**
     * Updates the current test progress
     * @param int $prim The prim number of the current question
     * @return void Nothing is returned
     */
    protected function updateTestProgress($prim){
        $this->current = $this->questionNo($prim);
        $_SESSION['question_no']['free'] = $this->current;
    }
    
    /**
     * This should be called at the very start of the free test to that no information is added unnecessarily. This function assigns values to the tamples to start the test
     */
    protected function startTest(){
        $this->layout->assign('script', $this->existingScript());
        $this->questiondata = $this->layout->fetch('existing.tpl');
    }
    
    /**
     * Checks a cookie to see if the test has started
     * @return boolean If the test has been started will return true else return false
     */
    private function testStarted(){
        if(isset($_COOKIE['started']) && is_numeric($_COOKIE['started'])){
            return true;
        }
        return false;
    }
    
    /**
     * Sets the cookie to say that the test has been started
     * @return void Nothing is returned
     */
    public function startNewTest(){
        setcookie('started', 1, time() + 3600, '/', '', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? true : false), true);
    }
    
    /**
     * Creates the HTML for an entire Theory Test for use when creating a new test
     * @return void Nothing is returned
     */
    public function buildTest(){
        if(!$this->testStarted()){$this->startTest();}
        else{$this->createQuestionHTML($this->getFirstQuestion(), true); setcookie('started', 0, time() - 3600);}
        $this->layout->assign('test_name', $this->getTestName(), true);
        $this->layout->assign('question_no', '1', true);
        $this->layout->assign('no_questions', $this->numQuestions(), true);
        $this->layout->assign('question_data', $this->questiondata, true);
        $this->layout->assign('js_script_location', $this->getJavascriptLocation());
        $this->layout->assign('report', false);
        return $this->layout->fetch($this->section.'test.tpl');
    }
    
    /**
     * Sets the time taken an remaining in the session variables
     * @param int $time The current time in seconds
     * @param string $type This should be set to either taken or remaining
     * @return void nothing is returned
     */
    public function setTime($time, $type = 'taken'){
        if($time){
            if($type == 'taken'){
                list($mins, $secs) = explode(':', $time);
                $time = gmdate('i:s', ($this->seconds - (($mins * 60) + $secs)));
            }
            $_SESSION['time_'.$type] = $time;
        }
    }
    
    /**
     * Gets the Time taken or 'remaining for the current test
     * @param string $type This should be either set to 'taken' or 'remaining' depending on which you wish to get 'taken' by default
     * @return string Returns the time from the database
     */
    public function getTime($type = 'taken'){
        return $_SESSION['time_'.$type];
    }
    
    /**
     * Marks the current test
     */
    protected function markTest(){
        foreach($this->getQuestions() as $prim){
             if($this->getUserAnswers()[$this->questionNo($prim)]['status'] == 4){$type = 'correct';}
             else{$type = 'incorrect';}
             
             $dsa = $this->getDSACat($prim);
             $this->testresults['dsa'][$dsa][$type] = (int)$this->testresults['dsa'][$dsa][$type] + 1;
        }
        
        $this->testresults['id'] = date('jn').'-'.rand(1000, 9999);
        $this->testresults['correct'] = $this->numCorrect();
        $this->testresults['incorrect'] = ($this->numQuestions() - $this->numCorrect());
        $this->testresults['incomplete'] = $this->numIncomplete();
        $this->testresults['flagged'] = $this->numFlagged();
        $this->testresults['numquestions'] = $this->numQuestions();
        $this->testresults['percent']['correct'] = round(($this->testresults['correct'] / $this->testresults['numquestions']) * 100);
        $this->testresults['percent']['incorrect'] = round(($this->testresults['incorrect'] / $this->testresults['numquestions']) * 100);
        $this->testresults['percent']['flagged'] = round(($this->testresults['flagged'] / $this->testresults['numquestions']) * 100);
        $this->testresults['percent']['incomplete'] = round(($this->testresults['incomplete'] / $this->testresults['numquestions']) * 100);
        $this->testresults['complete'] = date('Y-m-d H:i:s');
        if($this->numCorrect() >= $this->passmark){
            $this->testresults['status'] = 'pass';
        }
        else{
            $this->testresults['status'] = 'fail';
        }
        $_SESSION['results'] = serialize($this->testresults);
    }
    
    /**
     * Returns the test results for the current test
     * @return array Returns the test results for the current test as an array
     */
    public function getTestResults(){
        $this->testresults = unserialize($_SESSION['results']);
        return $this->testresults;
    }
    
    /**
     * Returns the print certificate/report button
     * @return array Returns the print certificate/report variables
     */
    protected function printCertif(){
        $certificate = parent::printCertif();
        $certificate['location'] = $certificate['location'].'&amp;type=free';
        return $certificate;
    }
    
    /**
     * Returns the test report table to be displayed
     * @return string Returns the test report table to be displayed
     */
    protected function testReport(){
        $this->layout->assign('free_test', 'Yes', true);
        $report = parent::testReport();
        unset($report['user']);
        return $report;
    }
}