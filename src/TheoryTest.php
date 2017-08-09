<?php
namespace TheoryTest;

use TheoryTest\Essential\TTInterface;
use Smarty;
use DBAL\Database;
use User;

/**
 * Produces a Theory Test including HTML5 audio if compatible. Requires a number of external files and classes in order to work correctly.
 * 
 * @package LDC Theory Test
 * @author Learner Driving Centres <https://www.learnerdriving.com>
 * @author Adam Binnersley <adam.binnersley@learnerdriving.com>
 * @version LDC Theory Test 1.0
 * @copyright &copy; Teaching Driving Ltd
 * @link https://www.learnerdriving.com
 */
class TheoryTest implements TTInterface{
    protected static $db;
    protected static $layout;
    protected static $user;
    
    protected $testID;
    protected $exists = false;
    public $passmark = 43;
    
    public $learningProgressTable = 'users_progress';
    public $questionsTable = 'theory_questions_2016';
    public $progressTable = 'users_test_progress';
    public $caseTable = 'theory_case_studies';
    public $dsaCategoriesTable = 'theory_dsa_sections';
	
    protected $audioLocation = '/audio';
    
    protected $seconds = 3420;
    protected $section = 'theory';
    
    public $audioEnabled = false;
    public $questions;
    public $useranswers;
    
    protected $testNo;
    protected $testName;
    
    protected $questiondata;
    protected $current;
    
    protected $casestudy;
    protected $review = false;
    public $testresults;
    public $dsacat;
    
    protected static $testType = 'car'; //Done
    
    /**
     * Connects to the database sets the current user and gets any user answers
     * @param Database $db
     * @param Smarty $layout
     * @param User $user
     */
    public function __construct(Database $db, Smarty $layout, User $user){
        self::$db = $db;
        self::$user = $user;
        self::$layout = $layout;
        self::$layout->addTemplateDir(dirname(__FILE__).DS.'templates');
        $this->getUserAnswers();
    }
    
    /**
     * Create a new Theory Test for the test number given
     * @param int $theorytest Should be the test number
     * @return string Returns the HTML for a test
     */
    public function createNewTest($theorytest = 1){
        $this->clearSettings();
        $this->setTest($theorytest);
        self::$user->checkUserAccess($theorytest);
        $this->setTestName();
        if($this->anyExisting() === false){
            $this->chooseQuestions($theorytest);
        }
        return $this->buildTest();
    }
    
    /**
     * Sets the Test Type for the current test the default is car
     * @param string $type This should be the type of test the user is currently undertaking
     * @return $this
     */
    public function setTestType($type){
        self::$testType = strtoupper($type);
        return $this;
    }
    
    /**
     * Gets the current test type
     * @return string Will return the current test type
     */
    public function getTestType(){
        return strtoupper(self::$testType);
    }
    
    /**
     * Sets the passmark for the test the default is set to 43 which is what is set by the DVSA
     * @param int $mark This should be the passmark for the test (no greater than 50 as only 50 questions are retrieved)
     * @return $this
     */
    public function setPassmark($mark){
        if(is_int($mark)){
            $this->passmark = intval($mark);
        }
        return $this;
    }
    
    /**
     * Returns the current passmark for the test
     * @return int Returns the set passmark for the current test
     */
    public function getPassmark(){
        return intval($this->passmark);
    }
    
    /**
     * Sets the amount of seconds that should be allowed to undertake a test
     * @param int $seconds If you wish to change the seconds allowed from the 57 minutes (3420 seconds) set the number in seconds 
     * @return $this
     */
    public function setSeconds($seconds){
        if(is_int($seconds)){
            $this->seconds = intval($seconds);
        }
        return $this;
    }
    
    /**
     * Gets the amount of seconds that are allowed for the current test
     * @return int This should be the number of sends allowed to partake the test
     */
    public function getStartSeconds(){
        return $this->seconds;
    }
    
    /**
     * Sets the location where the audio files can be found
     * @param string $location The should either be a URL or a relative position (minus mp4 & ogg folders)
     * @return $this
     */
    public function setAudioLocation($location){
        $this->audioLocation = $location;
        return $this;
    }
    
    /**
     * Returns the currents set location of the audio files
     * @return string This should be the folder where all the audio files can be found
     */
    public function getAudioLocation(){
        return $this->audioLocation;
    }
    
    /**
     * Creates the test report HTML if the test has been completed
     * @param int $theorytest The test number you wish to view the report for
     * @return string Returns the HTML for the test report for the given test ID
     */
    public function createTestReport($theorytest = 1){
        $this->setTest($theorytest);
        if($this->getTestResults()){
            $this->setTestName();
            return $this->buildReport(false);
        }
        return self::$layout->fetch('report'.DS.'report-unavail.tpl');
    }

    /**
     * Choose the questions for the test
     * @param int $testNo This should be the test number you which to get the questions for
     * @return boolean If the test questions are inserted into the database will return true else returns false
     */
    protected function chooseQuestions($testNo){
        $questions = self::$db->selectAll($this->questionsTable, array('mocktestcarno' => $testNo), array('prim'), array('mocktestcarqposition' => 'ASC'));
        self::$db->delete($this->progressTable, array('user_id' => self::$user->getUserID(), 'test_id' => $testNo, 'type' => $this->getTestType()/*, 'status' => 0*/));
        $q = 1;
        foreach($questions as $question){
            $this->questions[$q] = $question['prim'];
            $_SESSION['test'.$this->getTest()][$q]['answer'] = ''; $_SESSION['test'.$this->getTest()][$q]['flagged'] = 0; $_SESSION['test'.$this->getTest()][$q]['status'] = 0;
            $q++;
        }
        return self::$db->insert($this->progressTable, array('user_id' => self::$user->getUserID(), 'questions' => serialize($this->questions), 'answers' => serialize(array()), 'test_id' => $testNo, 'started' => date('Y-m-d H:i:s'), 'status' => 0, 'type' => $this->getTestType()));
    }
    
    /**
     * Checks to see if their is currently a test which is not complete or a test which has already been passed
     * @return string|false
     */
    protected function anyExisting(){
        $existing = self::$db->select($this->progressTable, array('user_id' => self::$user->getUserID(), 'test_id' => $this->getTest(), 'type' => $this->getTestType(), 'status' => array('<=', 1)));
        if(!empty($existing)){
            $this->exists = true;
            if($existing['status'] == 1){return 'passed';}
            else{return 'exists';}
        }
        return false;
    }
    
