<?php
namespace TheoryTest\Car;

use TheoryTest\Car\Essential\TTInterface;
use Configuration\Config;
use Smarty;
use DBAL\Database;

/**
 * Produces a Theory Test
 * @package Theory Test
 * @author Adam Binnersley <adam.binnersley@learnerdriving.com>
 */
class TheoryTest implements TTInterface
{
    
    /**
     * @var object Should be an instance of the Database object
     */
    protected $db;
    
    /**
     * @var object Should be an instance of configuration class
     */
    protected $config;
    
    /**
     * @var object Should be an instance of the Smarty Template object
     */
    protected $layout;
    
    /**
     * @var object Should be an instance of the User object
     */
    protected $user;
    
    /**
     * @var int|false If you want to emulate a user set this to their user ID
     */
    protected $userClone = false;
    
    /**
     * @var int This is the unique number given to each test attempted
     */
    protected $testID;
    
    /**
     * @var boolean|string If any tests already exists for the current tests should be set to test status
     */
    protected $exists = false;
    
    /**
     * @var int This is the number of questions needed to be correct to pass the test
     */
    public $passmark = 43;
    
    /*
     * @var in This is the maximum number of answers each question has within the test
     */
    public $noAnswers = 4;
    
    /**
     * @var string The name of the tests question order database table
     */
    public $testsTable;
    
    /**
     * @var string The name of the user tests database table
     */
    public $questionsTable;
    
    /**
     * @var string The name of the user tests database table
     */
    public $learningProgressTable;
    
    /**
     * @var string The name of the user tests database table
     */
    public $progressTable;
    
    /**
     * @var string The name of the case studies database table
     */
    public $caseTable;
    
    /**
     * @var string The name of the DVSA sections database table
     */
    public $dvsaCatTable;
    
    /**
     * @var string The location where the JavaScript files can be found relative to where the Theory Test is located
     */
    protected $javascriptLocation = '/js/theory/';
    
    /**
     * @var string The location where the video files can be found relative to where the Theory Test is located
     */
    public $videoLocation = '/videos/case/';
    
    /**
     * @var string The variant of the JavaScript file being looked at for the current test
     */
    protected $scriptVar = 'questions';
    
    /**
     * @var string The server root path before the image directory
     */
    protected $imageRootPath;

    /**
     * @var string The location relative to the theory test where any images can be found
     */
    protected $imagePath;
    
    /**
     * @var int The number of seconds that are allowed to complete a new test
     */
    protected $seconds = 3420;
    
    /**
     * @var string This should either be set to 'theory' or 'learn' depending on what section is being accessed
     */
    protected $section = 'theory';
    
    /**
     * @var boolean If audio should be shown set to true else set to false
     */
    public $audioEnabled = false;
    
    /**
     * @var array This is an array of all of the test questions and their prim numbers
     */
    public $questions;
    
    /**
     * @var array This should be the users progress fro the current test
     */
    protected $userProgress = false;
    
    /**
     * @var array This should be an array of any answers given by the user
     */
    public $useranswers;
    
    /**
     * @var int The number of the current set test
     */
    protected $testNo;
    
    /**
     * @var string This is the name to be displayed at the top of the test
     */
    protected $testName;
    
    /**
     * @var array This should be the test information the user has given
     */
    protected $testData;
    
    /**
     * @var string The current question information
     */
    protected $questiondata;
    
    /**
     * @var int This is the current question number
     */
    protected $current;
    
    /**
     * @var array If the question is a case study type, this should be an array of the case study information
     */
    protected $casestudy;
    
    /**
     * @var false|string If review not active should be false else set to type i.e. all, flagged, incomplete or answers
     */
    protected $review = false;
    
    /**
     * @var array This should be the marking information and how the user has done once the test has been marked
     */
    public $testresults;
    
    /**
     * @var boolean If you want all old tests deleted when resitting a new test set to true else to save all completed tests set to false
     */
    protected $deleteOldTests = true;
    
    /**
     * Connects to the database sets the current user and gets any user answers
     * @param Database $db This needs to be an instance of the database class
     * @param Config $config This needs to be an instance of the Configuration class
     * @param Smarty $layout This needs to be an instance of the Smarty Template class
     * @param object $user This should be the user class used
     * @param int|false $userID If you want to emulate a user set the user ID here
     * @param string|false $templateDir If you want to change the template location set this location here
     * @param string $theme This is the template folder to look at currently either 'bootstrap' or 'bootstrap4'
     */
    public function __construct(Database $db, Config $config, Smarty $layout, $user, $userID = false, $templateDir = false, $theme = 'bootstrap')
    {
        $this->db = $db;
        $this->config = $config;
        $this->user = $user;
        $this->layout = $layout;
        $this->layout->addTemplateDir(($templateDir === false ? str_replace(basename(__DIR__), '', dirname(__FILE__)).'templates'.DIRECTORY_SEPARATOR.$theme : $templateDir), 'theory');
        if (is_numeric($userID)) {
            $this->userClone = $userID;
        }
        if (!session_id()) {
            if (defined(SESSION_NAME)) {
                session_name(SESSION_NAME);
            }
            session_set_cookie_params(0, '/', '.'.(defined('DOMAIN') ? DOMAIN : str_replace(['http://', 'https://', 'www.'], '', $_SERVER['SERVER_NAME'])), (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? true : false), (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? true : false));
            session_start();
        }
        $this->setTables();
        $this->getUserAnswers();
        $this->setImageRootPath($_SERVER['DOCUMENT_ROOT'])->setImagePath();
    }
    
    /**
     * Sets the tables
     */
    protected function setTables()
    {
        $this->testsTable = $this->config->table_theory_tests;
        $this->questionsTable = $this->config->table_theory_questions;
        $this->learningProgressTable = $this->config->table_users_progress;
        $this->progressTable = $this->config->table_users_test_progress;
        $this->caseTable = $this->config->table_theory_case_studies;
        $this->dvsaCatTable = $this->config->table_theory_dvsa_sections;
    }
    
    /**
     * Returns the userID or the mock userID if you wish to look at users progress
     * @return int Returns the UserID or mocked up userID if valid
     */
    public function getUserID()
    {
        if (is_numeric($this->userClone)) {
            return $this->userClone;
        }
        return $this->user->getUserID();
    }
    
    /**
     * Create a new Theory Test for the test number given
     * @param int $theorytest Should be the test number
     * @return string|false Returns the HTML for a test if valid else returns false
     */
    public function createNewTest($theorytest = 1)
    {
        $this->clearSettings();
        $this->setTest($theorytest);
        if (method_exists($this->user, 'checkUserAccess')) {
            $this->user->checkUserAccess($theorytest);
        }
        $this->setTestName();
        if ($this->anyExisting() === false) {
            $this->chooseQuestions($theorytest);
        }
        return $this->buildTest();
    }
    
