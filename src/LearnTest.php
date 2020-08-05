<?php
namespace TheoryTest\Car;

use DBAL\Database;
use Configuration\Config;
use Smarty;
use DVSA\HighwayCode;

class LearnTest extends TheoryTest{
    protected $section = 'learn';
    protected $scriptVar = 'learn';
      
    protected $testInfo;
    protected $current;
    protected $currentPrim;
    
    protected $hcCatTable;
    protected $l2dCatTable;
    protected $casestudyCatTable;
    
    protected $categories = ['dvsa' => 'dsacat', 'hc' => 'hcsection', 'l2d' => 'ldclessonno', 'casestudy' => 'casestudyno'];
    protected $sortBy = ['dvsa' => 'dsaqposition', 'hc' => 'hcqposition', 'l2d' => 'ldcqno', 'casestudy' => 'caseqposition'];

    /**
     * Set up all of the components needed to create a Theory Test
     * @param Database $db This should be an instance of Database
     * @param Smarty $layout This needs to be an instance of Smarty Templating
     * @param object $user This should be and instance if the User Class
     * @param false|int $userID If you wish to emulate a user set this value to the users ID else set to false
     * @param string|false $templateDir If you want to change the template location set this location here else set to false
     */
    public function __construct(Database $db, Config $config, Smarty $layout, $user, $userID = false, $templateDir = false, $theme = 'bootstrap') {
        parent::__construct($db, $config, $layout, $user, $userID, $templateDir, $theme);
        $this->getTestInfo();
    }
    
    /**
     * Sets the tables
     */
    public function setTables() {
        parent::setTables();
        $this->hcCatTable = $this->config->table_theory_hc_sections;
        $this->l2dCatTable = $this->config->table_theory_l2d_sections;
        $this->casestudyCatTable = $this->config->table_theory_case_studies;
    }
    
    /**
     * Creates a new test for the 
     * @param int $sectionNo This should be the section number for the test
     * @param string $type This should be the section you wish to create a test for currently 4 sections: dvsa, hc, l2d & casestudy
     * @return string|false Returns a new test if questions exists else will return false
     */
    public function createNewTest($sectionNo = '1', $type = 'dvsa'){
        $this->clearSettings();
        if($type == 'casestudy'){$sectionNo = $this->getRealCaseID($sectionNo);}
        $this->chooseStudyQuestions($sectionNo, $type);
        $this->setTest($type.$sectionNo);
        $table = strtolower($type).'CatTable';
        if(empty($this->$table)){return false;}
        if($type != 'casestudy'){
            $sectionInfo = $this->getSectionInfo($this->$table, $sectionNo);
            $name = $sectionNo.'. '.$sectionInfo['name'];
        }
        else{
            $sectionInfo = $this->getSectionInfo($this->$table, $sectionNo, 'casestudyno');
            $name = 'Case Study '.$sectionNo;
        }
        if((!isset($sectionInfo['free']) || $sectionInfo['free'] == 0) && method_exists($this->user, 'checkUserAccess')){$this->user->checkUserAccess();}
        $this->setTestName($name);
        return $this->buildTest();
    }
    
