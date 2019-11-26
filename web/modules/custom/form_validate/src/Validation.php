<?php

namespace Drupal\form_validate;

/**
 * Class Validation
 *
 * @package Drupal\form_validate
 */
class Validation {

  /**
   * @var
   */
  public $table1;

  /**
   * @var
   */
  public $table;

  /**
   * @var
   */
  public $form_state;

  /**
   * @var
   */
  public $table_not_empty_el;

  /**
   * @param $form_state
   * @param $table_not_empty_el
   *
   * @return bool
   */
  public function validateTable($form_state, $table_not_empty_el) {
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
        /*For empty table*/
        /*if (count($this->table1[$y][$i]) == 1) {
        $form_state = FALSE;
        }*/
      }
    }
    return $form_state;
  }

  /**
   * @param $table
   *
   * @return mixed
   */
  public function validateMount($table) {
    $this->table = $table;
    for ($y = 0; $y < count($this->table); $y++) {
      $arra = [];
      for ($r = 1; $r <= count($this->table[$y]); $r++) {
        krsort($this->table[$y][$r]);
        $arra = array_merge($arra, $this->table[$y][$r]);
      }
      $this->table[$y] = $arra;
      unset($arra);
      for ($i = 0; $i < count($this->table[$y]); $i++) {
        if (is_numeric($this->table[$y][$i])) {
          $a = $i;
          if (!is_numeric($this->table[$y][$i + 1])) {
            for ($i = $a + 1; $i < count($this->table[$y]); $i++) {
              if (is_numeric($this->table[$y][$i])) {
                $this->form_state = FALSE;
                break 2;
              }
            }
          }
        }
        else {
          $this->form_state = TRUE;
        }
      }
    }
    return $this->form_state;
  }

}
