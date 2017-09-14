<?php

namespace TheoryTest\Car;

use DBAL\Database;

class Review{
    protected static $db;
    protected static $user;
    protected $userClone;
    
    public $where = array('carquestion' => 'Y', 'alertcasestudy' => array('IS', 'NULL'));
    
    public $noOfTests = 15;
    public $noOfHPTests = 12;
    public $type = 'CAR';
    
    protected $questionsTable = 'theory_questions_2016';
    protected $DSACatTable = 'theory_dsa_sections';
    protected $progressTable = 'user_progress';
    protected $testProgressTable = 'user_test_progress';
    
    public function __construct(Database $db, $user, $userID = false){
        self::$db = $db;
        self::$user = $user;
        if(is_numeric($userID)){$this->userClone = $userID;}
    }
    
    /**
     * Returns the userID or the mock userID if you wish to look at users progress
     * @return int Returns the UserID or mocked up userID if valid
     */
    public function getUserID(){
        if(is_numeric($this->userClone)){
            return $this->userClone;
        }
        return self::$user->getUserID();
    }
    
    public function getSectionTables(){
        return array(
            array('table' => 'theory_hc_sections', 'name' => 'Highway Code Section', 'section' => 'hc', 'sectionNo' => 'hcsection'),
            array('table' => 'theory_dsa_sections', 'name' => 'DVSA Category', 'section' => 'dsa', 'sectionNo' => 'dsacat'),
            array('table' => 'theory_l2d_sections', 'name' => 'Learn to Drive Lesson', 'section' => 'l2d', 'sectionNo' => 'ldclessonno'),
            'case' => true
        );
    }
    
    /**
     * Returns the current users answers for the current test
     * @return array Returns the current users answers for the current test
     */
    public function getUserAnswers(){
        if(!isset($this->useranswers)){
            $answers = $this->db->select($this->progressTable, array('user_id' => $this->getUserID()), array('progress'));
            $this->useranswers = unserialize(stripslashes($answers['progress']));
        }
        return $this->useranswers;
    }
    
    /*
     * Selects the number of unique test for a given test type
     * @return int Returns the number of unique test
     */
    public function numberOfTests(){
        if(!is_numeric($this->noOfTests)){
            $this->db->query('SELECT DISTINCT `mocktestcarno` FROM `theory_questions` WHERE `mocktestcarno` IS NOT NULL LIMIT 50;');
            $this->noOfTests = $this->db->numRows();
        }
        return $this->noOfTests;
    }
    
    /**
     * Returns the number of tests passed
     * @return int Returns The number of tests the user has passed
     */
    public function testsPassed(){
        return $this->db->count($this->testProgressTable, array('status' => 1, 'user_id' => $this->getUserID()));
    }
    
    /**
     * Returns the number of tests failed
     * @return int Returns The number of tests the user has failed
     */
    public function testsFailed(){
        return $this->db->count($this->testProgressTable, array('status' => 2, 'user_id' => $this->getUserID()));
    }
    
    /**
     * Build the review table for the given categories
     * @param string $table The table which should be used to get the information
     * @param string $tableSecNo The field which that table should be sorted by
     * @param string $title The title that should be given to the table
     * @return string|boolean Returns the table as a HTML string if the information exists else will return false
     */
    public function buildReviewTable($table, $tableSecNo, $title, $section){
        $this->getUserAnswers();
        $categories = $this->db->selectAll($table, '', '*', array('section' => 'ASC'));
        $review = array();
        $review['title'] = $title;
        $review['section'] = $section;
        foreach($categories as $cat){
            $review['ans'][$cat['section']] = $cat;
            $review['ans'][$cat['section']]['notattempted'] = 0;
            $review['ans'][$cat['section']]['incorrect'] = 0;
            $review['ans'][$cat['section']]['correct'] = 0;

            $questions = $this->db->selectAll($this->questionsTable, array_merge(array($tableSecNo => $cat['section']), $this->where), array('prim'));
            $review['ans'][$cat['section']]['numquestions'] = count($questions);
            foreach($questions as $question){
                if($this->useranswers[$question['prim']]['status'] == 0){$review['ans'][$cat['section']]['notattempted']++;}
                elseif($this->useranswers[$question['prim']]['status'] == 1){$review['ans'][$cat['section']]['incorrect']++;}
                elseif($this->useranswers[$question['prim']]['status'] == 2){$review['ans'][$cat['section']]['correct']++;}
            }
            $review['totalquestions'] = $review['totalquestions'] + $review['ans'][$cat['section']]['numquestions'];
            $review['totalcorrect'] = $review['totalcorrect'] + $review['ans'][$cat['section']]['correct'];
            $review['totalnotattempted'] = $review['totalnotattempted'] + $review['ans'][$cat['section']]['notattempted'];
            $review['totalincorrect'] = $review['totalincorrect'] + $review['ans'][$cat['section']]['incorrect'];
        }
        return $review;
    }
    
    /**
     * Build the case study review table
     * @return string|boolean If the case study information exists in the database the table will be returned as a HTML string else will return false
     */
    public function reviewCaseStudy(){
        $this->getUserAnswers();
        $categories = $this->db->selectAll($this->DSACatTable, '', '*', array('section' => 'ASC'));
        foreach($categories as $cat){
            $case[$cat['section']] = $cat;
            foreach($this->db->selectAll($this->questionsTable, array('casestudyno' => $cat['section']), '*', array('csqposition' => 'ASC')) as $num => $question){
                $case[$cat['section']]['q'][$num]['status'] = $this->useranswers[$question['prim']]['status'];
                $case[$cat['section']]['q'][$num]['num'] = ($num + 1);
            }
        }
        return $case;
    }
    
    /**
     * Returns the answers for each of the tests ready to review
     * @return type Returns the answers for each of the tests ready to review
     */
    public function reviewTests(){
        for($i = 1; $i <= $this->numberOfTests(); $i++){
            if($i == $this->numberOfTests() && ($this->type == 'CAR' || $this->type == 'Fleet')){$testID = 'random';}else{$testID = $i;}
            unset($_SESSION['test'.$i]);
            $answers[$testID] = $this->db->select($this->testProgressTable, array('user_id' => $this->getUserID(), 'test_id' => $i, 'status' => array('>=', 1)), array('status', 'totalscore', 'complete'));
        }
        return $answers;
    }
    
    /**
     * Returns a summary of how the user has done on the questions and how many they have correct, incorrect and how many are incomplete
     * @return array Returns a summary of how the user has done on the questions and how many they have correct, incorrect and how many are incomplete as an array of numbers
     */
    public function userTestInformation(){
        $this->getUserAnswers();
        $notattempted = 0;
        $incorrect = 0;
        $correct = 0;

        $questions = $this->db->selectAll($this->questionsTable, $this->where, array('prim'));
        $info['noQuestions'] = $this->db->rowCount();
        foreach($questions as $question){
            if($this->useranswers[$question['prim']]['status'] == 0){$notattempted++;}
            elseif($this->useranswers[$question['prim']]['status'] == 1){$incorrect++;}
            elseif($this->useranswers[$question['prim']]['status'] == 2){$correct++;}
        }
        $info['notAttempted'] = $notattempted;
        $info['Incorrect'] = $incorrect;
        $info['Correct'] = $correct;
        return $info;
    }
}