    /**
     * Returns the JavaScript script to be displayed on the page
     * @return string Returns the JavaScript script to be displayed on the page
     */
    protected function existingScript(){
        return '<script type="text/javascript" src="/js/theory/existing-questions.js"></script>';
    }

    /**
     * If a test already exist for the test ID this will add variables for the template to displayed a confirmation of new test
     * @return void Nothing is returned
     */
    protected function existingLayout(){        
        if($this->anyExisting() === 'passed'){
            $text = '<p>You have already passed this test! Are you sure you want to start a new test?</p><div class="timeremaining" id=""></div>';
            $continue = '';
        }
        else{
            $text = '<p>You have already started this test! Would you like to continue this test or start a new one?</p><div class="timeremaining" id="'.$this->getSeconds().'"></div>';
            $continue = '<div class="continue btn btn-theory" id="'.$this->questionPrim($this->currentQuestion()).'"><span class="fa fa-long-arrow-right fa-fw"></span><span class="hidden-xs"> Continue Test</span></div>';
        }
        
        self::$layout->assign('existing_text', $text);
        self::$layout->assign('start_new_test', '<div class="newtest btn btn-theory"><span class="fa fa-refresh fa-fw"></span><span class="hidden-xs"> Start New Test</span></div>');
        self::$layout->assign('continue_test', $continue);
        self::$layout->assign('script', $this->existingScript());
        $this->questiondata = self::$layout->fetch('existing.tpl');
    }
        
    /**
     * Gets the questions array from the database if $this->questions is not set
     * @return array|false Returns the questions array if it exists else returns false
     */
    public function getQuestions(){
        if(!isset($this->questions)){
            $questions = self::$db->select($this->progressTable, array('user_id' => self::$user->getUserID(), 'test_id' => $this->getTest(), 'type' => $this->getTestType()), array('questions'), array('started' => 'DESC'));
            if(!empty($questions)){
                $this->questions = unserialize($questions['questions']);
                return $this->questions;
            }
            return false;
        }
    }
    
    /**
     * Returns the current users answers for the current test
     * @return array|false Returns the current users answers for the current test if any exist else returns false
     */
    public function getUserAnswers(){
        if(!isset(self::$useranswers)){
            $answers = self::$db->select($this->progressTable, array('user_id' => self::$user->getUserID(), 'test_id' => $this->getTest(), 'type' => $this->getTestType()), array('id', 'answers', 'question_no'), array('started' => 'DESC'));
            if(!empty($answers)){
                self::$useranswers = unserialize($answers['answers']);
                if(!is_array($_SESSION['test'.$this->getTest()])){$_SESSION['test'.$this->getTest()] = self::$useranswers;}
                if(!is_numeric($_SESSION['question_no']['test'.$this->getTest()])){$_SESSION['question_no']['test'.$this->getTest()] = $answers['question_no'];}
                $this->testID = $answers['id'];
                return self::$useranswers;
            }
            return false;
        }
    }
    
    /**
     * Returns the number of questions in the test
     * @return int Returns the number of questions
     */
    public function numQuestions(){
        $this->getQuestions();
        return count($this->questions);
    }
    
    /**
     * Sets and returns the current question number
     * @return int Returns the current question number
     */
    protected function currentQuestion(){
        if(!isset($this->current)){
            $this->current = $_SESSION['question_no']['test'.$this->getTest()];
        }
        return $this->current;
    }
    
    /**
     * Returns the prim number for any given question number
     * @param int $questionNo This should be the question number in the current test
     * @return int Returns the unique prim number for the question
     */
    public function questionPrim($questionNo){
        $this->getQuestions();
        return $this->questions[$questionNo];
    }
    
    /**
     * Gets the question number of a given prim for the test
     * @param int $prim This should be the prim number of the question you wish to fin the question number for
     * @return int Returns the question number
     */
    public function questionNo($prim){
        $this->getQuestions();
        $key = array_keys($this->questions, $prim);
        return $key[0];
    }
    
    /**
     * Returns the prim number of the first question
     * @return int Returns the first question prim number
     */
    protected function getFirstQuestion(){
        $this->getQuestions();
        return $this->questions[1];
    }
    
    /**
     * Returns the prim number of the last question
     * @return int Returns the last question prim number
     */
    protected function getLastQuestion(){
        $this->getQuestions();
        return $this->questions[$this->numQuestions()];
    }
    
    /**
     * Returns the next flagged question number
     * @param int $current This should be the current question 
     * @return int|false Returns the next question ID if one exists else will return false
     */
    public function getNextFlagged($current = ''){
        if(!is_numeric($current)){$current = $this->currentQuestion();}
        foreach($_SESSION['test'.$this->getTest()] as $question => $value){
            if($question > $current && $value['flagged'] == 1){
                return $question;
            }
        }
        if($this->numFlagged() > 1){
            return $this->getNextFlagged(0);
        }
        return false;
    }
    
    /**
     * Returns the previous flagged question number
     * @param int $current This should be the current question
     * @return int|boolean Returns the previous question ID if one exists else will return false
     */
    public function getPrevFlagged($current = ''){
        if(!is_numeric($current)){$current = $this->currentQuestion();}
        foreach($_SESSION['test'.$this->getTest()] as $question => $value){
            if($question < $current && $value['flagged'] == 1){
                return $question;
            }
        }
        if($this->numFlagged() > 1){
            return $this->getPrevFlagged($this->numQuestions() + 1);
        }
        return false;
    }
    
    /**
     * Returns the next incomplete question
     * @param int $current This should be the current question
     * @return int|boolean Returns the next incomplete question ID if one exists else will return false
     */
    public function getNextIncomplete($current = ''){
        if(!is_numeric($current)){$current = $this->currentQuestion();}
        foreach($_SESSION['test'.$this->getTest()] as $question => $value){
            if($question > $current && $value['status'] < 3){
                return $question;
            }
        }
        if($this->numIncomplete() > 1){
            return $this->getNextIncomplete(0);
        }
        return false;
    }
    
    /**
     * Returns the previous incomplete question
     * @param int $current This should be the current question
     * @return int|boolean Returns the previous incomplete question ID if one exists else will return false
     */
    public function getPrevIncomplete($current = ''){
        if(!is_numeric($current)){$current = $this->currentQuestion();}
        foreach($_SESSION['test'.$this->getTest()] as $question => $value){
            if($question < $current && $value['status'] < 3){
                return $question;
            }
        }
        if($this->numIncomplete() > 1){return $this->getPrevIncomplete($this->numQuestions() + 1);}
        return false;
    }
    