    /**
     * Sets the pass mark for the test the default is set to 43 which is what is set by the DVSA
     * @param int $mark This should be the pass mark for the test (no greater than 50 as only 50 questions are retrieved)
     * @return $this
     */
    public function setPassmark($mark)
    {
        if (is_numeric($mark) && $mark >= 1) {
            $this->passmark = intval($mark);
        }
        return $this;
    }
    
    /**
     * Returns the current pass mark for the test
     * @return int Returns the set pass mark for the current test
     */
    public function getPassmark()
    {
        return intval($this->passmark);
    }
    
    /**
     * Sets the amount of seconds that should be allowed to undertake a test
     * @param int $seconds If you wish to change the seconds allowed from the 57 minutes (3420 seconds) set the number in seconds
     * @return $this
     */
    public function setSeconds($seconds)
    {
        if (is_int($seconds)) {
            $this->seconds = intval($seconds);
        }
        return $this;
    }
    
    /**
     * Gets the amount of seconds that are allowed for the current test
     * @return int This should be the number of sends allowed to partake the test
     */
    public function getStartSeconds()
    {
        return $this->seconds;
    }
    
    /**
     * Sets the location where the JavaScript files can be found
     * @param string $location The should either be a URL or a relative position
     * @return $this
     */
    public function setJavascriptLocation($location)
    {
        if (is_string($location)) {
            $this->javascriptLocation = $location;
        }
        return $this;
    }
    
    /**
     * Returns the currents set location of the JavaScript files
     * @return string This should be the folder where all the JavaScript files can be found
     */
    public function getJavascriptLocation()
    {
        return $this->javascriptLocation;
    }
    
    /**
     * The location where the videos are located (can be absolute or root path)
     * @param string $location The path to the video clips
     * @return $this
     */
    public function setVidLocation($location)
    {
        $this->videoLocation = $location;
        return $this;
    }
    
    /**
     * Returns the video path
     * @return string This is the path to where the videos are located (minus the mp4 and ogv)
     */
    public function getVidLocation()
    {
        return $this->videoLocation;
    }
    
    /**
     * Sets the path where images can be found
     * @param string $path This should be the path that you want to set where the test images can be found
     * @return $this
     */
    public function setImagePath($path = '')
    {
        if (strlen($path) >= 3) {
            $this->imagePath = $path;
        } else {
            $this->imagePath = DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'prim'.DIRECTORY_SEPARATOR;
        }
        return $this;
    }
    
    /**
     * Returns the path where the test images can be located
     * @return string This will be the image location path
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }
    
    /**
     * Sets the root path to the images directory
     * @param string $path The image root path
     * @return $this
     */
    public function setImageRootPath($path)
    {
        if (is_string($path)) {
            $this->imageRootPath = $path;
        }
        return $this;
    }
    
    /**
     * The image root path
     * @return string
     */
    public function getImageRootPath()
    {
        return $this->imageRootPath;
    }
    
    /**
     * Return the test data from the session for the current user
     * @return array This should be any test data that exists in the current session
     */
    protected function getUserTestInfo()
    {
        if (!is_array($this->testData) && isset($_SESSION['test'.$this->getTest()])) {
            $this->testData = $_SESSION['test'.$this->getTest()];
        }
        return $this->testData;
    }
    
    /**
     * Creates the test report HTML if the test has been completed
     * @param int $theorytest The test number you wish to view the report for
     * @return string Returns the HTML for the test report for the given test ID
     */
    public function createTestReport($theorytest = 1)
    {
        $this->setTest($theorytest);
        if ($this->getTestResults()) {
            $this->setTestName($this->testName);
            return $this->buildReport(false);
        }
        return $this->layout->fetch('report'.DIRECTORY_SEPARATOR.'report-unavail.tpl');
    }

    /**
     * Choose the questions for the test
     * @param int $testNo This should be the test number you which to get the questions for
     * @return boolean If the test questions are inserted into the database will return true else returns false
     */
    protected function chooseQuestions($testNo)
    {
        $questions = $this->db->selectAll($this->testsTable, ['test' => $testNo], ['prim'], ['position' => 'ASC']);
        $this->db->delete($this->progressTable, array_merge(['user_id' => $this->getUserID(), 'test_id' => $testNo], ($this->deleteOldTests === true ? [] : ['status' => 0])));
        unset($_SESSION['test'.$this->getTest()]);
        if (is_array($questions)) {
            foreach ($questions as $i => $question) {
                $this->questions[($i + 1)] = $question['prim'];
            }
            return $this->db->insert($this->progressTable, ['user_id' => $this->getUserID(), 'questions' => serialize($this->questions), 'answers' => serialize([]), 'test_id' => $testNo, 'started' => date('Y-m-d H:i:s'), 'status' => 0]);
        }
        return false;
    }
    
    /**
     * Checks to see if their is currently a test which is not complete or a test which has already been passed
     * @return string|false
     */
    protected function anyExisting()
    {
        if (is_string($this->exists)) {
            return $this->exists;
        }
        $existing = $this->db->select($this->progressTable, ['user_id' => $this->getUserID(), 'test_id' => $this->getTest(), 'status' => ['<=', 1]]);
        if (!empty($existing)) {
            $this->exists = ($existing['status'] == 1 ? 'passed' : 'exists');
            return $this->exists;
        }
        return false;
    }
    
    /**
     * Returns the JavaScript script to be displayed on the page
     * @return string Returns the JavaScript script to be displayed on the page
     */
    protected function existingScript()
    {
        return $this->getJavascriptLocation().'existing-'.$this->scriptVar.'.js';
    }

    /**
     * If a test already exist for the test ID this will add variables for the template to displayed a confirmation of new test
     * @return void Nothing is returned
     */
    protected function existingLayout()
    {
        $this->layout->assign('existing_text', $this->anyExisting());
        $this->layout->assign('seconds', ($this->anyExisting() !== 'passed' ? $this->getSeconds() : false));
        $this->layout->assign('continue_test', ($this->anyExisting() !== 'passed' ? $this->questionPrim($this->currentQuestion()) : false));
        $this->layout->assign('script', $this->existingScript());
        $this->questiondata = $this->layout->fetch('existing.tpl');
    }
        
    /**
     * Gets the questions array from the database if $this->questions is not set
     * @return array|false Returns the questions array if it exists else returns false
     */
    public function getQuestions()
    {
        if (!isset($this->questions)) {
            $questions = $this->getUserProgress();
            if (!empty($questions)) {
                $this->questions = unserialize(stripslashes($questions['questions']));
                return $this->questions;
            }
            return false;
        }
    }
    
