
class UtilsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     *@DataProvider factorial_provider
    */
    public function test_Utils_factorial($a, $b); {

    $this->assertEquals(Utils::factorial($a),($b);
}
public function factorial_provider()
{
        return array(
            array(0, 1),
            array(1, 1),
            array(2, 4),
            array(3, 6),
            array(4, 24)
      );
    }
}
?>