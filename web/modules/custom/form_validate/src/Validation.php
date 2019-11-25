<?php

namespace Drupal\form_validate;

class Validation {

  public $table1;
  public $table;

  public function ValidateTable($form_state, $table_not_empty_el) {
    $this->table1 = $table_not_empty_el;
    for ($y = 0; $y < count($this->table1); $y++) {
      for ($i = 0; $i < count($this->table1[$y]); $i++) {
        if ($this->table1[$y + 1] != NULL) {
          $a = array_intersect_key($this->table1[$y], $this->table1[$y + 1]);
          $b = array_intersect_key($this->table1[$y + 1], $this->table1[$y]);
          foreach ($a as $ka => $va) {
            foreach ($b as $kb => $vb) {
              if ($ka == $kb) {
                $a1 = array_diff_key($va, $vb);
                $b1 = array_diff_key($vb, $va);
                if ((!empty($a1)) || (!empty($b1))) {
                  $form_state = FALSE;
                  break 4;
                }
              }
            }
          }
        }
      }
    }
    return $form_state;
  }

  public function ValidateMounth($form_state,$table){
    $this->table = $table;
    for ($y = 0; $y < count( $this->table); $y++) {
      $arra = [];
      for ($r = 1; $r <= count( $this->table[$y]); $r++) {
        krsort( $this->table[$y][$r]);
        $arra = array_merge($arra,  $this->table[$y][$r]);
      }
      $this->table[$y] = $arra;
      unset($arra);
      for ($i = 0; $i < count( $this->table[$y]); $i++) {
        if (is_numeric( $this->table[$y][$i])) {
          $a = $i;
          if (!is_numeric( $this->table[$y][$i + 1])) {
            for ($i = $a + 1; $i < count( $this->table[$y]); $i++) {
              if (is_numeric( $this->table[$y][$i])) {
                $form_state = FALSE;
                break 2;
              }
            }
          }
        }
      }
    }
    return $form_state;
  }
}