<?php

namespace TheoryTest\Tests;

class UserTest extends SetUp
{
    
    /**
     * @covers TheoryTest\Car\User::getUsername
     * @covers TheoryTest\Car\User::getFirstname
     * @covers TheoryTest\Car\User::getUserField
     */
    public function testGetUsername()
    {
        $this->assertFalse($this->user->getUsername());
        $this->assertEquals('Test', $this->user->getUsername(1));
        $this->assertFalse($this->user->getUsername(52));
        $this->assertFalse($this->user->getUsername('Test'));
    }
    
    /**
     * @covers TheoryTest\Car\User::getFirstname
     * @covers TheoryTest\Car\User::getUserField
     */
    public function testGetFirstname()
    {
        $this->assertFalse($this->user->getFirstname());
        $this->assertEquals('Test', $this->user->getFirstname(1));
        $this->assertFalse($this->user->getFirstname(52));
        $this->assertFalse($this->user->getFirstname('Test'));
    }
    
    /**
     * @covers TheoryTest\Car\User::getLastname
     * @covers TheoryTest\Car\User::getUserField
     */
    public function testGetLastname()
    {
        $this->assertFalse($this->user->getLastname());
        $this->assertEquals('User', $this->user->getLastname(1));
        $this->assertFalse($this->user->getLastname(52));
        $this->assertFalse($this->user->getLastname('User'));
    }
    
    /**
     * @covers TheoryTest\Car\User::checkUserAccess
     */
    public function testCheckUserAccess()
    {
        $this->assertTrue($this->user->checkUserAccess());
        $this->assertTrue($this->user->checkUserAccess(3));
        $this->assertTrue($this->user->checkUserAccess(3, 54));
        $this->assertTrue($this->user->checkUserAccess('any', 'info'));
    }
    
    /**
     * @covers TheoryTest\Car\User::getUserSettings
     * @covers TheoryTest\Car\User::setUserSettings
     */
    public function testUserSettings()
    {
        $this->assertEmpty($this->user->getUserSettings());
        $this->assertEmpty($this->user->getUserSettings('info'));
        $this->assertFalse($this->user->setUserSettings('info'));
        $this->assertFalse($this->user->setUserSettings('info', 1));
        $this->assertTrue($this->user->setUserSettings(['current_test' => 2, 'audio' => 'on'], 1));
        $userSettings = $this->user->getUserSettings(1);
        $this->assertNotEmpty($userSettings);
        $this->assertArrayHasKey('current_test', $userSettings);
        $this->assertArrayHasKey('audio', $userSettings);
        $this->assertTrue($this->user->setUserSettings([], 1));
        $this->assertEmpty($this->user->getUserSettings(1));
        $this->assertTrue($this->user->setUserSettings(['current_test' => 1], 1));
        $this->assertFalse($this->user->setUserSettings(['current_test' => 1], 1));
        $this->assertArrayNotHasKey('audio', $this->user->getUserSettings(1));
    }
    
    /**
     * @covers TheoryTest\Car\User::getUsername
     * @covers TheoryTest\Car\User::getFirstname
     * @covers TheoryTest\Car\User::getLastname
     * @covers TheoryTest\Car\User::getUserField
     */
    public function testGetDetailsWhenLoggedIn()
    {
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->user->getUserInfo();
        $this->assertEquals('Test', $this->user->getUsername());
        $this->assertEquals('User', $this->user->getLastname());
    }
}
