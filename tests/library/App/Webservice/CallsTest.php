<?php
namespace App\Webservice;

require_once 'PHPUnit/Framework/TestCase.php';

class CallsTest extends \PHPUnit_Framework_TestCase {

    protected $entityManager;

    public function setup() {
        $this->entityManager = \Zend_Registry::get('em');
    }

    public function testRandomQuote()
    {
        $calls = new Calls($this->entityManager);
        $request = new \App_Webservice_Types_Request_RandomQuoteRequest();
        $response = $calls->randomQuote($request);
        $this->assertInstanceOf('App_Webservice_Types_Response_RandomQuoteResponse', $response);
        $this->assertInternalType('string', $response->quote->wording);
        $this->assertInternalType('string', $response->quote->author);
    }

    public function testQuote()
    {
        $calls = new Calls($this->entityManager);
        $request = new \App_Webservice_Types_Request_QuoteRequest();
        $request->id = 1;
        $response = $calls->quote($request);
        $this->assertInstanceOf('App_Webservice_Types_Response_QuoteResponse', $response);
        $this->assertInternalType('string', $response->quote->wording);
        $this->assertInternalType('string', $response->quote->author);
    }

}