    /**
     * Returns the current users answers for the current test
     * @return array|false Returns the current users answers for the current test if any exist else returns false
     */
    public function getUserAnswers()
    {
        if (!isset($this->useranswers)) {
            $answers = $this->getUserProgress();
            if (!empty($answers)) {
                $this->useranswers = unserialize(stripslashes($answers['answers']));
                if (!is_array($this->getUserTestInfo())) {
                    $_SESSION['test'.$this->getTest()] = $this->useranswers;
                }
                if (!isset($_SESSION['question_no']['test'.$this->getTest()])) {
                    $_SESSION['question_no']['test'.$this->getTest()] = $answers['question_no'];
                }
                $this->testID = $answers['id'];
                return $this->useranswers;
            }
        }
        return false;
    }
    
    /**
     * Gets the users information for the current test
     * @return array|false
     */
    protected function getUserProgress()
    {
        if (!empty($this->userProgress)) {
            return $this->userProgress;
        }
        $this->userProgress = $this->db->select($this->progressTable, ['user_id' => $this->getUserID(), 'test_id' => $this->getTest()], '*', ['started' => 'DESC']);
        return $this->userProgress;
    }


    /**
     * Returns the number of questions in the test
     * @return int Returns the number of questions
     */
    public function numQuestions()
    {
        $this->getQuestions();
        return count($this->questions);
    }
    
    /**
     * Sets and returns the current question number
     * @return int Returns the current question number
     */
    protected function currentQuestion()
    {
        if (!isset($this->current) && isset($_SESSION['question_no'])) {
            $this->current = $_SESSION['question_no']['test'.$this->getTest()];
        } elseif (!isset($this->current)) {
            $this->current = 1;
        }
        return $this->current;
    }
    
    /**
     * Returns the prim number for any given question number
     * @param int $questionNo This should be the question number in the current test
     * @return int Returns the unique prim number for the question
     */
    public function questionPrim($questionNo)
    {
        $this->getQuestions();
        return $this->questions[intval($questionNo)];
    }
    
    /**
     * Gets the question number of a given prim for the test
     * @param int $prim This should be the prim number of the question you wish to fin the question number for
     * @return int Returns the question number
     */
    public function questionNo($prim)
    {
        $this->getQuestions();
        $key = array_keys($this->questions, $prim);
        return intval($key[0]);
    }
    
    /**
     * Returns the prim number of the first question
     * @return int Returns the first question prim number
     */
    protected function getFirstQuestion()
    {
        $this->getQuestions();
        return $this->questions[1];
    }
    
    /**
     * Returns the prim number of the last question
     * @return int Returns the last question prim number
     */
    protected function getLastQuestion()
    {
        $this->getQuestions();
        return $this->questions[$this->numQuestions()];
    }
    
    /**
     * Returns the next flagged question number
     * @param string $dir This should be set to 'next' for the next question or 'prev' for the previous question
     * @param int|false If you want to search for anything above or below a current question set this to the question number else set to false
     * @return int Returns the next question ID if one exists else will return false
     */
    public function getNextFlagged($dir = 'next', $current = false)
    {
        if (!is_numeric($current)) {
            $current = $this->currentQuestion();
        }
        for ($q = $current; ($dir === 'next' ? $q <= $this->numQuestions() : $q >= 1); ($dir === 'next' ? $q++ : $q--)) {
            if ($q != $current && $this->getUserTestInfo()[$q]['flagged'] == 1) {
                return (int)$q;
            }
        }
        if ($this->numFlagged() > 1) {
            return (int)$this->getNextFlagged($dir, ($dir === 'next' ? 0 : ($this->numQuestions() + 1)));
        }
    }
    
    /**
     * Returns the next incomplete question
     * @param string $dir This should be set to 'next' for the next question or 'prev' for the previous question
     * @param int|false $questionNo The number to start the count from
     * @return int Returns the next incomplete question ID if one exists else will return false
     */
    public function getNextIncomplete($dir = 'next', $questionNo = false)
    {
        $current = $this->currentQuestion();
        for ($q = (is_numeric($questionNo) ? $questionNo : $current); ($dir === 'next' ? $q <= $this->numQuestions() : $q >= 1); ($dir === 'next' ? $q++ : $q--)) {
            $value = $this->getUserTestInfo()[$q]['status'];
            if ($q != $current && ($value < 3 || !$value)) {
                return (int)$q;
            }
        }
        if ($this->numIncomplete() > 1) {
            return (int)$this->getNextIncomplete($dir, ($dir === 'next' ? 1 : $this->numQuestions()));
        }
    }
    
    /**
     * Change the audio enabled settings
     * @param string $status Should be set to either 'on' or 'off'
     * @return string If the settings are updated will return true else returns false as a JSON string
     */
    public function audioEnable($status = 'on')
    {
        if ($status == 'on') {
            $this->audioEnabled = true;
        } else {
            $this->audioEnabled = false;
        }
        $settings = $this->checkSettings();
        $settings['audio'] = $status;
        return json_encode($this->user->setUserSettings($settings));
    }
    
    /**
     * Returns the HTML5 audio HTML information as a string
     * @param int $prim This should be the question prim number
     * @param string $letter This should be the letter of the question or answer
     * @return array Returns the array information needed for the audio
     */
    protected function addAudio($prim, $letter)
    {
        if ($this->audioEnabled && is_numeric($prim)) {
            return ['enabled' => true, 'file' => strtoupper($letter).$prim];
        }
        return false;
    }
    
    /**
     * Returns the audio switch button
     * @return boolean If the user can play audio the button will be returned else returns false
     */
    protected function audioButton()
    {
        return boolval($this->audioEnabled);
    }
    
    /**
     * Updates the database to enable or disable the hint button and display/hide contents
     * @return string If the settings are updated will return true else returns false as a JSON string
     */
    public function hintEnable()
    {
        $settings = $this->checkSettings();
        $settings['hint'] = ($settings['hint'] === 'on' ? 'off' : 'on');
        return json_encode($this->user->setUserSettings($settings));
    }
    
    /**
     * Returns the image HTML if the image exists else returns false
     * @param string $file Should be the image name and extension
     * @param boolean $main If the image is from the question should be set to true
     * @return array|false Returns image array information if it exists
     */
    public function createImage($file, $main = false)
    {
        if ($file != null && $file != '' && file_exists($this->getImageRootPath().$this->getImagePath().$file)) {
            list($width, $height) = getimagesize($this->getImageRootPath().$this->getImagePath().$file);
            $image = [];
            $image['width'] = $width;
            $image['height'] = $height;
            $image['src'] = $this->getImagePath().$file;
            $image['main'] = $main;
            return $image;
        }
    }
    
    /**
     * Returns the correct JavaScript file required for the page
     * @param boolean $review If in the review section should be set to true to force script
     * @return string Returns the script needed for the page the user is currently on
     */
    protected function getScript($review = false)
    {
        if ($this->review !== 'answers' && $review === false) {
            return $this->getJavascriptLocation().'theory-test-'.$this->scriptVar.'.js';
        }
        return $this->getJavascriptLocation().'review-'.$this->scriptVar.'.js';
    }
    
