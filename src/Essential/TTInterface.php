<?php

namespace TheoryTest\Essential;

interface TTInterface{
    public function createNewTest($test = 1);
    public function createTestReport();
    public function setTime($time);
    public function createQuestionHTML($prim);
    public function addAnswer($answer, $prim);
    public function removeAnswer($answer, $prim);
    public function replaceAnswer($answer, $prim);
    public function flagQuestion($prim);
    public function reviewSection();
    public function reviewOnly($type = 'all');
    public function endTest($time);
    public function startNewTest();
    public function saveProgress();
    
    public function setTestType($type);
    public function getTestType();
    public function setPassmark($mark);
    public function getPassmark();
    
    public function setAudioLocation($location);
    public function getAudioLocation();
}