    /**
     * Change the audio enabled settings
     * @param string $status Should be set to either 'on' or 'off'
     * @return boolean if the settings are updated will return true else returns false
     */
    public function audioEnable($status = 'on'){
        if($status == 'on'){$this->audioEnabled = true;}else{$this->audioEnabled = false;}
        $settings = $this->checkSettings();
        $settings['audio'] = $status;
        self::$user->setUserSettings($settings);
    }
    
    /**
     * Returns the HTML5 audio HTML information as a string
     * @param int $prim This should be the question prim number
     * @param string $letter This should be the letter of the question or answer
     * @return string Returns the HTML needed for the audio
     */
    protected function addAudio($prim, $letter, $folder = ''){
        if($this->audioEnabled && is_numeric($prim)){
            return '<div class="sound fa fa-fw fa-volume-up" id="audioanswer'.$letter.$prim.'"><audio id="audio'.$letter.$prim.'" preload="auto"><source src="'.$this->getAudioLocation().$folder.'/mp3/'.strtoupper($letter).$prim.'.mp3" type="audio/mpeg"><source src="'.$this->getAudioLocation().$folder.'/ogg/'.strtoupper($letter).$prim.'.ogg" type="audio/ogg"></audio></div>';
        }
    }
    
    /**
     * Returns the audio switch button
     * @return string|boolean If the user can play audio the button will be returned else returns false
     */
    protected function audioButton(){
        if($this->audioEnabled == true){return '<div class="audioswitch audiooff"><span class="fa-stack fa-lg"><span class="fa fa-volume-up fa-stack-1x"></span><span class="fa fa-ban fa-stack-2x text-danger"></span></span><span class="sr-only">Turn Sound OFF</span></div>';}
        else{return '<div class="audioswitch audioon"><span class="fa-stack fa-lg"><span class="fa fa-volume-up fa-stack-1x"></span></span><span class="sr-only">Turn Sound ON</span></div>';}
    }
    
    /**
     * Updates the database to enable or disable the hint button and display/hide contents
     * @return boolean Returns true if DB updated else returns false
     */
    public function hintEnable(){
        $settings = $this->checkSettings();
        if($settings['hint'] == 'on'){$settings['hint'] = 'off';}
        else{$settings['hint'] = 'on';}
        self::$user->setUserSettings($settings);
    }
    
    /**
     * Returns the image HTML if the image exists else returns false
     * @param string $file Should be the image name and extension
     * @param boolean $main If the image is from the question should be set to true
     * @return string|boolean Returns HTML image string if exists else returns false
     */
    public function createImage($file, $main = false){
        if($file != NULL && $file != '' && file_exists(ROOT.DS.'images'.DS.'prim'.DS.$file)){
            if($main == true){$class = ' class="imageright questionimage img-responsive"'; $width = '273'; $height = '178';}
            else{
                list($width, $height) = getimagesize(ROOT.DS.'images'.DS.'prim'.DS.$file);
                $class = ' class="img-responsive"';
            }
            return '<img src="/images/prim/'.$file.'" alt="" width="'.$width.'" height="'.$height.'"'.$class.' />';
        }
        return false;
    }
    
    /**
     * Returns the correct JavaScript file required for the page
     * @param boolean $review If in the review section should be set to true to force script
     * @return string Returns the script needed for the page the user is currently on
     */
    protected function getScript($review = false){
        if($this->review != 'answers' && $review == false){
            return '<script type="text/javascript" src="/js/theory/theory-test-questions.js"></script>';
        }
        return '<script type="text/javascript" src="/js/theory/review-questions.js"></script>';
    }
    
    /**
     * Returns the number of answers which need to be marked HTML
     * @param int $num This should be the number of answers to select
     * @return string Returns the HTML with the number of questions to mark
     */
    protected function getMarkText($num){
        if($num == 1){return '<span class="mark" title="1">Mark one answer</span>';}
        if($num == 2){return '<span class="mark" title="2">Mark two answers</span>';}
        if($num == 3){return '<span class="mark" title="3">Mark three answers</span>';}
        if($num == 4){return '<span class="mark" title="4">Mark four answers</span>';}
    }
    
    /**
     * If reviewing a particular set of questions will provide the alert HTML of false
     * @return string|boolean Returns the alert HTML if in the correct section else return false
     */
    protected function alert(){
        if($this->review == 'flagged' || $this->review == 'incomplete'){
            return '<div class="alert alert-danger">Reviewing '.$this->review.' questions only</div>';
        }
        elseif(!$this->review && $this->numComplete() == $this->numQuestions()){
            return '<div class="msg">You have now completed all of the questions, you can mark the test by clicking the "<span class="fa fa-binoculars fa-fw"></span><span class="hidden-xs"> Review</span>" and then "<span class="endtest"><span class="fa fa-sign-out fa-fw"></span><span class="hidden-xs"> End Test</span></span>" buttons or click on the following button <div class="endtest btn btn-default">Mark my test</div></div>';
        }
        return false;
    }
    
    /**
     * Displays the correct buttons for the section
     * @param int $prim The current question prim number
     * @return string Returns the button HTML code
     */
    protected function flagHintButton($prim){
        if($this->review != 'answers'){
            return '<div class="flag'.($this->questionFlagged($prim) ? ' flagged' : '').' btn btn-theory"><span class="fa fa-flag fa-fw"></span><span class="hidden-xs"> Flag Question</span></div>';
        }
        $settings = $this->checkSettings();
        return '<div class="viewfeedback'.($settings['hint'] === 'on' ? ' flagged' : '').' btn btn-theory"><span class="fa fa-book fa-fw"></span><span class="hidden-xs"> Explain</span></div>';
    }
    
    /**
     * Returns the review button HTML code
     * @return string Returns the button HTML code
     */
    protected function reviewButton(){
        if($this->review != 'answers'){
            return '<div class="review btn btn-theory"><span class="fa fa-binoculars fa-fw"></span><span class="hidden-xs"> Review</span></div>';
        }
        return '<div class="endreview btn btn-theory"><span class="fa fa-reply fa-fw"></span><span class="hidden-xs"> End Review</span></div>';
    }
    
