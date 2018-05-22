<?php

namespace TheoryTest\Tests;

use TheoryTest\Car\DeleteData;

class DeleteDataTest extends SetUp {
    
    protected $delete;
    
    protected function setUp() {
        self::$user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->delete = new DeleteData(self::$db, self::$config, self::$user);
    }
    
    /**
     * @covers TheoryTest\Car\DeleteData::deleteData
     */
    public function testDeleteData(){
        $this->markTestIncomplete();
    }
}
