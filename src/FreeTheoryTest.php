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
        unset($_SESSION['test'.$testNo.'a']);
        unset($_SESSION['test'.$testNo]);
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
        if($this->testNo){
            return $this->testNo;
        }
        else{
            $this->testNo = $_SESSION['current_test'];
            return $this->testNo;
        }
    }
    
    /**
     * Returns the current users settings which they have enabled
     * @param boolean $new If it is a new test or not
     * @return array Returns the current users settings which they have enabled
     */
    protected function checkSettings($new = false) {
        $settings = unserialize($_SESSION['settings']);
        if($new !== true){
            if($settings['review'] == 'all'){$this->review = 'all';}
            elseif($settings['review'] == 'flagged'){$this->review = 'flagged';}
            elseif($settings['review'] == 'incomplete'){$this->review = 'incomplete';}
            elseif($settings['review'] == 'answers'){$this->review = 'answers';}
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
            $this->testName = '<span class="hidden-xs">Free Theory </span>Test '.$this->getTest();
        }
    }

    /**
     * Chooses the test questions an inserts them into the session
     * @param int $testNo The current test number
     * @return boolean Returns true
     */
    protected function chooseQuestions($testNo){
        if(!session_id()){
            session_name(SESSION_NAME);
            session_start();
        }
        $questions = self::$db->selectAll($this->questionsTable, array('mocktestcarno' => $testNo), array('prim'), array('mocktestcarqposition' => 'ASC'));
        $q = 1;
        foreach($questions as $question){
            $this->questions[$q] = $question['prim'];
            $this->useranswers[$q]['answer'] = '';
            $this->useranswers[$q]['flagged'] = 0;
            $this->useranswers[$q]['status'] = 0;
            $q++;
        }
        $_SESSION['test'.$testNo.'q'] = serialize($this->questions);
        $_SESSION['test'.$testNo.'a'] = serialize($this->useranswers);
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
        if(!isset($this->useranswers)){
            $this->useranswers = unserialize($_SESSION['test'.$this->getTest().'a']);
        }
        return $this->useranswers;
    }
    
    /**
     * Updates the useranswers field in the progress table in the database
     * @return void Nothing is returned
     */
    protected function updateAnswers(){
        $_SESSION['test'.$this->getTest().'a'] = serialize($this->useranswers);
    }
    
    /**
     * Sets and returns the current question number
     * @return int Returns the current question number
     */
    protected function currentQuestion(){
        if(!isset($this->current)){
            $this->current = $_SESSION['question_no'];
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
        $_SESSION['question_no'] = $this->current;
    }
    
    /**
     * This should be called at the very start of the free test to that no information is added unnecessarily. This function assigns values to the tamples to start the test
     */
    protected function startTest(){        
        $text = '<p>Please click the start test button below when you are ready to start. Please make sure you do not navigate away from the page as you will not be able to pick up from where you left the test.</p>';
        self::$layout->assign('existing_text', $text);
        self::$layout->assign('start_new_test', '<div class="newtest btn btn-theory"><span class="fa fa-refresh fa-fw"></span><span class="hidden-xs"> Start Test</span></div>');
        self::$layout->assign('script', $this->existingScript());
        $this->questiondata = self::$layout->fetch('theory'.DS.'existing.tpl');
    }
    
    /**
     * Checks a cookie to see if the test has started
     * @return boolean If the test has been started will return true else return false
     */
    private function testStarted(){
        if(is_numeric($_COOKIE['started'])){
            return true;
        }
        return false;
    }
    
    /**
     * Sets the cookie to say that the test has been started
     * @return void Nothing is returned
     */
    public function startNewTest(){
        setcookie('started', 1, time() + 3600, '/');
    }
    
    /**
     * Creates the HTML for an entire Theory Test for use when creating a new test
     * @return void Nothing is returned
     */
    public function buildTest(){
        if(!$this->testStarted()){$this->startTest();}
        else{$this->createQuestionHTML($this->getFirstQuestion(), true); setcookie('started', 0, time() - 3600);}
        self::$layout->assign('test_name', $this->getTestName(), true);
        self::$layout->assign('question_no', '1', true);
        self::$layout->assign('no_questions', $this->numQuestions(), true);
        self::$layout->assign('question_data', $this->questiondata, true);
        return self::$layout->fetch('theory'.DS.$this->section.'test.tpl');
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
     * @return void Nothing is returned
     */
    protected function markTest(){
        $this->getQuestions();
        foreach($this->questions as $prim){
             if($_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['status'] == 4){$type = 'correct';}
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
     * @return string Returns the print certificate/report button depending on how the user has done in the test
     */
    protected function printCertif(){
        if($this->testresults['status'] == 'pass'){
            return '<a href="/certificate.pdf?type=free&amp;testID='.$this->getTest().'" title="Print Certificate" target="_blank" class="printcert btn btn-theory"><span class="fa fa-print fa-fw"></span><span class="hidden-xs"> Print Certificate</span></a>';
        }
        return '<a href="/certificate.pdf?type=free&amp;testID='.$this->getTest().'" title="Print Results" target="_blank" class="printcert btn btn-theory"><span class="fa fa-print fa-fw"></span><span class="hidden-xs"> Print Results</span></a>';
    }
    
    /**
     * Returns the test report table to be displayed
     * @return string Returns the test report table to be displayed
     */
    protected function testReport(){
        if(!$this->user->getUserID()){self::$layout->assign('free_test', 'Yes', true);}
        $this->getTestResults();
        $report['testname'] = ucwords($this->getTestName());
        $report['status'] = $this->testStatus();
        $report['time'] = $this->getTime();
        $report['passmark'] = $this->passmark;
        $report['testdate'] = date('d/m/Y', strtotime($this->testresults['complete']));
        return $report;
    }
}
