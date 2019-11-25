<?php


namespace Drupal\form_validate;


class GetTableInfo {
  public $table;
  public $table1;
  public $arr;
  public $row;

  function getTable(){
    $t = 0;
    foreach ($_POST as $key => $value) {
      $key_to_value = explode('_', $key);
      if ($t != $key_to_value[0]) {
        unset($this->row);
        $t = $key_to_value[0];
      }
      if (is_numeric($key_to_value[2])) {
        $this->arr[] = $value;
        $this->row[$key_to_value[1]] = $this->arr;
        $this->table[$key_to_value[0]] = $this->row;
        foreach ($this->arr as $k => $v) {
          if ($key_to_value[2] == 12) {
            unset($arr);
          }
        }
      }
    }
    return $this->table;
  }

  function NotEmptyElement($table){
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