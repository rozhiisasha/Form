<?php


namespace Drupal\form_validate;

/**
 * Class GetTableInfo
 *
 * @package Drupal\form_validate
 */
class GetTableInfo {

  /**
   * @var
   */
  public $table;

  /**
   * @var
   */
  public $table1;

  /**
   * @return mixed
   */
  public function getTable() {
    $t = 0;
    foreach ($_POST as $key => $value) {
      $key_to_value = explode('_', $key);
      if ($t != $key_to_value[0]) {
        unset($this->row);
        $t = $key_to_value[0];
      }
      if (is_numeric($key_to_value[2])) {
        $arr[] = $value;
        $row[$key_to_value[1]] = $arr;
        $this->table[$key_to_value[0]] = $row;
        foreach ($arr as $k => $v) {
          if ($key_to_value[2] == 12) {
            unset($arr);
          }
        }
      }
    }
    return $this->table;
  }

  /**
   * @param $table
   *
   * @return mixed
   */
  public function notEmptyElement($table) {
    $t = 0;
    $rows = 0;
    $this->table = $table;
    for ($y = 0; $y < count($this->table); $y++) {
      for ($r = 1; $r <= count($this->table[$y]); $r++) {
        if ($t != $y) {
          unset($arr1);
          unset($row1);
          $t = $y;
        }
        if ($rows != $r) {
          unset($arr1);
          $rows = $r;
        }
        foreach ($this->table[$y][$r] as $key => $val) {
          if (is_numeric($val)) {
            $arr1[$key] = $val;
            $row1[$r] = $arr1;
            $this->table1[$y] = $row1;
          }
          else {
            $arr1['empty'] = 0;
            $row1[$r] = $arr1;
            $this->table1[$y] = $row1;
          }
        }
      }
    }
    return $this->table1;
  }

}
