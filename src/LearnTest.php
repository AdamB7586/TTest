<?php
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
namespace TheoryTest;

use DBAL\Database;

class LearnTest extends TheoryTest{
    protected $section = 'learn';
    public $review = 'all';
      
    protected $testInfo;
    protected $current;
    protected $currentPrim;
    
    public $progressTable = 'users_progress';
    
    /**
     * Connects to the database sets the current user and gets any user answers
     * @param Database $db
     * @param Smarty $layout
     * @param User $user
     */
    public function __construct(Database $db, Smarty $layout, User $user) {
        parent::__construct($db, $layout, $user);
        $this->getTestInfo();
    }
    
    /**
     * Creates a new test for the 
     * @param int $sectionNo This should be the section number for the test
     * @param string $type This should be the section you wish to create a test for currently 4 sections: dsa, hc, l2d & casestudy
     */
    public function createNewTest($sectionNo = '1', $type = 'dsa'){
        $this->clearSettings();
        if($type == 'casestudy'){$sectionNo = $this->getRealCaseID($sectionNo);}
        $this->chooseQuestions($sectionNo, $type);
        $this->setTest($type.$sectionNo);
        if($type != 'casestudy'){
            $learnName = $this->db->select('theory_'.strtolower($type).'_sections', array('section' => $sectionNo), array('name', 'free'));
            $name = $sectionNo.'. '.$learnName['name'];
            if($learnName['free'] == 0){$this->user->checkUserAccess();}
        }
        else{$name = 'Case Study '.$sectionNo;}
        $this->setTestName($name);
        return $this->buildTest();
    }
    
