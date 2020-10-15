<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\RandomTest;

class RandomTestTest extends SetUp
{
    
    protected $theoryTest;
    
    protected function setUp() : void
    {
        parent::setUp();
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->theoryTest = new RandomTest($this->db, $this->config, $this->template, $this->user);
    }
    
    public function testConnection()
    {
        $this->markTestIncomplete();
    }
}