    /**
     * Returns the number of answers which need to be marked
     * @param int $num This should be the number of answers to select
     * @return array Returns the array with the number of questions to mark
     */
    protected function getMark($num)
    {
        $number = [1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six'];
        return ['num' => intval($num), 'text' => $number[intval($num)], 'plural' => ($num > 1 ? true : false)];
    }
    
    /**
     * If reviewing a particular set of questions will provide the alert HTML of false
     * @return string|false Returns the alert HTML if in the correct section else return false
     */
    protected function alert()
    {
        if ($this->review === 'flagged' || $this->review === 'incomplete') {
            return $this->review;
        } elseif ($this->review === false && $this->numComplete() == $this->numQuestions()) {
            return 'allmarked';
        }
        return false;
    }
    
    /**
     * Displays the correct buttons for the section
     * @param int $prim The current question prim number
     * @return string Returns the button HTML code
     */
    protected function flagHintButton($prim)
    {
        if ($this->review !== 'answers') {
            return ['text' => 'Flag <span class="d-none d-xl-inline-block visible-lg-inline-block">Question</span>', 'class' => 'flag'.($this->questionFlagged($prim) ? ' flagged' : ''), 'icon' => 'flag'];
        }
        return ['text' => 'Explain', 'class' => 'viewfeedback'.($this->checkSettings()['hint'] === 'on' ? ' flagged' : ''), 'icon' => 'book'];
    }
    
    /**
     * Returns the review button HTML code
     * @return array Returns the button array information
     */
    protected function reviewButton()
    {
        if ($this->review !== 'answers') {
            return ['text' => 'Review', 'class' => 'review', 'icon' => 'binoculars'];
        }
        return ['text' => 'End Review', 'class' => 'endreview', 'icon' => 'reply'];
    }
    
    /**
     * Returns the current user settings for the test
     * @param boolean $new If it is a new test should be set to true
     * @return array Returns the current test settings
     */
    protected function checkSettings($new = false)
    {
        if (!is_numeric($this->userClone)) {
            $settings = $this->user->getUserSettings();
            if ($new !== true && isset($settings['review'])) {
                if ($settings['review'] == 'all') {
                    $this->review = 'all';
                } elseif ($settings['review'] == 'flagged') {
                    $this->review = 'flagged';
                } elseif ($settings['review'] == 'incomplete') {
                    $this->review = 'incomplete';
                } elseif ($settings['review'] == 'answers') {
                    $this->review = 'answers';
                }
            } else {
                $this->review = false;
            }
        } else {
            $settings = [];
            $this->review = 'answers';
        }
        if (isset($settings['audio']) && $settings['audio'] == 'on') {
            $this->audioEnabled = true;
        }
        return $settings;
    }
    
    /**
     * Updates the test review type in the settings
     * @param string $type Should be the review type (e.g. 'all', 'flagged', 'incomplete', etc)
     * @return string If the settings are updated will return true else returns false as a JSON string
     */
    public function reviewOnly($type = 'all')
    {
        $settings = $this->checkSettings();
        $settings['review'] = $type;
        return json_encode($this->user->setUserSettings($settings));
    }
    
    /**
     * Adds a given answer to the users progress in the database
     * @param string $answer This is the answer the user has just selected
     * @param int $prim The current question number to add the answer to
     * @return string Will return true as a JSON string
     */
    public function addAnswer($answer, $prim)
    {
        $arraystring = str_replace($answer, '', trim(filter_var($this->getUserTestInfo()[$this->questionNo($prim)]['answer'], FILTER_SANITIZE_STRING))).$answer;
        return $this->replaceAnswer($this->sortAnswers($arraystring), $prim);
    }
       
    /**
     * Replaces the answer for the given prim number
     * @param string $letters This should be the answer the user has selected
     * @param int $prim This should be the question prim number
     * @return string Will return true as a JSON string
     */
    public function replaceAnswer($letters, $prim)
    {
        $this->updateTestProgress($prim);
        $qNo = $this->currentQuestion();
        $questiondata = $this->getQuestionData($prim);
        $answer = strtoupper($letters);
        $_SESSION['test'.$this->getTest()][$qNo]['answer'] = $answer;
        if (strlen($answer) == $questiondata['mark']) {
            if ($answer == $questiondata['answerletters']) {
                $_SESSION['test'.$this->getTest()][$qNo]['status'] = 4;
            } else {
                $_SESSION['test'.$this->getTest()][$qNo]['status'] = 3;
            }
        } else {
            $_SESSION['test'.$this->getTest()][$qNo]['status'] = 1;
        }
        
        return json_encode(true);
    }
    
    /**
     * Removes a given answer from the current question
     * @param string $answer This should be the answer you wish to remove
     * @param int $prim This should be the question prim you wish to remove the answer from
     * @return string Will return true as a JSON string
     */
    public function removeAnswer($answer, $prim)
    {
        $this->updateTestProgress($prim);
        $qNo = $this->currentQuestion();
        $removed = str_replace(strtoupper($answer), '', filter_var($this->getUserTestInfo()[$qNo]['answer'], FILTER_SANITIZE_STRING));
        $_SESSION['test'.$this->getTest()][$qNo]['answer'] = $removed;
        if ($removed === '') {
            $_SESSION['test'.$this->getTest()][$qNo]['status'] = 0;
        } else {
            $_SESSION['test'.$this->getTest()][$qNo]['status'] = 1;
        }

        return json_encode(true);
    }
    
    /**
     * Sorts the answer letters into alphabetical order to compare to the database
     * @param string $string If the string length is greater than 1 in length will break apart and sort else will simply return original value
     * @return string The ordered string or original string will be returned
     */
    protected function sortAnswers($string)
    {
        if (strlen($string) > 1) {
            $stringParts = str_split($string);
            sort($stringParts);
            $string = implode('', $stringParts);
        }
        return $string;
    }

    /**
     * Adds/Removes flags the particular question
     * @param int $prim This should be the question prim
     * @return string Should return true if flag status has been updated else returns false as A JSON string
     */
    public function flagQuestion($prim)
    {
        if (filter_var($this->getUserTestInfo()[$this->questionNo($prim)]['flagged'], FILTER_SANITIZE_NUMBER_INT) === 0 || !filter_var($this->getUserTestInfo()[$this->questionNo($prim)]['flagged'], FILTER_VALIDATE_INT)) {
            $_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['flagged'] = 1;
        } else {
            unset($_SESSION['test'.$this->getTest()][$this->questionNo($prim)]['flagged']);
        }
        return json_encode(true);
    }
    
    /**
     * Updates the `useranswers` field in the progress table in the database
     * @return string If updated will return true else for failure returns false as a JSON string
     */
    protected function updateAnswers()
    {
        if (!empty($this->getUserTestInfo())) {
            return json_encode($this->db->update($this->progressTable, ['answers' => serialize($this->getUserTestInfo()), 'time_remaining' => $_SESSION['time_remaining']['test'.$this->getTest()], 'question_no' => $this->currentQuestion()], ['user_id' => $this->getUserID(), 'test_id' => $this->getTest(), 'current_test' => 1]));
        }
        return json_encode(false);
    }
    
    /**
     * Public function to save the users information before the page is exited
     * @return string If updated will return true else for failure returns false as a JSON string
     */
    public function saveProgress()
    {
        return $this->updateAnswers();
    }
    
    /**
     * Returns the number of complete questions
     * @return int Should return the number of complete questions
     */
    public function numComplete()
    {
        $num = 0;
        if (is_array($this->getUserTestInfo())) {
            foreach ($this->getUserTestInfo() as $value) {
                $value = trim($value['status']);
                if ($value >= 2) {
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
    public function numIncomplete()
    {
        return (count($this->questions) - $this->numComplete());
    }
    
    /**
     * Returns the number of flagged questions
     * @return int Should return the number of flagged questions
     */
    public function numFlagged()
    {
        $num = 0;
        foreach (filter_var_array($this->getUserTestInfo()) as $value) {
            $value = trim($value['flagged']);
            if ($value == 1) {
                $num++;
            }
        }
        return $num;
    }
    
    /**
     * Returns the number of correct answers
     * @return int Returns the number of correct answers
     */
    protected function numCorrect()
    {
        $num = 0;
        foreach (filter_var_array($this->getUserTestInfo()) as $value) {
            $value = trim($value['status']);
            if ($value == 4) {
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
    protected function answerSelected($prim, $letter)
    {
        if (strpos(filter_var($this->getUserTestInfo()[$this->questionNo($prim)]['answer'], FILTER_SANITIZE_STRING), strtoupper($letter)) !== false) {
            return true;
        }
        return false;
    }
    
    /**
     * Checks to see if the answer is selected and if it is correct or not
     * @param int $prim Should be the question prim number
     * @param string $letter Should be the letter of the answer you are checking if it is correct
     * @return string|false Returns string if correct and not selected, selected and correct, or selected and incorrect else returns false
     */
    protected function answerSelectedCorrect($prim, $letter)
    {
        $isCorrect = $this->db->select($this->questionsTable, ['prim' => $prim, 'answerletters' => ['LIKE', '%'.strtoupper($letter).'%']], ['answerletters']);
        
        if ($this->answerSelected($prim, $letter) && !empty($isCorrect)) {
            return 'CORRECT';
        } elseif ($this->answerSelected($prim, $letter) && $isCorrect === false) {
            return 'INCORRECT';
        } elseif (!empty($isCorrect)) {
            return 'NSCORRECT';
        }
        return false;
    }
    
    /**
     * Checks to see if the current question is flagged or not
     * @param int $prim This should be the prim number of the question you are checking if it is flagged or not
     * @return boolean Returns true if current question is flagged else returns false
     */
    public function questionFlagged($prim)
    {
        if (isset($this->getUserTestInfo()[$this->questionNo($prim)]['flagged'])) {
            return true;
        }
        return false;
    }
    
    /**
     * This is to add extra content if required (Used on extension classes)
     * @return mixed
     */
    protected function extraContent()
    {
        return false;
    }
    
    /**
     * Returns the question data for the given prim number
     * @param int $prim Should be the question prim number
     * @return array|boolean Returns question data as array if data exists else returns false
     */
    protected function getQuestionData($prim)
    {
        return $this->db->select($this->questionsTable, ['prim' => $prim]);
    }
    
    /**
     * Returns the option HTML for a selected option of a question
     * @param int $question This should be the unique question prim number
     * @param string $option This should be the option text
     * @param int $answer_num This should be the option number
     * @param boolean $image If is a image question should be set to true else if it is multiple choice set to false (default)
     * @param boolean $new If the test is new this should be set to true else set to false
     * @return array Should return the option array for the given answer
     */
    protected function getOptions($question, $option, $answer_num, $image = false, $new = false)
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $options = [];
        if ($new === false && $this->review !== 'answers') {
            if ($this->answerSelected($question, $letters[$answer_num])) {
                $options['selected'] = true;
            }
        } elseif ($new === false) {
            $options['selected'] = strtolower($this->answerSelectedCorrect($question, $letters[$answer_num]));
        }
        if ($image !== false) {
            $options['image'] = $this->createImage($question.strtolower($letters[$answer_num]).'.png');
        }
        $options['audio'] = $this->addAudio($question, $letters[$answer_num]);
        $options['id'] = strtolower($letters[$answer_num].$question);
        $options['prim'] = $question;
        $options['letter'] = $letters[$answer_num];
        $options['option'] = $option;
        return $options;
    }
    
    /**
     * Produces the HTML information for the review page
     */
    public function reviewSection()
    {
        $this->updateAnswers();
        $this->layout->assign('test_questions', $this->numQuestions(), true);
        $this->layout->assign('complete_questions', $this->numComplete(), true);
        $this->layout->assign('incomplete_questions', $this->numIncomplete(), true);
        $this->layout->assign('flagged_questions', $this->numFlagged(), true);
        $this->layout->assign('review_all', $this->getFirstQuestion(), true);
        $this->layout->assign('review_incomplete', $this->getIncompleteQuestion(), true);
        $this->layout->assign('review_flagged', $this->getFlaggedQuestion(), true);
        $this->layout->assign('script', $this->getScript(false), true);
        return json_encode($this->layout->fetch('review.tpl'));
    }
    
    /**
     * Creates the HTML for an entire Theory Test for use when creating a new test
     * @return string Returns the test HTML code
     */
    public function buildTest()
    {
        if ($this->exists) {
            $this->existingLayout();
        } else {
            $this->createQuestionHTML($this->getFirstQuestion(), true);
        }
        $this->layout->assign('test_name', $this->getTestName(), true);
        $this->layout->assign('question_no', '1', true);
        $this->layout->assign('no_questions', $this->numQuestions(), true);
        $this->layout->assign('question_data', $this->questiondata, true);
        $this->layout->assign('js_script_location', $this->getJavascriptLocation());
        $this->layout->assign('report', false);
        return $this->layout->fetch($this->section.'test.tpl');
    }
    
    /**
     * Returns the test report HTML code
     * @param boolean $mark If the test needs to be marked should be set to true else should be false
     * @return string Returns the report HTML code
     */
    public function buildReport($mark = true)
    {
        $this->endTest($this->getTime(), $mark);
        $this->layout->assign('test_name', $this->getTestName(), true);
        $this->layout->assign('question_data', $this->questiondata, true);
        $this->layout->assign('report', true);
        return $this->layout->fetch($this->section.'test.tpl');
    }
    
    /**
     * Creates the HTML for the given question number
     * @param int $prim This should be the prim number for the selected question
     * @param boolean $new If if is a new test should be set to true else should be false
     * @return string|boolean Returns the question HTML and Question number as a JSON encoded string if question exists else returns false
     */
    public function createQuestionHTML($prim, $new = false)
    {
        $this->updateTestProgress($prim);
        $this->checkSettings($new);
        $question = $this->getQuestionData($prim);
        if (!empty($question)) {
            if (isset($question['casestudyno']) && is_numeric($question['casestudyno'])) {
                $this->setCaseStudy($question['casestudyno']);
            }
            $image = (($question['format'] == '0' || $question['format'] == '2') ? false : true);
            $this->layout->assign('mark', $this->getMark($question['mark']));
            $this->layout->assign('prim', $prim);
            $question['audio'] = $this->addAudio($prim, 'Q');
            $this->layout->assign('question', $question);
            $answers = [];
            for ($a = 1; $a <= $this->noAnswers; $a++) {
                $answers[$a] = $this->getOptions($question['prim'], $question['option'.$a], ($a - 1), $image, $new);
            }
            $this->layout->assign('answers', array_filter($answers));
            $this->layout->assign('image', (!empty($question['dsaimageid']) ? $this->createImage($question['prim'].'.jpg', true) : false));
            $this->layout->assign('case_study', $this->casestudy);
            $this->layout->assign('dsa_explanation', (isset($question['dsaexplanation']) && !empty($question['dsaexplanation']) ? $this->dsaExplanation($question['dsaexplanation'], $prim) : false));
            $this->layout->assign('previous_question', $this->prevQuestion());
            $this->layout->assign('flag_question', $this->flagHintButton($question['prim']));
            $this->layout->assign('review', $this->reviewButton());
            $this->layout->assign('next_question', $this->nextQuestion());
            $this->layout->assign('script', $this->getScript());
            $this->layout->assign('alert', $this->alert());
            $this->layout->assign('review_questions', $this->reviewAnswers());
            $this->layout->assign('extra', $this->extraContent());
            $this->layout->assign('audio', $this->audioButton());
        }
        $this->questiondata = $this->layout->fetch((!empty($question) ? 'layout'.$question['format'] : 'empty').'.tpl');
        return json_encode(['html' => utf8_encode($this->questiondata), 'questionnum' => $this->questionNo($prim)]);
    }
    
    /**
     * Returns the question information e.g. category, topic number for any given prim
     * @param int $prim this is the question unique number
     * @return array Returns that particular prim info
     */
    public function questionInfo($prim)
    {
        $info = [];
        $questioninfo = $this->db->select($this->questionsTable, ['prim' => $prim], ['prim', 'dsacat', 'dsaqposition']);
        $catinfo = $this->db->select($this->dvsaCatTable, ['section' => $questioninfo['dsacat']]);
        $info['prim'] = $questioninfo['prim'];
        $info['cat'] = $questioninfo['dsacat'].'. '.$catinfo['name'];
        $info['topic'] = ($questioninfo['dsaqposition'] ? $questioninfo['dsaqposition'] : 'Case Study');
        return $info;
    }
    
    /**
     * Updates the test progress in the database
     * @param int $prim This should be the current question prim number
     */
    protected function updateTestProgress($prim)
    {
        $this->current = $this->questionNo($prim);
        if ($this->current < 1) {
            $this->current = 1;
        }
        $_SESSION['question_no']['test'.$this->getTest()] = $this->current;
    }

    /**
     * Gets the first Flagged question prim if one exists else returns 'none'
     * @return int|string Should be the first flagged question prim or 'none' if none exist
     */
    protected function getFlaggedQuestion()
    {
        $q = 1;
        foreach ($this->getUserTestInfo() as $value) {
            if ($value['flagged'] == 1) {
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
    protected function getIncompleteQuestion()
    {
        for ($q = 1; $q < $this->numQuestions(); $q++) {
            if ($this->getUserTestInfo()[$q]['status'] <= 1 || !isset($this->getUserTestInfo()[$q])) {
                return $this->questionPrim($q);
            }
        }
        return 'none';
    }
    
    /**
     * Returns the correct HTML for the DVSA explanation in the review section
     * @param string $explanation Should be the DVSA explanation for the particular question
     * @param int $prim Should be the prim number of the current question
     * @return array|false Returns the HTML string if in the review section else returns false
     */
    public function dsaExplanation($explanation, $prim)
    {
        if ($this->review == 'answers') {
            $explain = [];
            $explain['visable'] = (isset($this->checkSettings()['hint']) && $this->checkSettings()['hint'] == 'on' ? ' visable' : '');
            $explain['audio'] = $this->addAudio($prim, 'DSA');
            $explain['explanation'] = $explanation;
            return $explain;
        }
        return false;
    }
    
    /**
     * This should set the case study for this group of questions
     * @param int $casestudy This should be the case study number for the set of questions
     * @return $this
     */
    protected function setCaseStudy($casestudy)
    {
        $case = $this->db->select($this->caseTable, ['casestudyno' => $casestudy]);
        $this->casestudy = ['case' => $case['cssituation'], 'audio' => $this->addAudio($casestudy, 'CS'), 'video' => $case['video'], 'videoLocation' => $this->getVidLocation(), 'ratio' => $case['ratio']];
        return $this;
    }
    
    /**
     * Clears the test settings in the database
     * @return boolean Returns true if the settings are cleared and updated else returns false
     */
    protected function clearSettings()
    {
        unset($_SESSION['test'.$this->getTest()]);
        unset($_SESSION['question_no']);
        $settings = $this->checkSettings();
        $settings['review'] = false;
        $settings['hint'] = 'off';
        return $this->user->setUserSettings($settings);
    }
    
    /**
     * Sets the current test number
     * @param int $testNo This should be the current test number
     * @return $this
     */
    public function setTest($testNo)
    {
        unset($this->questions, $this->userProgress, $this->useranswers, $this->testID, $this->testData);
        if (is_numeric($testNo) && $this->testNo !== $testNo) {
            $this->testNo = $testNo;
        }
        $settings = $this->checkSettings();
        $settings['current_test'] = $testNo;
        $_SESSION['current_test'] = $testNo;
        if ($this->user->setUserSettings($settings)) {
            $this->getQuestions();
            $this->getUserAnswers();
        }
        return $this;
    }
    
    /**
     * Returns the test number
     * @return int Returns the current test number
     */
    public function getTest()
    {
        if (!is_numeric($this->testNo)) {
            if (isset($_SESSION['current_test'])) {
                $this->testNo = $_SESSION['current_test'];
            } else {
                $testNo = $this->user->getUserSettings();
                if (is_array($testNo)) {
                    $this->testNo = $testNo['current_test'];
                }
            }
        }
        return $this->testNo;
    }
    
    /**
     * Sets the current test name
     * @param string $name This should be the name of the test you wish to set it to if left blank will just be Theory Test plus test number
     * Return $this
     */
    protected function setTestName($name = '')
    {
        if (!empty($name)) {
            $this->testName = $name;
        } else {
            $this->testName = 'Theory Test '.$this->getTest();
        }
        return $this;
    }
    
    /**
     * Returns the test name
     * @return string Returns the current test name
     */
    public function getTestName()
    {
        if (empty($this->testName)) {
            $this->setTestName();
        }
        return $this->testName;
    }
        
    /**
     * Produces the amount of time the user has spent on the current test
     * @param int $time This should be the amount of seconds remaining for the current test
     * @param string $type This should be either set to 'taken' or 'remaining' depending on which you wish to update 'taken' by default
     * return $this
     */
    public function setTime($time, $type = 'taken')
    {
        if ($time) {
            if ($type == 'taken') {
                list($mins, $secs) = explode(':', $time);
                $newtime = gmdate('i:s', ($this->getStartSeconds() - (($mins * 60) + $secs)));
                $this->userProgress['time_taken'] = $newtime;
                $this->db->update($this->progressTable, ['time_'.$type => $newtime], ['user_id' => $this->getUserID(), 'test_id' => $this->getTest(), 'current_test' => 1]);
            } else {
                $_SESSION['time_'.$type]['test'.$this->getTest()] = $time;
            }
        }
        return $this;
    }
    
    /**
     * Gets the Time taken or 'remaining for the current test
     * @param string $type This should be either set to 'taken' or 'remaining' depending on which you wish to get 'taken' by default
     * @return string Returns the time from the database
     */
    public function getTime($type = 'taken')
    {
        $userProgress = $this->getUserProgress();
        return (isset($userProgress['time_'.$type]) ? $userProgress['time_'.$type] : false);
    }
    
    /**
     * Gets the number of seconds remaining for the current test
     * @return int Returns the current number of seconds remaining for the test
     */
    protected function getSeconds()
    {
        $remaining = $this->getTime('remaining');
        list($minutes, $seconds) = explode(':', ($remaining ? $remaining : gmdate('i:s', $this->seconds)));
        return intval((intval($minutes) * 60) + intval($seconds));
    }

    /**
     * Returns the previous question button HTML with correct id in code
     * @return string Returns the previous question button HTML code
     */
    protected function prevQuestion()
    {
        return $this->getNextPrevButtonArray(($this->currentQuestion() - 1), $this->getLastQuestion(), 'prev', 'Prev<span class="d-none d-lg-inline-block visible-lg-inline-block">ious</span>', 'angle-left');
    }
    
    /**
     * Returns the next question button HTML with correct id in code
     * @return string Returns the next question button HTML code
     */
    protected function nextQuestion()
    {
        return $this->getNextPrevButtonArray(($this->currentQuestion() + 1), $this->getFirstQuestion());
    }
    
    /**
     * Return the button array information for the next button
     * @param int $nextQuestion This should be the next question number
     * @param int $loopQuestion If first of last should be the opposite end prim
     * @param string $dir The direction of the next question 'prev' or 'next'
     * @param string $text The text on the button
     * @param string $icon The font awesome icon the button should display
     * @return array|boolean If the button exists will return the information array else will return false
     */
    protected function getNextPrevButtonArray($nextQuestion, $loopQuestion, $dir = 'next', $text = 'Next', $icon = 'angle-right')
    {
        if ($this->checkIfLast($this->numQuestions())) {
            if ($this->review == 'flagged' && $this->numFlagged() > 1) {
                $next = $this->questionPrim($this->getNextFlagged($dir));
            } elseif ($this->review == 'incomplete' && $this->numIncomplete() > 1) {
                $next = $this->questionPrim($this->getNextIncomplete($dir));
            } else {
                $next = $this->questionPrim($nextQuestion);
            }
            return ['id' => $next, 'text' => $text, 'icon' => $icon];
        }
        if ($this->review === 'all' || $this->review === 'answers' || $this->review === false) {
            return ['id' => $loopQuestion, 'text' => $text, 'icon' => $icon];
        }
        return false;
    }
    
    /**
     * Check to see if its the last question in the loop or not
     * @param int $noQuestions The number of the question it should not match i.e. (1 of last)
     * @return boolean If it is not the last in the loop will return true else return false
     */
    protected function checkIfLast($noQuestions = 1)
    {
        if (($this->review === 'flagged' && $this->numFlagged() > 1) || ($this->review === 'incomplete' && $this->numIncomplete() > 1) || ((int)$this->currentQuestion() != $noQuestions && ($this->review === 'all' || $this->review === false || $this->review === 'answers'))) {
            return true;
        }
        return false;
    }
    
    /**
     * Returns the questions DVSA category number
     * @param int $prim This should be the prim number of the current question
     * @return int Returns the DVSA Category number of the current question
     */
    protected function getDSACat($prim)
    {
        return $this->db->fetchColumn($this->questionsTable, ['prim' => $prim], ['dsacat']);
    }
    
    /**
     * Deletes the existing test for the current user if they wish to start again
     * @return string If existing tests are deleted will return true else will return false as a JSON string
     */
    public function startNewTest()
    {
        return json_encode($this->db->delete($this->progressTable, array_merge(['user_id' => $this->getUserID(), 'test_id' => $this->getTest()], ($this->deleteOldTests === true ? [] : ['status' => 0]))));
    }
    
    /**
     * Ends the current test and starts the process to mark if necessary
     * @param int $time The amount of time taken for the current test
     * @param boolean $mark If the test needed to be marked should set to true
     * @return string The end test HTML code will be returned
     */
    public function endTest($time, $mark = true)
    {
        if ($mark === true) {
            $this->markTest($time);
        } else {
            $this->getTestResults();
        }
        $this->layout->assign('report', $this->testReport());
        $this->layout->assign('results', $this->testresults);
        $this->layout->assign('percentages', $this->testPercentages());
        $this->layout->assign('dsa_cat_results', $this->createOverviewResults());
        $this->layout->assign('review_test', $this->getFirstQuestion());
        $this->layout->assign('print_certificate', $this->printCertif());
        $this->layout->assign('script', $this->getScript(true));
        $this->questiondata = $this->layout->fetch('results.tpl');
        return json_encode($this->questiondata);
    }
    
    /**
     * Marks the current test
     * @param int|false $time The time to set as taken for the current test of false to not update
     * @return $this
     */
    protected function markTest($time = false)
    {
        $this->getQuestions();
        foreach ($this->questions as $prim) {
            if ($this->getUserTestInfo()[$this->questionNo($prim)]['status'] == 4) {
                $type = 'correct';
            } else {
                $type = 'incorrect';
            }
            $dvsa = $this->getDSACat($prim);
            $this->testresults['dvsa'][$dvsa][$type] = (int)$this->testresults['dvsa'][$dvsa][$type] + 1;
        }
        
        $this->testresults['correct'] = $this->numCorrect();
        $this->testresults['incorrect'] = ($this->numQuestions() - $this->numCorrect());
        $this->testresults['incomplete'] = $this->numIncomplete();
        $this->testresults['flagged'] = $this->numFlagged();
        $this->testresults['numquestions'] = $this->numQuestions();
        $this->testresults['percent']['correct'] = round($this->testresults['correct'] / $this->testresults['numquestions'] * 100);
        $this->testresults['percent']['incorrect'] = round($this->testresults['incorrect'] / $this->testresults['numquestions'] * 100);
        $this->testresults['percent']['flagged'] = round($this->testresults['flagged'] / $this->testresults['numquestions'] * 100);
        $this->testresults['percent']['incomplete'] = round($this->testresults['incomplete'] / $this->testresults['numquestions'] * 100);
        //$this->updateLearningSection();
        if ($this->numCorrect() >= $this->getPassmark()) {
            $this->testresults['status'] = 'pass';
            $status = 1;
        } else {
            $this->testresults['status'] = 'fail';
            $status = 2;
        }
        if ($time !== false && preg_match('~[0-9]+~', $time)) {
            list($mins, $secs) = explode(':', $time);
            $newtime = gmdate('i:s', ($this->getStartSeconds() - (($mins * 60) + $secs)));
            $this->userProgress['time_taken'] = $newtime;
        }
        $this->db->update($this->progressTable, array_merge(['status' => $status, 'answers' => serialize($this->getUserTestInfo()), 'results' => serialize($this->testresults), 'complete' => date('Y-m-d H:i:s'), 'totalscore' => $this->numCorrect(), 'current_test' => 0], ($time !== false ? ['time_taken' => $newtime] : [])), ['user_id' => $this->getUserID(), 'test_id' => $this->getTest(), 'current_test' => 1]);
        return $this;
    }
    
    /**
     * Updated the learning progress to show what questions have been correctly answer in the test
     * @return boolean Returns true if the learning progress has been updated
     */
    public function updateLearningSection()
    {
        $info = $this->db->select($this->learningProgressTable, ['user_id' => $this->getUserID()], ['progress']);
        $userprogress = unserialize(stripslashes($info['progress']));
        $this->getQuestions();
        foreach ($this->questions as $prim) {
            $answer = $this->getUserTestInfo()[$this->questionNo($prim)]['answer'];
            if (!empty($answer)) {
                $userprogress[$prim]['answer'] = $answer;
                if ($this->getUserTestInfo()[$this->questionNo($prim)]['status'] == '4') {
                    $userprogress[$prim]['status'] = 2;
                } elseif ($this->getUserTestInfo()[$this->questionNo($prim)]['status'] == '3') {
                    $userprogress[$prim]['status'] = 1;
                }
            }
        }
        return $this->db->update($this->learningProgressTable, ['progress' => serialize(array_filter($userprogress))], ['user_id' => $this->getUserID()]);
    }
    
    /**
     * Returns the print certificate button
     * @return array Returns the print certificate/report variables
     */
    protected function printCertif()
    {
        $certificate = [];
        $certificate['status'] = $this->testresults['status'];
        $certificate['location'] = '/certificate.pdf?testID='.$this->getTest();
        return $certificate;
    }
    
    /**
     * Returns the test results for the current test
     * @return boolean|array If the test has been completed the test results will be returned as an array else will return false
     */
    public function getTestResults()
    {
        $results = $this->db->select($this->progressTable, ['user_id' => $this->getUserID(), 'test_id' => $this->getTest(), 'status' => ['>', 0]], ['id', 'test_id', 'results', 'started', 'complete', 'time_taken', 'status'], ['started' => 'DESC']);
        if (!empty($results)) {
            $this->testresults = unserialize(stripslashes($results['results']));
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
    public function testStatus()
    {
        return $this->testresults['status'];
    }
    
    /**
     * Returns the test report table
     * @return string Returns the test report table
     */
    protected function testReport()
    {
        $report = [];
        $this->getTestResults();
        $report['testname'] = ucwords($this->getTestName());
        if (method_exists($this->user, 'getFirstname') && method_exists($this->user, 'getLastname')) {
            $report['user'] = $this->user->getFirstname(is_numeric($this->userClone) ? $this->getUserID() : false).' '.$this->user->getLastname(is_numeric($this->userClone) ? $this->getUserID() : false);
        } elseif (method_exists($this->user, 'getUsername')) {
            $report['user'] = $this->user->getUsername(is_numeric($this->userClone) ? $this->getUserID() : false);
        }
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
    protected function testPercentages()
    {
        return $this->testresults;
    }
    
    /**
     * Creates an array of all of the categories
     * @return array Returns an array of all of the categories
     */
    public function getCategories()
    {
        return $this->db->selectAll($this->dvsaCatTable);
    }
    
    /**
     * Creates an overview of the test results
     * @return string Returns an overview of the test results table
     */
    protected function createOverviewResults()
    {
        $catresults = [];
        foreach ($this->getCategories() as $i => $dvsacat) {
            $catresults[$i]['section'] = $dvsacat['section'];
            $catresults[$i]['name'] = $dvsacat['name'];
            $catresults[$i]['correct'] = isset($this->testresults['dvsa'][$dvsacat['section']]['correct']) ? (int)$this->testresults['dvsa'][$dvsacat['section']]['correct'] : 0;
            $catresults[$i]['incorrect'] = isset($this->testresults['dvsa'][$dvsacat['section']]['incorrect']) ? (int)$this->testresults['dvsa'][$dvsacat['section']]['incorrect'] : 0;
            $catresults[$i]['total'] = ($catresults[$i]['correct'] + $catresults[$i]['incorrect'] + (isset($this->testresults['dvsa'][$dvsacat['section']]['unattempted']) ? (int)$this->testresults['dvsa'][$dvsacat['section']]['unattempted'] : 0));
        }
        return $catresults;
    }
    
    /**
     * Returns the status of each questions and the styles for the review answers section
     * @return string|false Returns the HTML code if they are in the reviewing answers section else return false
     */
    protected function reviewAnswers()
    {
        if ($this->review == 'answers') {
            $questions = [];
            for ($r = 1; $r <= $this->numQuestions(); $r++) {
                $questions[$r]['status'] = $this->getUserTestInfo()[$r]['status'];
                $questions[$r]['current'] = ($this->currentQuestion() == $r ? true : false);
                $questions[$r]['prim'] = $this->questionPrim($r);
            }
            return $questions;
        }
        return false;
    }
}
