<?php
// customerCustomerTest.php
require_once 'PHPUnit/Autoload.php';
require_once '../app/Mage.php';
class customerCustomerTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        Mage::app('default');
    }
}
?>

<?php
require_once 'PHPUnit/Autoload.php';
require_once '../app/Mage.php';
class customerCustomerTest extends PHPUnit_Framework_TestCase {
    private $local_url_v1 = "http://192.168.1.91/api/soap/?wsdl=1";
    private $local_url_v2 = "http://192.168.1.91/api/v2_soap/?wsdl=1";
    private $api_url_v1;
    private $api_url_v2;
    public function setUp() {
        Mage::app('default');
        $this->setApiUrlV2($this->local_url_v2);
    }
    public function getApiUrlV2() {
        return $this->api_url_v2;
    }
    public function setApiUrlV2($api_url_v2) {
        $this->api_url_v2 = $api_url_v2;
    }
    public function testLogin() {
        $cli = new SoapClient($this->api_url_v2);
        $username = 'mobile';
        $password = 'mobile123';
        $result = $cli->login($username, $password);
        $session_id = isset($result) ? $result : null;
        $this->assertNotNull($session_id);
        return $session_id;
    }
    public function testCoreCustomerList_V2() {
        $session_id = $this->testLogin();
        $cli = new SoapClient($this->api_url_v2);
        $result = $cli->customerCustomerList($session_id);
        $this->assertTrue(is_array($result));
        foreach ($result as $res) {
            $this->assertObjectHasAttribute('customer_id', $res);
        }
    }
}