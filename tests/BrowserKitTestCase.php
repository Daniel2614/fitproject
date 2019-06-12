<?php

use Tests\CreatesApplication;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class BrowserKitTestCase extends BaseTestCase
{
    use CreatesApplication;
    public $baseUrl = 'http://localhost';
}