    /**
     * Gets the questions for the current section test
     * @param int $sectionNo This should be the section number for the test
     * @param string $type This should be the section you wish to create a test for currently 4 sections: dsa, hc, l2d & casestudy
     */
    protected function chooseQuestions($sectionNo, $type) {
        $this->testInfo['casestudy'] = array('IS', 'NULL');
        if($type == 'dsa'){
            $this->testInfo['category'] = 'dsacat';
            $this->testInfo['sort'] = 'dsaqposition';
        }
        elseif($type == 'hc'){
            $this->testInfo['category'] = 'hcsection';
            $this->testInfo['sort'] = 'hcqposition';
        }
        elseif($type == 'l2d'){
            $this->testInfo['category'] = 'ldclessonno';
            $this->testInfo['sort'] = 'ldcqno';
        }
        elseif($type == 'casestudy'){
            $this->testInfo['category'] = 'casestudyno';
            $this->testInfo['sort'] = 'csqposition';
            $this->testInfo['casestudy'] = '1';
        }
        $this->testInfo['section'] = $sectionNo;
        setcookie('testinfo', serialize($this->testInfo), time() + 31536000, '/');
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
        if(!isset($this->testInfo)){
            $this->testInfo = unserialize($_COOKIE['testinfo']);
        }
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
            $answers = $this->db->select($this->progressTable, array('user_id' => $this->user->getUserID()), array('progress'));
            if(!empty($answers)){
                if($_SESSION['answers']){$this->useranswers = $_SESSION['answers'] + unserialize(stripslashes($answers['progress']));}
                else{$this->useranswers = unserialize(stripslashes($answers['progress']));}
            }
            else{
                $this->db->insert($this->progressTable, array('user_id' => $this->user->getUserID(), 'progress' => serialize(array())));
            }
        }
    }
    
    /**
     * Returns the number of questions in the current section
     * @return int This should be the number of questions for the section
     */
    public function numQuestions(){
        if($this->testInfo['category']){
            return count($this->db->selectAll($this->questionsTable, array($this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'), array('prim')));
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
            $currentnum = $this->db->select($this->questionsTable, array('prim' => $this->currentPrim, $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'), array($this->testInfo['sort']));
            $this->current = $currentnum[$this->testInfo['sort']];
        }
        return $this->current;
    }
    
    /**
     * Returns the HTML code for the options
     * @param int $prim The prim number for the question
     * @param string $option The option text
     * @param string $letter The letter of the current option
     * @return string Returns the HTML code for a given question option
     */
    protected function getOptions($prim, $option, $letter) {
        if($this->answerSelected($prim, $letter)){
            $selected = ' selected';
            if($this->questionStatus() != 'unattempted'){$selected.= ' selected'.$this->questionStatus();}
        }
        return '<div class="answer'.$selected.'" id="'.$letter.'"><div class="selectbtn"></div>'.$this->addAudio($prim, $letter).$option.'</div>';
    }
    
    /**
     * Returns the HTML code for the options if its an image type of question
     * @param int $prim The prim number for the question
     * @param string $option The option text
     * @param string $letter The letter of the current option
     * @return string Returns the HTML code for a given question option
     */
    protected function imageOption($prim, $option, $letter) {
        if($this->answerSelected($prim, $letter)){
            $selected = ' imgselected';
            if($this->questionStatus() != 'unattempted'){$selected.= ' selected'.$this->questionStatus();}
        }
        return '<div class="answerimage'.$selected.'" id="'.strtoupper($letter).'">'.$option.$this->createImage($prim.strtolower($letter).'.png').'</div>';
    }
    
    /**
     * Returns the Previous question HTML for the current question
     * @return string Returns the previous question HTML with the correct prim number for the previous question
     */
    protected function prevQuestion(){
        if($_COOKIE['skipCorrect'] == 1){return '<div class="prevquestion btn btn-theory" id="'.$this->getIncomplete('prev').'"><span class="fa fa-angle-left fa-fw"></span><span class="hidden-xs"> Previous</span></div>';}
        elseif($this->currentQuestion() != 1 && $this->testInfo['category']){
            $question = $this->db->select($this->questionsTable, array($this->testInfo['sort'] => array('<', $this->currentQuestion()), $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'), array('prim'), array($this->testInfo['sort'] => 'DESC'));
            return '<div class="prevquestion btn btn-theory" id="'.$question['prim'].'"><span class="fa fa-angle-left fa-fw"></span><span class="hidden-xs"> Previous</span></div>';
        }
        else{return '<div class="prevquestion btn btn-theory" id="'.$this->getLastQuestion().'"><span class="fa fa-angle-left fa-fw"></span><span class="hidden-xs"> Previous</span></div>';}
    }
    
    /**
     * Returns the Next question HTML for the current question
     * @return string Returns the next question HTML with the correct prim number for the next question
     */
    protected function nextQuestion(){
        if($_COOKIE['skipCorrect'] == 1){return '<div class="nextquestion btn btn-theory" id="'.$this->getIncomplete().'"><span class="fa fa-angle-right fa-fw"></span><span class="hidden-xs"> Next</span></div>';}
        elseif(($this->currentQuestion() < $this->numQuestions()) && $this->testInfo['category']){
            $question = $this->db->select($this->questionsTable, array($this->testInfo['sort'] => array('>', $this->currentQuestion()), $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'), array('prim'), array($this->testInfo['sort'] => 'ASC'));
            return '<div class="nextquestion btn btn-theory" id="'.$question['prim'].'"><span class="fa fa-angle-right fa-fw"></span><span class="hidden-xs"> Next</span></div>';
        }
        else{return '<div class="nextquestion btn btn-theory" id="'.$this->getFirstQuestion().'"><span class="fa fa-angle-right fa-fw"></span><span class="hidden-xs"> Next</span></div>';}
    }
    
    /**
     * Returns the prim number for the next or previous incomplete question
     * @param string $nextOrPrev Should be set to either 'next' or 'prev' depending on which way you wish to get the next question for
     * @return int|string Returns the prim number for the next/prev incomplete question
     */
    protected function getIncomplete($nextOrPrev = 'next'){
        if(strtolower($nextOrPrev) == 'next'){$dir = '>'; $sort = 'ASC'; $start = '0';}
        else{$dir = '<'; $sort = 'DESC'; $start = '100000';}
        
        if($this->testInfo['sort']){
            foreach($this->db->selectAll($this->questionsTable, array($this->testInfo['sort'] => array($dir, $this->currentQuestion()), $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'), array('prim'), array($this->testInfo['sort'] => $sort)) as $question){
                if($this->useranswers[$question['prim']]['status'] <= 1){
                    return $question['prim'];
                }
            }
            foreach($this->db->selectAll($this->questionsTable, array($this->testInfo['sort'] => array($dir, $start), $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'), array('prim'), array($this->testInfo['sort'] => $sort)) as $question){
                if($this->useranswers[$question['prim']]['status'] <= 1){
                    return $question['prim'];
                }
            }
        }
        return 'none';
    }
    
    /**
     * Returns the first questions prim number for the current section
     * @return int Returns the prim number of the first question in the current section
     */
    protected function getFirstQuestion(){
        if($this->testInfo['category']){
            return $this->db->fetchColumn($this->questionsTable, array($this->testInfo['sort'] => '1', $this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'), array('prim'));
        }
    }
    
     /**
     * Returns the last question prim number for the current section
     * @return int Returns the prim number of the last question in the current section
     */
    protected function getLastQuestion(){
        if($this->testInfo['category']){
            return $this->db->fetchColumn($this->questionsTable, array($this->testInfo['category'] => $this->testInfo['section'], 'alertcasestudy' => $this->testInfo['casestudy'], strtolower($this->getTestType()).'question' => 'Y'), array('prim'), 0, array($this->testInfo['sort'] => 'DESC'));
        }
    }

    /**
     * Adds the answer to the selected ones and updated the database
     * @param string $answer The letter of the option the user has selected
     * @param int $prim The prim number for the question the user is answering
     * @return boolean Returns true
     */
    public function addAnswer($answer, $prim){
        $questiondata = $this->getQuestionData($prim);
        
        $arraystring = str_replace($answer, '', trim($_SESSION['answers'][$prim]['answer'])).$answer;
        if(strlen($arraystring) > 1){
            $stringParts = str_split($arraystring);
            sort($stringParts);
            $arraystring = implode('', $stringParts);
        }
        $_SESSION['answers'][$prim]['answer'] = strtoupper($arraystring);
        if(strlen($_SESSION['answers'][$prim]['answer']) == $questiondata['mark']){
            if($_SESSION['answers'][$prim]['answer'] == $questiondata['answerletters']){$_SESSION['answers'][$prim]['status'] = 2;}
            else{$_SESSION['answers'][$prim]['status'] = 1;}
        }
        else{$_SESSION['answers'][$prim]['status'] = 0;}
        return true;
    }
    
    /**
     * Replaces a given question answer in the database
     * @param string $answer The new answer letter
     * @param int $prim The prim number for the current question
     * @return boolean Returns true
     */
    public function replaceAnswer($answer, $prim){
        $_SESSION['answers'][$prim]['answer'] = strtoupper($answer);
        $questiondata = $this->getQuestionData($prim);
        if($_SESSION['answers'][$prim]['answer'] == $questiondata['answerletters']){$_SESSION['answers'][$prim]['status'] = 2;}
        else{$_SESSION['answers'][$prim]['status'] = 1;}
        return true;
    }
    
    /**
     * Removes an answer from those that are selected
     * @param string $answer The Answer letter that you wish to remove
     * @param int $prim The prim number for the current question
     * @return boolean Returns true
     */
    public function removeAnswer($answer, $prim){
        $_SESSION['answers'][$prim]['answer'] = str_replace(strtoupper($answer), '', $_SESSION['answers'][$prim]['answer']);
        $_SESSION['answers'][$prim]['status'] = 0;
        return true;
    }
    
    /**
     * Updates the current answers in the database
     * @return boolean Returns true if updated else returns false
     */
    public function updateLearningProgress(){
        unset($_SESSION['answers']);
        return $this->db->update($this->progressTable, array('progress' => serialize($this->useranswers)), array('user_id' => $this->user->getUserID()));
    }
    
    /**
     * Checks to see if the given letter for the question is selected
     * @param int $prim The prim number of the question you are checking
     * @param string $letter The letter of the answer you are checking to see if it is selected
     * @return boolean Returns true if the answer is selected else returns false
     */
    protected function answerSelected($prim, $letter){
        if(strpos($this->useranswers[$prim]['answer'], strtoupper($letter)) !== false){
            return true;
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
        if($_SESSION['answers'][$prim]['status'] == '2'){
            echo('CORRECT');
            $this->updateLearningProgress();
        }
        elseif($_SESSION['answers'][$prim]['status'] == '1'){echo('INCORRECT');}
        else{echo('INCOMPLETE');}
    }
    
    /**
     * Checks on the question status
     * @return string Will return the current status of the question as a string
     */
    protected function questionStatus(){
        if($this->useranswers[$this->currentPrim]['status'] == '2'){return 'correct';}
        elseif($this->useranswers[$this->currentPrim]['status'] == '1'){return 'incorrect';}
        else{return 'unattempted';}
    }
    
    /**
     * Returns the review button for the current test
     * @return string Returns the review button for the current test
     */
    protected function reviewButton() {
        $currentstatus = $this->questionStatus();
        if($currentstatus == 'correct'){$style = ' checkcorrect'; $text = '<span class="fa fa-check fa-fw"></span><span class="hidden-xs"> Correct</span>';}
        elseif($currentstatus == 'incorrect'){$style = ' checkincorrect'; $text = '<span class="fa fa-times fa-fw"></span><span class="hidden-xs"> Incorrect</span>';}
        else{$style = ''; $text = '<span class="fa fa-question fa-fw"></span><span class="hidden-xs"> Check Answer</span>';}
        return '<div class="check btn btn-theory'.$style.'">'.$text.'</div>';
    }

    /**
     * Returns the correct button for the learning test section
     * @return string Returns the button HTML
     */
    protected function flagHintButton(){
        $settings = $this->checkSettings();
        $class = ($settings['hint'] === 'on' ? ' studyon' : '');
        return '<div class="hint btn btn-theory'.$class.'"><span class="fa fa-book fa-fw"></span><span class="hidden-xs"> Study</span></div>';
    }
    
    /**
     * Returns the script for the learning section
     * @return string Returns the script HTML information
     */
    protected function getScript(){
        return '<script async type="text/javascript" src="/js/theory/learning-learn.js"></script>';
    }
    
    /**
     * Returns any extra HTML code that needs adding to the page
     * @return string Returns any extra HTML code that needs adding to the page
     */
    protected function extraContent(){
        if(is_array($this->testInfo['casestudy'])){
            $skipcorrect = ($_COOKIE['skipCorrect'] === 1 ? ' flagged' : '');
            $extra.= '</div></div><div class="row"><div><div class="col-xs-12 skipcorrectclear"><div class="skipcorrect btn btn-theory'.$skipcorrect.'">Skip Correct</div></div>';
        }
        $extra.='<div class="signal signal'.$this->questionStatus().'"></div>';
        return $extra;
    }
    
    /**
     * Returns any related information about the current question
     * @param string $explanation This should be the DSA explanation for the database as it has already been retrieved
     * @param int $prim This should be the questions unique prim number
     * @return string Should return any related question information in a tabbed format
     */
    public function dsaExplanation($explanation, $prim){
        $settings = $this->checkSettings();
        $class = ($settings['hint'] === 'on' ? ' visible' : '');
        return '<div class="col-xs-12 showhint'.$class.'">
<ul class="nav nav-tabs">
<li class="active"><a href="#tab-1" aria-controls="profile" role="tab" data-toggle="tab">Highway Code +</a></li>
<li><a href="#tab-2" aria-controls="profile" role="tab" data-toggle="tab">DVSA Advice</a></li>'.
/*<li><a href="#tab-3">Instructor Comment</a></li>*/'
</ul>
<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="tab-1">'.$this->highwayCodePlus($prim).'</div>
<div role="tabpanel" class="tab-pane" id="tab-2">'.$this->addAudio($prim, 'DSA').$explanation.'</div>'.
/*<div role="tabpanel" class="tab-pane" id="tab-3">'.$this->instructorComments($prim).'</div>*/'
</div>
</div>';
    }
    
    /**
     * Returns any related highway code rules for the current question
     * @param int $prim This should be the questions unique prim number
     * @return string Returns any highway rules associated with the current question
     */
    protected function highwayCodePlus($prim){
        $hcRules = $this->db->select($this->questionsTable, array('prim' => $prim), array('hcrule1', 'hcrule2', 'hcrule3'));
        $highwaycode = '';
        foreach($hcRules as $ruleno){
            if(is_numeric($ruleno)){
                $ruleinfo = $this->db->select('highway_code', array('hcno' => $ruleno), array('hcrule', 'hctitle', 'imagetitle1', 'imagetitle2'));
                if(!$ruleinfo['hcrule']){
                    list($width, $height) = getimagesize(ROOT.DS.'images/highway-code/'.$ruleinfo['imagetitle1']);
                    $rule = '<p class="center"><img src="/images/highway-code/'.$ruleinfo['imagetitle1'].'" alt="'.$ruleinfo['hctitle'].'" width="'.$width.'" height="'.$height.'" /> ';
                    if($ruleinfo['imagetitle2']){
                        list($width, $height) = getimagesize(ROOT.DS.'images/highway-code/'.$ruleinfo['imagetitle2']);
                        $rule.= '<img src="/images/highway-code/'.$ruleinfo['imagetitle2'].'" alt="'.$ruleinfo['hctitle'].'" width="'.$width.'" height="'.$height.'" />';
                    }
                    $rule.= '</p><p class="center">'.$ruleinfo['hctitle'].'</p>';
                }
                else{
                    $rule = $ruleinfo['hcrule'];
                    if($ruleinfo['imagetitle1']){
                        list($width, $height) = getimagesize(ROOT.DS.'images/highway-code/'.$ruleinfo['imagetitle1']);
                        $rule.= '<p class="center"><img src="/images/highway-code/'.$ruleinfo['imagetitle1'].'" alt="'.$ruleinfo['hctitle'].'" width="'.$width.'" height="'.$height.'" /></p>';
                    }
                }
                $highwaycode.= $this->addAudio($ruleno, 'HC', '/highway-code').$rule;
            }
        }
        return $highwaycode;
    }
    
    /**
     * Returns the instructors comments for the given question
     * @param int $prim This should be the questions unique prim number
     * @return string Returns the questions explanation
     */
    protected function instructorComments($prim){
        $comments = $this->db->select($this->questionsTable, array('prim' => $prim), array('explanation'));
        return $comments['explanation'];
    }
    
    /**
     * The case ID's give may not match so make sure to get the correct one
     * @param int $sectionNo This should be the section number for the test
     * @return int|false Returns the real case study ID number if it exists or returns false
     */
    private function getRealCaseID($sectionNo){
        if($this->getTestType() == 'CAR'){$type = 'car';}else{$type = 'M/C';}
        $caseInfo = $this->db->select('theory_case_studies', array('type' => $type, 'lp' => 1, 'dsacat' => $sectionNo), array('casestudyno'));
        if($caseInfo){
            return $caseInfo['casestudyno'];
        }
        return false;
    }
}