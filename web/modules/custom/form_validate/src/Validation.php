<?php

namespace Drupal\form_validate;

class Validation {

  public $table1;

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
                /*If need a empty table wil be invalid*/
                $a3 = array_intersect_key($this->table1[$y], $this->table1[$y + 1]);
                $b3 = array_intersect_key($this->table1[$y + 1], $this->table1[$y]);
                if(($a3[$ka]['empty'] === 0 ) && ($b3[$kb]['empty'] === 0)){
                  $form_state = FALSE;
                }
              }
            }
          }
        }
      }
    }
    return $form_state;
  }
}