    /**
     * Returns the current user settings for the test
     * @param boolean $new If it is a new test should be set to true
     * @return array Returns the current test settings
     */
    protected function checkSettings($new = false){
        $settings = self::$user->getUserSettings();
        if($new != true){
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
     * Updates the test review type in the settings
     * @param string $type Should be the review type (e.g. 'all', 'flagged', 'incomplete', etc)
     * @return boolean Returns true if the settings are updated
     */
    public function reviewOnly($type = 'all'){
        $settings = $this->checkSettings();
        $settings['review'] = $type;
        return self::$user->setUserSettings($settings);
    }
    
    /**
     * Adds a given answer to the users progress in the database
     * @param string $answer This is the answer the user has just selected
     * @param int $prim The current question number to add the answer to
     * @return boolean If answer added returns true else returns false
     */
    public function addAnswer($answer, $prim){
        $qNo = $this->questionNo($prim);
        $questiondata = $this->getQuestionData($prim);
        
        $arraystring = str_replace($answer, '', trim($_SESSION['test'.$this->getTest()][$qNo]['answer'])).$answer;
        if(strlen($arraystring) > 1){
            $stringParts = str_split($arraystring);
            sort($stringParts);
            $arraystring = implode('', $stringParts);
        }
        $_SESSION['test'.$this->getTest()][$qNo]['answer'] = strtoupper($arraystring);
        if(strlen($_SESSION['test'.$this->getTest()][$qNo]['answer']) == $questiondata['mark']){
            if($_SESSION['test'.$this->getTest()][$qNo]['answer'] == $questiondata['answerletters']){$_SESSION['test'.$this->getTest()][$qNo]['status'] = 4;}
            else{$_SESSION['test'.$this->getTest()][$qNo]['status'] = 3;}
        }
        else{$_SESSION['test'.$this->getTest()][$qNo]['status'] = 1;}
        
        return $this->updateAnswers();
    }
    
    /**
     * Removes a given answer from the current question
     * @param string $answer This should be the answer you wish to remove
     * @param int $prim This should be the question prim you wish to remove the naswer from
     * @return boolean Returns true if database has been updated else return false
     */
    public function removeAnswer($answer, $prim){
        $qNo = $this->questionNo($prim);
        $_SESSION['test'.$this->getTest()][$qNo]['answer'] = str_replace(strtoupper($answer), '', $_SESSION['test'.$this->getTest()][$qNo]['answer']);
        if($_SESSION['test'.$this->getTest()][$qNo]['answer'] === ''){$_SESSION['test'.$this->getTest()][$qNo]['status'] = 0;}
        else{$_SESSION['test'.$this->getTest()][$qNo]['status'] = 1;}

        return $this->updateAnswers();
    }
    
    /**
     * Replaces the answer for the given prim number
     * @param string $answer This should be the answer the user has selected
     * @param int $prim This should be the question prim number
     * @return boolean Returns true if the answer has been updated else returns false
     */
    public function replaceAnswer($answer, $prim){
        $qNo = $this->questionNo($prim);
        $questiondata = $this->getQuestionData($prim);
        
        $_SESSION['test'.$this->getTest()][$qNo]['answer'] = strtoupper($answer);
        if($_SESSION['test'.$this->getTest()][$qNo]['answer'] == $questiondata['answerletters']){$_SESSION['test'.$this->getTest()][$qNo]['status'] = 4;}
        else{$_SESSION['test'.$this->getTest()][$qNo]['status'] = 3;}
        
        return $this->updateAnswers();
    }
    
    /**
     * Flags/Un-flags the particular question
     * @param int $prim This should be the question prim
     * @return boolean Should return true if flag status has been updated else returns false
     */
    public function flagQuestion($prim){
        if($_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['flagged'] === 0 || !$_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['flagged']){
            $_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['flagged'] = 1;
        }
        else{
            $_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['flagged'] = 0;
        }
        return $this->updateAnswers();
    }
    
    /**
     * Updates the `useranswers` field in the progress table in the database
     * @return boolean
     */
    protected function updateAnswers(){
        return self::$db->update($this->progressTable, array('answers' => serialize($_SESSION['test'.$this->getTest()]), 'time_remaining' => $_SESSION['time_remaining']['test'.$this->getTest()], 'question_no' => $_SESSION['question_no']['test'.$this->getTest()]), array('user_id' => self::$user->getUserID(), 'test_id' => $this->getTest(), 'type' => $this->getTestType(), 'id' => $this->testID));
    }
    
    /**
     * Public function to save the users information before the page is exited
     */
    public function saveProgress(){
        $this->updateAnswers();
    }
    
    /**
     * Returns the number of complete questions
     * @return int Should return the number of complete questions
     */
    public function numComplete(){
        $num = 0;
        if(is_array($_SESSION['test'.$this->getTest()])){
            foreach($_SESSION['test'.$this->getTest()] as $value){
                $value = trim($value['status']);
                if($value >= 2){
                    $num++;
                }
            }
        }
        return $num;
    }
    
    /**
     * Returns the number of incomplete questions
     * @return int Should return the number of incomplete questions
     */
    public function numIncomplete(){
        return (count($this->questions) - $this->numComplete());
    }
    
    /**
     * Returns the number of flagged questions
     * @return int Should return the number of flagged questions
     */
    public function numFlagged(){
        $num = 0;
        foreach($_SESSION['test'.$this->getTest()] as $value){
            $value = trim($value['flagged']);
            if($value == 1){
                $num++;
            }
        }
        return $num;
    }
    
    /**
     * Returns the number of correct answers
     * @return int Returns the number of correct answers
     */
    protected function numCorrect(){
        $num = 0;
        foreach($_SESSION['test'.$this->getTest()] as $value){
            $value = trim($value['status']);
            if($value == 4){
                $num++;
            }
        }
        return $num;
    }
    
    /**
     * Checks to see if the particular answer is selected or not
     * @param int $prim Should be the prim number of the question
     * @param string $letter Should be the letter of the answer you are checking to see if it selected
     * @return boolean Returns true if answer selected else return false
     */
    protected function answerSelected($prim, $letter){
        if(strpos($_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['answer'], strtoupper($letter)) !== false){
            return true;
        }
        return false;
    }
    
    /**
     * Checks to see if the answer is selected and if it is correct or not
     * @param int $prim Should be the question prim number
     * @param string $letter Should be the letter of the answer you are checking if it is correct
     * @return string|boolean Returns string if correct and not selected, selected and correct, or selected and incorrect else returns false
     */
    protected function answerSelectedCorrect($prim, $letter){
        $isCorrect = self::$db->select($this->questionsTable, array('prim' => $prim, 'answerletters' => array('LIKE', '%'.strtoupper($letter).'%')), array('answerletters'));
        
        if($this->answerSelected($prim, $letter) && !empty($isCorrect)){return 'CORRECT';}
        elseif($this->answerSelected($prim, $letter) && $isCorrect === false){return 'INCORRECT';}
        elseif(!empty($isCorrect)){return 'NSCORRECT';}
        return false;
    }
    
    /**
     * Checks to see if the current question is flagged or not
     * @param int $prim This should be the prim number of the question you are checking if it is flagged or not
     * @return boolean Returns true if current question is flagged else returns false
     */
    public function questionFlagged($prim){
        if($_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['flagged'] === 1){
            return true;
        }
        return false;
    }
    
    /**
     * This is to add extra content if required (Used on extention classes)
     * @return string
     */
    protected function extraContent(){
        return '';
    }
    
    /**
     * Returns the question data for the given prim number
     * @param int $prim Should be the question prim number
     * @return array|boolean Returns question data as array if data exists else returns false
     */
    protected function getQuestionData($prim){
        return self::$db->select($this->questionsTable, array('prim' => $prim), array('prim', 'question', 'mark', 'option1', 'option2', 'option3', 'option4', 'option5', 'option6', 'answerletters', 'dsaimageid', 'format', 'dsaexplanation', 'casestudyno'));
    }
    
    /**
     * Returns the option HTML for a selected option of a question
     * @param int $question This should be the unique question prim number
     * @param string $option This should be the option text
     * @param string $letter This should be the option letter
     * @param boolean $new If the test is new this should be set to true else set to false
     * @return string Should return the option HTML for the given option
     */
    protected function getOptions($question, $option, $letter, $new = false){
        if(!$new && $this->review != 'answers'){
            if($this->answerSelected($question, $letter)){$selected = ' selected';}
        }elseif(!$new){
            $iscorrect = $this->answerSelectedCorrect($question, $letter);
            if($iscorrect == 'CORRECT'){$selected = ' selectedcorrect';}
            elseif($iscorrect == 'INCORRECT'){$selected = ' selectedincorrect';}
            elseif($iscorrect == 'NSCORRECT'){$selected = ' nscorrect';}
        }
        return '<div class="answer'.$selected.'" id="'.$letter.'"><div class="selectbtn"></div>'.$this->addAudio($question, $letter).$option.'</div>';
    }
    
    /**
     * Returns the option HTML for a selected image option of a question
     * @param int $question This should be the unique question prim number
     * @param string $option This should be the option text
     * @param string $letter This should be the option letter
     * @param boolean $new If the test is new this should be set to true else set to false
     * @return string Should return the option HTML for the given image option
     */
    protected function imageOption($question, $option, $letter, $new = false){
        if(!$new && $this->review != 'answers'){
            if($this->answerSelected($question, strtoupper($letter))){$selected = ' imgselected';}
        }elseif(!$new){
            $iscorrect = $this->answerSelectedCorrect($question, $letter);
            if($iscorrect == 'CORRECT'){$selected = ' imgcorrect';}
            elseif($iscorrect == 'INCORRECT'){$selected = ' imgincorrect';}
            elseif($iscorrect == 'NSCORRECT'){$selected = ' imgnscorrect';}
        }
        return '<div class="answerimage'.$selected.'" id="'.strtoupper($letter).'">'.$option.$this->createImage($question.strtolower($letter).'.png').'</div>';
    }
    
    /**
     * Produces the HTML information for the review page
     */
    public function reviewSection(){
        $this->updateAnswers();
        self::$layout->assign('test_questions', $this->numQuestions(), true);
        self::$layout->assign('complete_questions', $this->numComplete(), true);
        self::$layout->assign('incomplete_questions', $this->numIncomplete(), true);
        self::$layout->assign('flagged_questions', $this->numFlagged(), true);
        self::$layout->assign('review_all', '<div class="reviewall btn btn-theory" id="'.$this->getFirstQuestion().'"><span class="fa fa-refresh fa-fw"></span><span class="hidden-xs"> Review All</span></div>', true);
        self::$layout->assign('review_incomplete', '<div class="reviewincomplete btn btn-theory" id="'.$this->getIncompleteQuestion().'"><span class="fa fa-tasks fa-fw"></span><span class="hidden-xs"> Review Incomplete</span></div>', true);
        self::$layout->assign('review_flagged', '<div class="reviewflagged btn btn-theory" id="'.$this->getFlaggedQuestion().'"><span class="fa fa-flag fa-fw"></span><span class="hidden-xs"> Review Flagged</span></div>', true);
        self::$layout->assign('end_test', '<div class="endtest btn btn-theory"><span class="fa fa-sign-out fa-fw"></span><span class="hidden-xs"> End Test</span></div>', true);
        self::$layout->assign('script', $this->getScript(false), true);
        self::$layout->display('review.tpl');
    }
    
    /**
     * Creates the HTML for an entire Theory Test for use when creating a new test
     * @return string Returns the test HTML code
     */
    public function buildTest(){
        if($this->exists){$this->existingLayout();}
        else{$this->createQuestionHTML($this->getFirstQuestion(), true);}
        self::$layout->assign('test_name', $this->getTestName(), true);
        self::$layout->assign('question_no', '1', true);
        self::$layout->assign('no_questions', $this->numQuestions(), true);
        self::$layout->assign('question_data', $this->questiondata, true);
        return self::$layout->fetch($this->section.'test.tpl');
    }
    
    /**
     * Returns the test report HTML code
     * @param boolean $mark If the test needs to be marked should be set to true else should be false
     * @return string Returns the report HTML code
     */
    public function buildReport($mark = true){
        $this->endTest($this->getTime(), $mark);
        self::$layout->assign('test_name', $this->getTestName(), true);
        self::$layout->assign('question_data', $this->questiondata, true);
        self::$layout->assign('report', true);
        return self::$layout->fetch($this->section.'test.tpl');
    }
    
    /**
     * Creates the HTML for the given question number
     * @param int $prim This should be the prim number for the selected question
     * @param booelean $new If if is a new test should be set to true else should be false
     * @return string|boolean Returns the question HTML and Question number as a JSON encoded string if question exists else returns false
     */
    public function createQuestionHTML($prim, $new = false){
        $this->updateTestProgress($prim, $new);
        $this->checkSettings($new);
        $question = $this->getQuestionData($prim);
        if($question){
            if(is_numeric($question['casestudyno'])){$this->setCaseStudy($question['casestudyno']);}
            if($question['format'] == '0' || $question['format'] == '2'){
                $option1 = $this->getOptions($question['prim'], $question['option1'], 'A', $new);
                $option2 = $this->getOptions($question['prim'], $question['option2'], 'B', $new);
                $option3 = $this->getOptions($question['prim'], $question['option3'], 'C', $new);
                $option4 = $this->getOptions($question['prim'], $question['option4'], 'D', $new);
                if($question['option5']){$option5 = $this->getOptions($question['prim'], $question['option5'], 'E', $new);}
                if($question['option6']){$option6 = $this->getOptions($question['prim'], $question['option6'], 'F', $new);}
            }
            else{
                $option1 = $this->imageOption($question['prim'], $question['option1'], 'A', $new);
                $option2 = $this->imageOption($question['prim'], $question['option2'], 'B', $new);
                $option3 = $this->imageOption($question['prim'], $question['option3'], 'C', $new);
                $option4 = $this->imageOption($question['prim'], $question['option4'], 'D', $new);
            }
            
            self::$layout->assign('mark', $this->getMarkText($question['mark']));
            self::$layout->assign('question', '<div class="questiontext" id="'.$prim.'">'.$this->addAudio($prim, 'Q').$question['question'].'</div>');
            self::$layout->assign('answer_1', $option1);
            self::$layout->assign('answer_2', $option2);
            self::$layout->assign('answer_3', $option3);
            self::$layout->assign('answer_4', $option4);
            self::$layout->assign('answer_5', $option5);
            self::$layout->assign('answer_6', $option6);
            self::$layout->assign('image', ($question['dsaimageid'] ? $this->createImage($question['prim'].'.jpg', true) : ''));
            self::$layout->assign('case_study', $this->casestudy);
            self::$layout->assign('dsa_explanation', $this->dsaExplanation($question['dsaexplanation'], $prim));
            self::$layout->assign('previous_question', $this->prevQuestion());
            self::$layout->assign('flag_question', $this->flagHintButton($question['prim']));
            self::$layout->assign('review', $this->reviewButton());
            self::$layout->assign('next_question', $this->nextQuestion());
            self::$layout->assign('script', $this->getScript());
            self::$layout->assign('alert', $this->alert());
            self::$layout->assign('review_questions', $this->reviewAnswers());
            self::$layout->assign('extra', $this->extraContent());
            self::$layout->assign('audio', $this->audioButton());
            $this->questiondata = self::$layout->fetch('layout'.$question['format'].'.tpl');
            return json_encode(array('html' => utf8_encode($this->questiondata), 'questionnum' => $this->questionNo($prim)));
        }
        else{
            $this->questiondata = '<div id="question-content"><p>There are currently no questions for this learning section.</p><p>Please choose an alternative learning section from the main menu.</p></div>';
            return json_encode(array('html' => $this->questiondata, 'questionnum' => 0));
        }
    }
    
    /**
     * Returns the question information e.g. category, topic number for any given prim
     * @param int $prim this is the question unique number
     * @return array Returns that particular prim info
     */
    public function questionInfo($prim){    
        $info = array();
        $questioninfo = self::$db->select($this->questionsTable, array('prim' => $prim), array('prim', 'dsacat', 'dsaqposition'));
        $catinfo = self::$db->select($this->dsaCategoriesTable, array('section' => $questioninfo['dsacat']));
        $info['prim'] = $questioninfo['prim'];
        $info['cat'] = $questioninfo['dsacat'].'. '.$catinfo['name'];
        if($questioninfo['dsaqposition']){$info['topic'] = $questioninfo['dsaqposition'];}else{$info['topic'] = 'Case Study';}
        return $info;
    }
    
    /**
     * Updates the test progress in the database
     * @param int $prim This should be the current question prim number
     * @param boolean $new If it is a new test should be set to true
     */
    protected function updateTestProgress($prim, $new = false){
        $this->current = $this->questionNo($prim);
        if($this->current < 1){$this->current = 1;} 
        $_SESSION['question_no']['test'.$this->getTest()] = $this->current;
    }

    /**
     * Gets the first Flagged question prim if one exists else returns 'none'
     * @return int|string Should be the first flagged question prim or 'none' if none exist
     */
    protected function getFlaggedQuestion(){
        $q = 1;
        foreach($_SESSION['test'.$this->getTest()] as $value){
            if($value['flagged'] == 1){
                return $this->questionPrim($q);
            }
            $q++;
        }
        return 'none';
    }
    
    /**
     * Gets the first Incomplete question prim if one exists else returns 'none'
     * @return int|string Should be the first incomplete question prim or 'none' if none exist
     */
    protected function getIncompleteQuestion(){
        $q = 1;
        foreach($_SESSION['test'.$this->getTest()] as $value){
            if($value['status'] <= 1){
                return $this->questionPrim($q);
            }
            $q++;
        }
        return 'none';
    }
    
    /**
     * Returns the correct HTML for the DSA explanation in the review section
     * @param string $explanation Should be the DSA explanation for the particular question
     * @param int $prim Should be the prim number of the current question
     * @return string|boolean Returns the HTML string if in the review section else returns false
     */
    public function dsaExplanation($explanation, $prim){
        if($this->review == 'answers'){
            $settings = $this->checkSettings();
            return '<div class="col-md-12"><div class="explanation'.($settings['hint'] === 'on' ? ' visable' : '').'">'.$this->addAudio($prim, 'DSA').'<strong>Official DVSA answer explanation:</strong> '.$explanation.'</div></div>';
        }
        return false;
    }
    
    /**
     * This should set the case study for this group of questions
     * @param int $casestudy This should be the case study number for the set of questions
     */
    protected function setCaseStudy($casestudy){
        $case = self::$db->select($this->caseTable, array('casestudyno' => $casestudy), array('cssituation'));
        $this->casestudy = $this->addAudio($casestudy, 'CS').$case['cssituation'];
    }
    
    /**
     * Clears the test settings in the database
     * @return boolean Returns true if the settings are cleared and updated else returns false
     */
    protected function clearSettings(){
        $settings = $this->checkSettings();
        unset($settings['review']);
        return self::$user->setUserSettings($settings);
    }
    
    /**
     * Sets the current test number
     * @param int $testNo This should be the current test number
     */
    public function setTest($testNo){
        $this->testNo = $testNo;
        if(self::$user->setUserSettings(array('current_test' => $this->testNo))){
            unset($this->questions);
            unset(self::$useranswers);
            $this->getQuestions();
            $this->getUserAnswers();
        }
    }
    
    /**
     * Returns the test number
     * @return int Returns the current test number
     */
    public function getTest(){
        if($this->testNo){
            return $this->testNo;
        }
        else{
            $testNo = self::$user->getUserSettings();
            $this->testNo = $testNo['current_test'];
            return $this->testNo;
        }
    }
    
    /**
     * Sets the current test name
     * @param string $name This should be the name of the test you wish to set it to if left blank will just be Theory Test plus test number
     */
    protected function setTestName($name = ''){
        if(!empty($name)){
            $this->testName = $name;
        }
        else{
            $this->testName = '<span class="hidden-xs">Theory </span>Test '.$this->getTest();
        }
    }
    
    /**
     * Returns the test name
     * @return string Returns the current test name
     */
    public function getTestName(){
        if(empty($this->testName)){
            $this->testName = '<span class="hidden-xs">Theory </span>Test '.$this->getTest();
        }
        return $this->testName;
    }
        
    /**
     * Produces the amount of time the user has spent on the current test
     * @param int $time This should be the amount of seconds remaining for the current test
     * @param string $type This should be either set to 'taken' or 'remaining' depending on which you wish to update 'taken' by default
     * @return void
     */
    public function setTime($time, $type = 'taken'){
        if($time){
            if($type == 'taken'){
                list($mins, $secs) = explode(':', $time);
                $time = gmdate('i:s', ($this->getStartSeconds() - (($mins * 60) + $secs)));
                self::$db->update($this->progressTable, array('time_'.$type => $time), array('user_id' => self::$user->getUserID(), 'test_id' => $this->getTest(), 'type' => $this->getTestType(), 'id' => $this->testID));
            }
            else{
                $_SESSION['time_'.$type]['test'.$this->getTest()] = $time;
            }
        }
    }
    
    /**
     * Gets the Time taken or 'remaining for the current test
     * @param string $type This should be either set to 'taken' or 'remaining' depending on which you wish to get 'taken' by default
     * @return string Returns the time from the database
     */
    public function getTime($type = 'taken'){
        return self::$db->fetchColumn($this->progressTable, array('user_id' => self::$user->getUserID(), 'test_id' => $this->getTest(), 'type' => $this->getTestType()), array('time_'.$type), array('started' => 'DESC'));
    }
    
    /**
     * Gets the number of seconds remaining for the current test
     * @return int Returns the current number of seconds remaining for the test
     */
    protected function getSeconds(){
        $time = $this->getTime('remaining');
        list($minutes, $seconds) = explode(':', $time);
        return intval((intval($minutes) * 60) + intval($seconds));
    }

    /**
     * Returns the previous question button HTML with correct id in code
     * @return string Returns the previous question button HTML code
     */
    protected function prevQuestion(){
        if(($this->review === 'flagged' && $this->numFlagged() > 1) || ($this->review === 'incomplete' && $this->numIncomplete() > 1) || ((int)$this->currentQuestion() != 1 && ($this->review === 'all' || $this->review === false || $this->review === 'answers'))){
            if($this->review == 'flagged' && $this->numFlagged() > 1){$prev = $this->questionPrim($this->getPrevFlagged());}
            elseif($this->review == 'incomplete' && $this->numIncomplete() > 1){$prev = $this->questionPrim($this->getPrevIncomplete());}
            else{$prev = $this->questionPrim(($this->currentQuestion() - 1));}
            return '<div class="prevquestion btn btn-theory" id="'.$prev.'"><span class="fa fa-angle-left fa-fw"></span><span class="hidden-xs"> Previous</span></div>';
        }
        if($this->review === 'all' || $this->review === 'answers' || $this->review === false){
            return '<div class="prevquestion btn btn-theory" id="'.$this->getLastQuestion().'"><span class="fa fa-angle-left fa-fw"></span><span class="hidden-xs"> Previous</span></div>';
        }
        return '<div class="noprev"></div>';
    }
    
    /**
     * Returns the next question button HTML with correct id in code
     * @return string Returns the next question button HTML code
     */
    protected function nextQuestion(){
        if(($this->review === 'flagged' && $this->numFlagged() > 1) || ($this->review === 'incomplete' && $this->numIncomplete() > 1) || ($this->currentQuestion() != $this->numQuestions() && ($this->review === 'all' || $this->review === false || $this->review === 'answers'))){
            if($this->review == 'flagged' && $this->numFlagged() > 1){$next = $this->questionPrim($this->getNextFlagged());}
            elseif($this->review == 'incomplete' && $this->numIncomplete() > 1){$next = $this->questionPrim($this->getNextIncomplete());}
            else{$next = $this->questionPrim(($this->currentQuestion() + 1));}
            return '<div class="nextquestion btn btn-theory" id="'.$next.'"><span class="fa fa-angle-right fa-fw"></span><span class="hidden-xs"> Next</span></div>';
        }
        if($this->review === 'all' || $this->review === 'answers' || $this->review == false){
            return '<div class="nextquestion btn btn-theory" id="'.$this->getFirstQuestion().'"><span class="fa fa-angle-right fa-fw"></span><span class="hidden-xs"> Next</span></div>';
        }
        return '';
    }
    
    /**
     * Returns the questions DSA category number
     * @param int $prim This should be the prim number of the current question
     * @return int Returns the DSA Category number of the current question
     */
    protected function getDSACat($prim){
        return self::$db->fetchColumn($this->questionsTable, array('prim' => $prim), array('dsacat'));
    }
    
    /**
     * Deletes the existing test for the current user if they wish to start again
     * @return boolean If existing tests are deleted will return true else will return false
     */
    public function startNewTest(){
        return self::$db->delete($this->progressTable, array('user_id' => self::$user->getUserID(), 'test_id' => $this->getTest(), 'type' => $this->getTestType(), 'id' => $this->testID));
    }
    
    /**
     * Ends the current test and starts the process to mark if necessary
     * @param int $time The amount of time taken for the current test
     * @param boolean $mark If the test needed to be marked should set to true
     * @return string The end test HTML code will be returned
     */
    public function endTest($time, $mark = true){
        if($mark == true){
            $this->setTime($time);
            $this->markTest();
        }
        else{
            $this->getTestResults();
        }
        self::$layout->assign('test_report', $this->testReport());
        self::$layout->assign('percentages', $this->testPercentages());
        self::$layout->assign('dsa_cat_results', $this->createOverviewResults());
        self::$layout->assign('review_test', '<div class="reviewtest btn btn-theory" id="'.$this->getFirstQuestion().'"><span class="fa fa-question fa-fw"></span><span class="hidden-xs"> Review Test</span></div>');
        self::$layout->assign('print_certificate', $this->printCertif());
        self::$layout->assign('exit_test', '<div class="blank"></div><div class="exittest btn btn-theory"><span class="fa fa-sign-out fa-fw"></span><span class="hidden-xs"> Exit Test</span></div>');
        self::$layout->assign('script', $this->getScript(true));
        $this->questiondata = self::$layout->fetch('results.tpl');
        return $this->questiondata;
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
        
        $this->testresults['correct'] = $this->numCorrect();
        $this->testresults['incorrect'] = ($this->numQuestions() - $this->numCorrect());
        $this->testresults['incomplete'] = $this->numIncomplete();
        $this->testresults['flagged'] = $this->numFlagged();
        $this->testresults['numquestions'] = $this->numQuestions();
        $this->testresults['percent']['correct'] = round(($this->testresults['correct'] / $this->testresults['numquestions']) * 100);
        $this->testresults['percent']['incorrect'] = round(($this->testresults['incorrect'] / $this->testresults['numquestions']) * 100);
        $this->testresults['percent']['flagged'] = round(($this->testresults['flagged'] / $this->testresults['numquestions']) * 100);
        $this->testresults['percent']['incomplete'] = round(($this->testresults['incomplete'] / $this->testresults['numquestions']) * 100);
        $this->updateLearningSection();
        if($this->numCorrect() >= $this->getPassmark()){
            $this->testresults['status'] = 'pass';
            $status = 1;
        }
        else{
            $this->testresults['status'] = 'fail';
            $status = 2;
        }
        self::$db->update($this->progressTable, array('status' => $status, 'results' => serialize($this->testresults), 'complete' => date('Y-m-d H:i:s'), 'totalscore' => $this->numCorrect()), array('user_id' => self::$user->getUserID(), 'test_id' => $this->getTest(), 'type' => $this->getTestType(), 'id' => $this->testID));
    }
    
    /**
     * Updated the learning progress to show what questions have been correctly answer in the test
     * @return boolean Returns true if the learning progress has been updated
     */
    public function updateLearningSection(){
        $info = self::$db->select($this->learningProgressTable, array('user_id' => self::$user->getUserID()), array('progress'));
        $userprogress = unserialize($info['progress']);
        $this->getQuestions();
        foreach($this->questions as $prim){
            $userprogress[$prim]['answer'] = $_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['answer'];
            if($_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['status'] == '4'){$userprogress[$prim]['status'] = 2;}
            elseif($_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['status'] == '3'){$userprogress[$prim]['status'] = 1;}
            else{$userprogress[$prim]['status'] = 0;}
        }
        return self::$db->update($this->learningProgressTable, array('progress' => serialize(array_filter($userprogress))), array('user_id' => self::$user->getUserID()));
    }
    
    /**
     * Returns the print certificate button
     * @return string Returns the print certificate/report button depending on how the user has done on the test
     */
    protected function printCertif(){
        if($this->testresults['status'] === 'pass'){
            return '<a href="/certificate.pdf?type=theory&amp;testID='.$this->getTest().'" title="Print Certificate" target="_blank" class="printcert btn btn-theory"><span class="fa fa-print fa-fw"></span><span class="hidden-xs"> Print Certificate</span></a>';
        }
        return '<a href="/certificate.pdf?type=theory&amp;testID='.$this->getTest().'" title="Print Results" target="_blank" class="printcert btn btn-theory"><span class="fa fa-print fa-fw"></span><span class="hidden-xs"> Print Results</span></a>';
    }
    
    /**
     * Returns the test results for the current test
     * @return boolean|array If the test has been completed the test results will be returned as an array else will return false
     */
    public function getTestResults(){
        $results = self::$db->select($this->progressTable, array('user_id' => self::$user->getUserID(), 'test_id' => $this->getTest(), 'type' => $this->getTestType(), 'status' => array('>', 0)), array('id', 'test_id', 'results', 'started', 'complete', 'time_taken', 'status'), array('started' => 'DESC'));
        if(!empty($results)){
            $this->testresults = unserialize($results['results']);
            $this->testresults['id'] = $results['id'];
            $this->testresults['test_id'] = $results['test_id'];
            $this->testresults['complete'] = $results['complete'];
            return $this->testresults;
        }
        return false;
    }
    
    /**
     * Returns the test status HTML code either pass or failed
     * @return string Returns the test status HTML code either pass or failed
     */
    public function testStatus(){
        if($this->testresults['status'] === 'pass'){return '<strong class="pass">Passed</strong>';}
        else{return '<strong class="fail">Failed</strong>';}
    }
    
    /**
     * Returns the test report table
     * @return string Returns the test report table
     */
    protected function testReport(){
        $report = array();
        $this->getTestResults();
        $report['testname'] = ucwords($this->getTestName());
        $report['user'] = self::$user->getFirstname().' '.self::$user->getLastname();
        $report['status'] = $this->testStatus();
        $report['time'] = $this->getTime();
        $report['passmark'] = $this->getPassmark();
        $report['testdate'] = date('d/m/Y', strtotime($this->testresults['complete']));
        return $report;
    }
    
    /**
     * Returns the test percentages table
     * @return string Returns the test percentages table
     */
    protected function testPercentages(){
        return $this->testresults;
    }
    
    /**
     * Creates an overview of the test results
     * @return string Returns an overview of the test results table
     */
    protected function createOverviewResults(){
        $dsacats = self::$db->selectAll($this->dsaCategoriesTable);
        $catresults = array();
        foreach($dsacats as $i => $dsacat){
            $catresults[$i]['section'] = $dsacat['section'];
            $catresults[$i]['name'] = $dsacat['name'];
            $catresults[$i]['correct'] = (int)$this->testresults['dsa'][$dsacat['section']]['correct'];
            $catresults[$i]['incorrect'] = (int)$this->testresults['dsa'][$dsacat['section']]['incorrect'];
            $catresults[$i]['total'] = ((int)$this->testresults['dsa'][$dsacat['section']]['correct'] + (int)$this->testresults['dsa'][$dsacat['section']]['incorrect'] + (int)$this->testresults['dsa'][$dsacat['section']]['unattempted']);
        }
        return $catresults;
    }
    
    /**
     * Returns the status of each questions and the styles for the review answers section
     * @return string|boolean Returns the HTML code if they are in the reviewing answers section else return false
     */
    protected function reviewAnswers(){
        if($this->review == 'answers'){
            $questions = '<div class="numreviewq">';
            for($r = 1; $r <= $this->numQuestions(); $r++){                
                if($_SESSION['test'.$this->getTest()][$r]['status'] == '4'){$class = ' correct';}
                elseif($_SESSION['test'.$this->getTest()][$r]['status'] == '3'){$class = ' incorrect';}
                else{$class = ' incomplete';}
                
                $questions.= '<div class="questionreview'.$class.($this->currentQuestion() == $r ? ' currentreview' : '').'" id="'.$this->questionPrim($r).'">'.$r.'</div>';
            }
            return $questions.'</div>';
        }
        return false;
    }
}