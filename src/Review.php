<?php

namespace TheoryTest\Car;

use DBAL\Database;
use Configuration\Config;
use Smarty;

class Review
{
    protected $db;
    protected $config;
    protected $layout;
    protected $user;
    protected $userClone;
    
    public $where = ['carquestion' => 'Y', 'dsacat' => 'IS NOT NULL', 'alertcasestudy' => 'IS NULL'];
    public $whereCase = ['carquestion' => 'Y', 'dsacat' => 'IS NOT NULL', 'alertcasestudy' => 'IS NOT NULL', 'casestudyno' => ['>=' => 67]];
    
    public $noOfTests = 14;
    
    protected $testsTable;
    protected $questionsTable;
    protected $dvsaCatTable;
    protected $learningProgressTable;
    protected $progressTable;
    protected $caseStudyTable;
    
    protected $useranswers = [];
    
    protected $testType = 'CAR';


    /**
     * Connects to the database sets the current user and gets any user answers
     * @param Database $db This needs to be an instance of the database class
     * @param Smarty $layout This needs to be an instance of the Smarty Templating class
     * @param object $user This should be the user class used
     * @param int|false $userID If you want to emulate a user set the user ID here
     * @param string|false $templateDir If you want to change the template location set this location here else set to false
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
    }
    
    /*
     * Setter Allows table names to be changed if needed
     */
    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
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
        $this->dvsaCatTable = $this->config->table_theory_dvsa_sections;
        $this->caseStudyTable = $this->config->table_theory_case_studies;
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
     * Gets the section table for the learning section
     * @return array This will be the tables where learning questions are available
     */
    public function getSectionTables()
    {
        return [
            ['table' => $this->config->table_theory_hc_sections, 'name' => 'Highway Code Section', 'section' => 'hc', 'sectionNo' => 'hcsection'],
            ['table' => $this->config->table_theory_dvsa_sections, 'name' => 'DVSA Category', 'section' => 'dvsa', 'sectionNo' => 'dsacat'],
            'case' => true
        ];
    }
    
    /**
     * Returns the current users answers for the current test
     * @return array Returns the current users answers for the current test
     */
    public function getUserAnswers()
    {
        if (empty($this->useranswers) && $this->getUserID() >= 1) {
            $answers = $this->db->select($this->learningProgressTable, ['user_id' => $this->getUserID()], ['progress']);
            if (isset($answers['progress'])) {
                $this->useranswers = unserialize(stripslashes($answers['progress']));
            }
        }
        return $this->useranswers;
    }
    
    /*
     * Selects the number of unique test for a given test type
     * @return int Returns the number of unique test
     */
    public function numberOfTests()
    {
        if (!is_numeric($this->noOfTests)) {
            $this->db->query("SELECT DISTINCT `test` FROM `{$this->testsTable}`;");
            $this->noOfTests = $this->db->numRows();
        }
        return $this->noOfTests;
    }
    
    /**
     * Returns the number of tests passed
     * @return int Returns The number of tests the user has passed
     */
    public function testsPassed()
    {
        return $this->db->count($this->progressTable, ['status' => 1, 'user_id' => $this->getUserID(), 'type' => strtoupper($this->testType), 'test_id' => ['<=' => $this->noOfTests]]);
    }
    
    /**
     * Returns the number of tests failed
     * @return int Returns The number of tests the user has failed
     */
    public function testsFailed()
    {
        return $this->db->count($this->progressTable, ['status' => 2, 'user_id' => $this->getUserID(), 'type' => strtoupper($this->testType), 'test_id' => ['<=' => $this->noOfTests]]);
    }
    
    /**
     * Build the review table for the given categories
     * @param string $table The table which should be used to get the information
     * @param string $tableSecNo The field which that table should be sorted by
     * @param string $title The title that should be given to the table
     * @return string|boolean Returns the table as a HTML string if the information exists else will return false
     */
    public function buildReviewTable($table, $tableSecNo, $title, $section)
    {
        $where = $this->db->where($this->where);
        $categories = $this->db->query("SELECT `{$table}`.*, count(*) as `numquestions` FROM `{$table}`, `{$this->questionsTable}`".(empty($where) ? ' WHERE' : $where.' AND')."`section` = `{$tableSecNo}` GROUP BY `{$tableSecNo}`{$this->db->orderBy(['section' => 'ASC'])};", $this->db->values);
        $review = ['totalquestions' => 0, 'totalcorrect' => 0, 'totalnotattempted' => 0, 'totalincorrect' => 0];
        if (is_array($categories)) {
            if (empty($this->useranswers)) {
                $this->getUserAnswers();
            }
            $review['title'] = $title;
            $review['section'] = $section;
            foreach ($categories as $cat) {
                $review['ans'][$cat['section']] = $cat;
                $review['ans'][$cat['section']]['notattempted'] = 0;
                $review['ans'][$cat['section']]['incorrect'] = 0;
                $review['ans'][$cat['section']]['correct'] = 0;

                $questions = $this->db->selectAll($this->questionsTable, array_merge($this->where, [$tableSecNo => $cat['section']]), ['prim']);
                if (is_array($questions)) {
                    foreach ($questions as $question) {
                        if (!isset($this->useranswers[$question['prim']]) || $this->useranswers[$question['prim']]['status'] == 0) {
                            $review['ans'][$cat['section']]['notattempted']++;
                        } elseif ($this->useranswers[$question['prim']]['status'] == 1) {
                            $review['ans'][$cat['section']]['incorrect']++;
                        } elseif ($this->useranswers[$question['prim']]['status'] == 2) {
                            $review['ans'][$cat['section']]['correct']++;
                        }
                    }
                }
                $review['totalquestions'] = $review['totalquestions'] + $review['ans'][$cat['section']]['numquestions'];
                $review['totalcorrect'] = $review['totalcorrect'] + $review['ans'][$cat['section']]['correct'];
                $review['totalnotattempted'] = $review['totalnotattempted'] + $review['ans'][$cat['section']]['notattempted'];
                $review['totalincorrect'] = $review['totalincorrect'] + $review['ans'][$cat['section']]['incorrect'];
            }
        }
        return $review;
    }
    