    /**
     * Gets the questions for the current section test
     * @param int $sectionNo This should be the section number for the test
     * @param string $type This should be the section you wish to create a test for currently 4 sections: dvsa, hc, l2d & casestudy
     */
    protected function chooseStudyQuestions($sectionNo, $type = 'dvsa') {
        $this->testInfo['casestudy'] = 'IS NULL';
        $this->testInfo['category'] = $this->categories[strtolower($type)];
        $this->testInfo['sort'] = $this->sortBy[strtolower($type)];
        if($type == 'casestudy'){
            $this->testInfo['casestudy'] = '1';
        }
        $this->testInfo['section'] = $sectionNo;
        setcookie('testinfo', serialize($this->testInfo), time() + 31536000, '/', '', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? true : false), true);
    }
    
    /**
     * Returns the current test name
     * @return string Returns the current test name
     */
    public function getTestName(){
        return $this->testName;
    }
    
    /**
     * Override current returns false
     * @return boolean returns false
     */
    public function getQuestions(){
        return false;
    }
    
    /**
     * Gets the question number of a given prim for the test
     * @param int $prim This should be the prim number of the question you wish to fin the question number for
     * @return int Returns the question number
     */
    public function questionNo($prim){
        return $this->currentQuestion();
    }

    /**
     * Sets the current test info into memory
     * @return void Nothing is returned
     */
    protected function getTestInfo(){
        if(!isset($this->testInfo) && isset($_COOKIE['testinfo'])){
            $this->testInfo = unserialize($_COOKIE['testinfo']);
        }
    }
    
    /**
     * Get the information from the section table if it exists
     * @param string $table This should be the table reference
     * @param int $sectionID This should be the section number
     * @param string $field If the field you searching on has another name than section this should be entered here
     * @return array|false If any results exist will return an array else returns false
     */
    public function getSectionInfo($table, $sectionID, $field = 'section'){
        return $this->db->select($table, [$field => $sectionID]);
    }
    
    /**
     * Make sure no alert is displayed within the learning section
     * @return boolean Returns false as no alerts should be displayed
     */
    protected function alert(){
        return false;
    }
    
    /**
     * Sets the current user answers into the memory
     * @return void Nothing is returned
     */
    public function getUserAnswers() {
        if(!isset($this->useranswers)){
            $answers = $this->db->fetchColumn($this->learningProgressTable, ['user_id' => $this->getUserID()], ['progress']);
            if($answers !== false){
                if(isset($_SESSION['answers'])){$this->useranswers = $_SESSION['answers'] + unserialize(stripslashes($answers));}
                else{$this->useranswers = unserialize(stripslashes($answers));}
            }
            else{
                $this->db->insert($this->learningProgressTable, ['user_id' => $this->getUserID(), 'progress' => serialize([])]);
            }
        }
    }
    
    /**
     * Returns the number of questions in the current section
     * @return int This should be the number of questions for the section
     */
    public function numQuestions(){
        if($this->testInfo['category']){
            return count($this->db->selectAll($this->questionsTable, [$this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'], ['prim']));
        }
        return 0;
    }
    
    /**
     * Sets the currentPrim value 
     * @param int $prim Should be the current question prim number
     */
    protected function updateTestProgress($prim) {
        $this->currentPrim = $prim;
    }


    /**
     * Returns the current question number
     * @return int Returns the current question number
     */
    protected function currentQuestion(){
        if(!isset($this->current) && $this->testInfo['category']){
            $currentnum = $this->db->select($this->questionsTable, ['prim' => $this->currentPrim, $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'], [$this->testInfo['sort']]);
            $this->current = $currentnum[$this->testInfo['sort']];
        }
        return $this->current;
    }
    
    /**
     * Returns the HTML code for the options
     * @param int $prim The prim number for the question
     * @param string $option The option text
     * @param int $answer_num This should be the option number
     * @param boolean $image If is a image question should be set to true else if it is multiple choice set to false (default)
     * @param boolean $new Added for compatibility with parent class
     * @return array Should return the option array for the given answer
     */
    protected function getOptions($prim, $option, $answer_num, $image = false, $new = false) {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $options = [];
        if($this->answerSelected($prim, $letters[$answer_num]) && $this->questionStatus() !== 'unattempted'){
            $options['selected'] = strtolower($this->questionStatus());
        }
        if($image !== false){
            $options['image'] = $this->createImage($prim.strtolower($letters[$answer_num]).'.png');
        }
        $options['audio'] = $this->addAudio($prim, $letters[$answer_num]);
        $options['id'] = strtolower($letters[$answer_num].$prim);
        $options['prim'] = $prim;
        $options['letter'] = $letters[$answer_num];
        $options['option'] = $option;
        return $options;
    }
    
    /**
     * Returns the Previous question HTML for the current question
     * @return string Returns the previous question HTML with the correct prim number for the previous question
     */
    protected function prevQuestion(){
        if(isset($_COOKIE['skipCorrect'])){$prim = $this->getIncomplete('prev');}
        elseif($this->currentQuestion() != 1 && $this->testInfo['category']){
            $prim = $this->db->fetchColumn($this->questionsTable, [$this->testInfo['sort'] => ['<', $this->currentQuestion()], $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'], ['prim'], 0, [$this->testInfo['sort'] => 'DESC']);
        }
        else{$prim = $this->getLastQuestion();}
        return ['id' => $prim, 'text' => 'Previous', 'icon' => 'angle-left'];
    }
    
    /**
     * Returns the Next question HTML for the current question
     * @return string Returns the next question HTML with the correct prim number for the next question
     */
    protected function nextQuestion(){
        if(isset($_COOKIE['skipCorrect'])){$prim = $this->getIncomplete();}
        elseif(($this->currentQuestion() < $this->numQuestions()) && $this->testInfo['category']){
            $prim = $this->db->fetchColumn($this->questionsTable, [$this->testInfo['sort'] => ['>', $this->currentQuestion()], $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'], ['prim'], 0, [$this->testInfo['sort'] => 'ASC']);
        }
        else{$prim = $this->getFirstQuestion();}
        return ['id' => $prim, 'text' => 'Next', 'icon' => 'angle-right'];
    }
    
    /**
     * Returns the prim number for the next or previous incomplete question
     * @param string $nextOrPrev Should be set to either 'next' or 'prev' depending on which way you wish to get the next question for
     * @return int|string Returns the prim number for the next/previous incomplete question
     */
    protected function getIncomplete($nextOrPrev = 'next'){
        if(strtolower($nextOrPrev) == 'next'){$dir = '>'; $sort = 'ASC'; $start = 0;}
        else{$dir = '<'; $sort = 'DESC'; $start = 100000;}
        
        if($this->testInfo['sort']){
            $searchCurrentQuestion = $this->findNextQuestion($dir, $this->currentQuestion(), $sort);
            if($searchCurrentQuestion !== false){
                return $searchCurrentQuestion;
            }
            $searchStart = $this->findNextQuestion($dir, $start, $sort);
            if($searchStart !== false){
                return $searchStart;
            }
        }
        return 'none';
    }
    
    /**
     * Finds the next question from the given parameters
     * @param string $dir This should be the direction to search for the next question '>' or '<'
     * @param int $start The start number to search for the next question
     * @param string $sort The sort order for the next question ASC or DESC
     * @return int|false Will return the prim number for the next question
     */
    protected function findNextQuestion($dir, $start, $sort){
        foreach($this->db->selectAll($this->questionsTable, [$this->testInfo['sort'] => [$dir, $start], $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'], ['prim'], [$this->testInfo['sort'] => $sort]) as $question){
            if($this->useranswers[$question['prim']]['status'] <= 1){
                return $question['prim'];
            }
        }
        return false;
    }
    
    /**
     * Returns the first questions prim number for the current section
     * @return int Returns the prim number of the first question in the current section
     */
    protected function getFirstQuestion(){
        if($this->testInfo['category']){
            return $this->db->fetchColumn($this->questionsTable, [$this->testInfo['sort'] => '1', $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'], ['prim']);
        }
    }
    
     /**
     * Returns the last question prim number for the current section
     * @return int Returns the prim number of the last question in the current section
     */
    protected function getLastQuestion(){
        if($this->testInfo['category']){
            return $this->db->fetchColumn($this->questionsTable, [$this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'], ['prim'], 0, [$this->testInfo['sort'] => 'DESC']);
        }
    }

    /**
     * Adds the answer to the selected ones and updated the database
     * @param string $answer The letter of the option the user has selected
     * @param int $prim The prim number for the question the user is answering
     * @return string Returns if the answer is is correct, incorrect or incomplete
     */
    public function addAnswer($answer, $prim){
        $arraystring = str_replace($answer, '', trim($_SESSION['answers'][$prim]['answer'])).$answer;
        return $this->replaceAnswer($this->sortAnswers($arraystring), $prim);
    }
    
    /**
     * Replaces a given question answer in the database
     * @param string $answer The new answer letter
     * @param int $prim The prim number for the current question
     * @return string Returns if the answer is is correct, incorrect or incomplete
     */
    public function replaceAnswer($answer, $prim){
        $questiondata = $this->getQuestionData($prim);
        $_SESSION['answers'][$prim]['answer'] = strtoupper($answer);
        if(strlen($_SESSION['answers'][$prim]['answer']) == $questiondata['mark']){
            if($_SESSION['answers'][$prim]['answer'] == $questiondata['answerletters']){$_SESSION['answers'][$prim]['status'] = 2;}
            else{$_SESSION['answers'][$prim]['status'] = 1;}
        }
        else{$_SESSION['answers'][$prim]['status'] = 0;}
        return $this->checkAnswer($prim);
    }
    
    /**
     * Removes an answer from those that are selected
     * @param string $answer The Answer letter that you wish to remove
     * @param int $prim The prim number for the current question
     * @return string Returns if the answer is is correct, incorrect or incomplete
     */
    public function removeAnswer($answer, $prim){
        $_SESSION['answers'][$prim]['answer'] = str_replace(strtoupper($answer), '', $_SESSION['answers'][$prim]['answer']);
        $_SESSION['answers'][$prim]['status'] = 0;
        return $this->checkAnswer($prim);
    }
    
    /**
     * Updates the current answers in the database
     * @return boolean Returns true if updated else returns false
     */
    public function updateLearningProgress(){
        return $this->db->update($this->learningProgressTable, ['progress' => serialize($this->useranswers)], ['user_id' => $this->getUserID()]);
    }
    
    /**
     * Checks to see if the given letter for the question is selected
     * @param int $prim The prim number of the question you are checking
     * @param string $letter The letter of the answer you are checking to see if it is selected
     * @return boolean Returns true if the answer is selected else returns false
     */
    protected function answerSelected($prim, $letter){
        if(isset($this->useranswers[$prim]['answer'])){
            if(strpos($this->useranswers[$prim]['answer'], strtoupper($letter)) !== false){
                return true;
            }
        }
        return false;
    }
    
    /**
     * Checks to see if the user has completed the given question
     * @param int $prim The prim number of the question you are checking
     * @return string Will return the current status of the question as a string
     */
    public function checkAnswer($prim){
        if(!isset($_SESSION['answers'])){$_SESSION['answers'] = $this->useranswers;}
        $this->updateLearningProgress();
        if($_SESSION['answers'][$prim]['status'] == '2'){return json_encode('CORRECT');}
        elseif($_SESSION['answers'][$prim]['status'] == '1'){return json_encode('INCORRECT');}
        else{return json_encode('INCOMPLETE');}
    }
    
    /**
     * Checks on the question status
     * @return string Will return the current status of the question as a string
     */
    protected function questionStatus(){
        if(isset($this->useranswers[$this->currentPrim]['status'])) {
            if($this->useranswers[$this->currentPrim]['status'] == '2'){return 'correct';}
            elseif($this->useranswers[$this->currentPrim]['status'] == '1'){return 'incorrect';}
        }
        return 'unattempted';
    }
    
    /**
     * Returns the review button for the current test
     * @return array Returns the button array information
     */
    protected function reviewButton() {
        $currentstatus = $this->questionStatus();
        if($currentstatus == 'correct'){$style = ' checkcorrect'; $icon = 'check'; $text = 'Correct';}
        elseif($currentstatus == 'incorrect'){$style = ' checkincorrect'; $icon = 'times'; $text = 'Incorrect';}
        else{$style = ''; $icon = 'question'; $text = 'Check Answer';}
        return ['text' => $text, 'class' => 'check'.$style, 'icon' => $icon];
    }

    /**
     * Returns the correct button for the learning test section
     * @param int $prim Added for compatibility with parent class
     * @return string Returns the button HTML
     */
    protected function flagHintButton($prim = false){
        return ['text' => 'Study', 'class' => 'hint'.(isset($this->checkSettings()['hint']) && $this->checkSettings()['hint'] === 'on' ? ' studyon' : ''), 'icon' => 'book'];
    }
    
    /**
     * Returns the script for the learning section
     * @param boolean $review Added for compatibility on parent class
     * @return string Returns the script HTML information
     */
    protected function getScript($review = false){
        if(is_numeric($this->userClone)){return $this->getJavascriptLocation().'preview-'.$this->scriptVar.'.js';}
        return $this->getJavascriptLocation().'learning-'.$this->scriptVar.'.js';
    }
    
    /**
     * Returns any extra HTML code that needs adding to the page
     * @return array Returns any extra HTML code that needs adding to the page
     */
    protected function extraContent(){
        $extra = [];
        if(is_array($this->testInfo['casestudy'])){
            $extra['skipCorrect'] = true;
            $extra['flagged'] = ($_COOKIE['skipCorrect'] == 1 ? ' flagged' : '');
        }
        $extra['signal'] = $this->questionStatus();
        return $extra;
    }
    
    /**
     * Returns any related information about the current question
     * @param string $explanation This should be the DVSA explanation for the database as it has already been retrieved
     * @param int $prim This should be the questions unique prim number
     * @return array Should return any related question information in a tabbed format
     */
    public function dsaExplanation($explanation, $prim){
        $explain = [];
        $explain['visable'] = (isset($this->checkSettings()['hint']) && $this->checkSettings()['hint'] == 'on' ? ' visable' : '');
        $explain['tabs'][1]['label'] = 'Highway Code +';
        $explain['tabs'][1]['text'] = $this->highwayCodePlus($prim);
        $explain['tabs'][2]['label'] = 'DVSA Advice';
        $explain['tabs'][2]['text'] = $explanation;
        $explain['tabs'][2]['audio'] = $this->addAudio($prim, 'DSA');
        //$explain['tabs'][3]['label'] = 'Instructor Comment';
        //$explain['tabs'][3]['text'] = $this->instructorComments($prim);
        return $explain;
    }
    
    /**
     * Returns any related highway code rules for the current question
     * @param int $prim This should be the questions unique prim number
     * @return string Returns any highway rules associated with the current question
     */
    protected function highwayCodePlus($prim){
        $highwaycode = '';
        $hcClass = new HighwayCode($this->db, $this->config);
        $rules = $hcClass->getRule($this->db->select($this->questionsTable, ['prim' => $prim], ['hcrule1', 'hcrule2', 'hcrule3']));
        if(is_array($rules)){
            foreach($rules as $ruleno){
                if(!$ruleno['hcrule']){
                    $rule = '<p class="center">'.$this->hcImage($ruleno['imagetitle1'], $ruleno['hctitle']).$this->hcImage($ruleno['imagetitle2'], $ruleno['hctitle']).'</p><p class="center">'.$ruleno['hctitle'].'</p>';
                }
                else{
                    $rule = $ruleno['hcrule'].$this->hcImage($ruleno['imagetitle1'], $ruleno['hctitle']);
                }
                $highwaycode.= ($this->audioEnabled ? '<div class="sound" data-audio-id="hc'.$ruleno['hcno'].'"></div>' : '').'<span id="audiohc'.$ruleno['hcno'].'">'.$rule.'</span>';
            }
        }
        return $highwaycode;
    }
    
    /**
     * Returns the formatted image HTML code
     * @param string $imagesrc This should be the image name
     * @param string $alttext This needs to be any alt text you want to give to the image
     * @return string|boolean If the image exists will return the image HTML else will return false
     */
    public function hcImage($imagesrc, $alttext){
        $hcClass = new HighwayCode($this->db, $this->config, $_SERVER["DOCUMENT_ROOT"]);
        $image = $hcClass->buildImage($imagesrc);
        if(!empty($image)){
            return '<img src="'.$image['image'].'" alt="'.$alttext.'" width="'.$image['width'].'" height="'.$image['height'].'" class="img-responsive center-block" />';
        }
        return false;
    }
    
    /**
     * Returns the instructors comments for the given question
     * @param int $prim This should be the questions unique prim number
     * @return string|false Returns the questions explanation if it exists else return false
     */
    protected function instructorComments($prim){
        return $this->db->fetchColumn($this->questionsTable, ['prim' => $prim], ['explanation']);
    }
    
    /**
     * The case ID's give may not match so make sure to get the correct one
     * @param int $sectionNo This should be the section number for the test
     * @return int|false Returns the real case study ID number if it exists or returns false
     */
    private function getRealCaseID($sectionNo){
        if($this->getTestType() == 'CAR'){$type = 'car';}else{$type = 'M/C';}
        return $this->db->fetchColumn($this->caseTable, ['type' => $type, 'lp' => 1, 'dsacat' => $sectionNo], ['casestudyno']);
    }
    
    /**
     * Returns the status of each questions and the styles for the review answers section
     * @return string|false Returns false for learning section
     */
    protected function reviewAnswers() {
        return false;
    }
}