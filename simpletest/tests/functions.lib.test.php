function test_compute_value_zero_when_param_zero() {
  $res = compute_value(0);
  $this->assertIdentical($res,0);
}