    /**
     * Returns the HTML Table for the review section
     * @return string Returns the HTML code for the learning review section
     */
    public function buildTables()
    {
        $this->getUserAnswers();
        foreach ($this->getSectionTables() as $i => $tables) {
            if (is_array($tables)) {
                $this->layout->assign('table', $this->buildReviewTable($tables['table'], $tables['sectionNo'], $tables['name'], $tables['section']), true);
                $this->layout->assign('table'.($i + 1).'name', $tables['name'], true);
                $this->layout->assign($tables['section'].'section', $this->layout->fetch('table-learning.tpl'), true);
            } elseif ($tables === true) {
                $this->layout->assign('cases', $this->reviewCaseStudy(), true);
                $this->layout->assign('reviewsection', $this->layout->fetch('table-case.tpl'), true);
            }
        }
        return $this->layout->fetch('study.tpl');
    }
    
    /**
     * Build the case study review table
     * @return string|boolean If the case study information exists in the database the table will be returned as a HTML string else will return false
     */
    public function reviewCaseStudy()
    {
        $this->getUserAnswers();
        $cases = [];
        foreach ($this->db->selectAll($this->caseStudyTable, ['lp' => 'IS NOT NULL', 'video' => 'IS NOT NULL', 'type' => $this->testType], '*', ['casestudyno' => 'ASC']) as $i => $case) {
            $cases[$i] = $case;
            $cases[$i]['section'] = $case['casestudyno'];
            if (!is_null($case['video'])) {
                $cases[$i]['name'] = 'Video Case';
            } else {
                $cases[$i]['name'] = $this->db->fetchColumn($this->dvsaCatTable, ['section' => $case['dsacat']], ['name']);
            }
            foreach ($this->db->selectAll($this->questionsTable, ['casestudyno' => $case['casestudyno'], 'alertcasestudy' => 1], '*', ['caseqposition' => 'ASC']) as $num => $question) {
                $cases[$i]['q'][$num]['status'] = (isset($this->useranswers[$question['prim']]) ? $this->useranswers[$question['prim']]['status'] : 0);
                $cases[$i]['q'][$num]['num'] = ($num + 1);
            }
        }
        return $cases;
    }
    
    /**
     * Returns the answers for each of the tests ready to review
     * @return type Returns the answers for each of the tests ready to review
     */
    public function reviewTests()
    {
        $answers = [];
        for ($i = 1; $i <= ($this->numberOfTests() + 1); $i++) {
            if ($i > $this->numberOfTests()) {
                $testID = 'random';
            } else {
                $testID = $i;
            }
            unset($_SESSION['test'.$i]);
            $answers[$testID] = $this->db->select($this->progressTable, ['user_id' => $this->getUserID(), 'test_id' => $i, 'status' => ['>=', 1], 'type' => strtoupper($this->testType)], ['status', 'totalscore', 'complete']);
        }
        return $answers;
    }
    
    /**
     * Returns a summary of how the user has done on the questions and how many they have correct, incorrect and how many are incomplete
     * @return array Returns a summary of how the user has done on the questions and how many they have correct, incorrect and how many are incomplete as an array of numbers
     */
    public function userTestInformation()
    {
        $this->getUserAnswers();
        $notattempted = 0;
        $incorrect = 0;
        $correct = 0;
        $info = [];
        
        $questions = $this->db->selectAll($this->questionsTable, $this->where, ['prim']);
        $info['noQuestions'] = $this->db->rowCount();
        foreach ($questions as $question) {
            if (!isset($this->useranswers[$question['prim']]) || $this->useranswers[$question['prim']]['status'] == 0) {
                $notattempted++;
            } elseif ($this->useranswers[$question['prim']]['status'] == 1) {
                $incorrect++;
            } elseif ($this->useranswers[$question['prim']]['status'] == 2) {
                $correct++;
            }
        }
        $info['notAttempted'] = $notattempted;
        $info['Incorrect'] = $incorrect;
        $info['Correct'] = $correct;
        return $info;
    }
    
    /**
     * Returns a summary of how the user has done on the case questions and how many they have correct, incorrect and how many are incomplete
     * @return array Returns a summary of how the user has done on the case questions and how many they have correct, incorrect and how many are incomplete as an array of numbers
     */
    public function userCaseInformation()
    {
        $originalWhere = $this->where;
        $this->where = $this->whereCase;
        $caseInfo = $this->userTestInformation();
        $this->where = $originalWhere;
        return $caseInfo;
    }
}
