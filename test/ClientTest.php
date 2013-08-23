<?php
require 'app/client.php';

/**
 * Inherit from class we are testing so we can reveal properties
 * in tests that are not accessible in the real class.
 */
class ClientTestWrapper extends DPSFolioProducer\Client {
    public $config;
    public $download_ticket;
    public $request_server;
    public $session;
    public $ticket;
}

class ClientTest extends PHPUnit_Framework_TestCase {
    private $test_config = array(
        'api_server' => 'https://dpsapi2.acrobat.com',
        'company' => '',
        'consumer_key' => '',
        'consumer_secret' => '',
        'email' => '',
        'password' => '',
        'session_props' => ''
    );

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Missing argument 1 for DPSFolioProducer\Client::__construct()
     */
    public function test_throws_exception_if_config_not_passed() {
        $client = new ClientTestWrapper();
        $this->assertEquals($client->config, array());
    }

    public function test_stores_passed_config() {
        $client = new ClientTestWrapper($this->test_config);
        $this->assertEquals($client->config, $this->test_config);
    }

    public function test_initializes_with_null_download_ticket() {
        $client = new ClientTestWrapper($this->test_config);
        $this->assertEquals($client->download_ticket, null);
    }

    public function test_initializes_with_null_request_server() {
        $client = new ClientTestWrapper($this->test_config);
        $this->assertEquals($client->request_server, null);
    }

    public function test_initializes_with_null_ticket() {
        $client = new ClientTestWrapper($this->test_config);
        $this->assertEquals($client->ticket, null);
    }

    public function test_creates_session_service() {
        $client = new ClientTestWrapper($this->test_config);
        $this->assertEquals(get_class($client->session), 'DPSFolioProducer\SessionService');
    }

    public function test_create_session_only_called_once() {
        $client = new ClientTestWrapper($this->test_config);
        $session = $this->getMock('Session', array('create'));
        $session->expects($this->once())
                ->method('create')
                ->will($this->returnValue(json_decode('{"response": {"ticket": "1234", "server": "http://example.com"}}')));
        $client->session = $session;
        $client->create_session();
        $client->create_session();
    }

    public function test_stores_ticket_after_create_session_call() {
        $client = new ClientTestWrapper($this->test_config);
        $session = $this->getMock('Session', array('create'));
        $session->expects($this->once())
                ->method('create')
                ->will($this->returnValue(json_decode('{"response": {"ticket": "1234", "server": "http://example.com"}}')));
        $client->session = $session;
        $client->create_session();
        $this->assertEquals($client->ticket, '1234');
    }

    public function test_stores_request_server_after_create_session_call() {
        $client = new ClientTestWrapper($this->test_config);
        $session = $this->getMock('Session', array('create'));
        $session->expects($this->once())
                ->method('create')
                ->will($this->returnValue(json_decode('{"response": {"ticket": "1234", "server": "http://example.com"}}')));
        $client->session = $session;
        $client->create_session();
        $this->assertEquals($client->request_server, 'http://example.com');
    }
}
