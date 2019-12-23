<?php

namespace Drupal\form_validate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FormValidation.
 *
 * @package Drupal\form_validate\Form
 */
class FormValidation extends FormBase {

  /**
   * @var int
   *  Current year.
   */
  public $year;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_validation';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->year = date('Y');
    $num_of_table = $form_state->get('num_of_table');
    if (empty($num_of_table)) {
      $num_of_table = 0;
      $form_state->set('num_of_table', $num_of_table);
    }
    $form['actions']['submit'] = [
      '#type'        => 'submit',
      '#name'        => 'new',
      '#value'       => $this->t('Submit'),
      '#button_type' => 'primary',
    ];
    $form['actions']['newtable'] = [
      '#type'   => 'submit',
      '#value'  => $this->t('Add Table'),
      '#submit' => ['::addTable'],
    ];
    $mounth['year'] = 'Year';
    for ($i = 1; $i <= 12; $i++) {
      $mounth[$i] = \Drupal::service('date.formatter')->format(mktime(1, 0, 0,
        $i, 5), 'custom', 'F');
      if ($i % 3 == 0) {
        $mounth['q' . $i] = 'Q' . $i / 3;
      }
    }
    $mounth['ytd'] = 'YTD';

    for ($y = 0; $y <= $num_of_table; $y++) {
      $form['mounth'][$y] = [
        '#type'   => 'table',
        '#title'  => 'Sample Table',
        '#header' => $mounth,
      ];
      $year = $form_state->get('year');
      if (empty($year[$y])) {
        $year[$y] = date('Y');
        $form_state->set('year', $year);
      }
      $num_of_rows = date('Y') + 1 - $year[$y];
      for ($i = 1; $i <= $num_of_rows; $i++) {
        $year = $this->year - $i + 1;
        foreach ($mounth as $key => $value) {
          if ($_POST[$y . '_' . $i . '_' . $key] != NULL) {
            $value_k = $_POST[$y . '_' . $i . '_' . $key];
          }
          else {
            if ($_POST[$key . $y . $i] != NULL) {
              $value_k = $_POST[$key . $y . $i];
            }
            else {
              $value_k = '';
            }
          }
          if ($key == 'year') {
            $form['mounth'][$y][$i][$key] = [
              '#type'     => 'textfield',
              '#size'     => '4',
              '#value'    => $year,
              '#disabled' => TRUE,
            ];
          }
          elseif (substr($key, 0, 1) == 'q') {
            $form['mounth'][$y][$i][$key] = [
              '#type'       => 'textfield',
              '#size'       => '4',
              '#min'        => 0,
              '#value'      => $value_k,
              '#step'       => 0.001,
              '#attributes' => [
                'readonly' => TRUE,
              ],
              '#id'         => $key . $y . $i,
              '#name'       => $key . $y . $i,
            ];
          }
          elseif ($key == 'ytd') {
            $form['mounth'][$y][$i][$key] = [
              '#type'     => 'textfield',
              '#size'     => '4',
              '#value'    => $value_k,
              '#step'     => 0.001,
              '#min'      => 0,
              '#attributes' => [
                'readonly' => TRUE,
              ],
              '#name'     => $y . '.' . $i . '.' . $key,
              '#id'       => $y . $i . $key,
            ];
          }
          else {
            $form['mounth'][$y][$i][$key] = [
              '#type'       => 'number',
              '#min'        => 0,
              '#step'     => 0.001,
              '#width'      => 4,
              '#value'      => $value_k,
              '#id'         => $y . '.' . $i . '.' . $key,
              '#name'       => $y . '.' . $i . '.' . $key,
            ];
          }
        }
        $form['mounth'][$y]['actions']['newrow'] = [
          '#type'   => 'submit',
          '#value'  => $this->t('Add Year'),
          '#name'   => 'new_row_to' . $y,
          '#submit' => ['::addRow'],
        ];

      }
      $form['#tree'] = TRUE;
      /*sort, because we must output table after*/
      krsort($form['mounth'][$y]);
    }
    $form['#attached']['library'][] = 'form_validate/form_validate';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_state->set('valid', TRUE);
    $ver = $form_state->getValues();
    $tr_el = $form_state->getTriggeringElement()['#type']=='submit'?:NULL;
    $ver = $ver['mounth'];
//    $ver = array_filter($ver, $this->filterArray($ver));
    foreach ($ver as $k => $v) {
      if (!is_int($k)) {
        unset($ver[$k]);
      }
      foreach ($ver[$k] as $k2 => $v2) {
        if (!is_int($k2)) {
          unset($ver[$k][$k2]);
        }
        foreach ($ver[$k][$k2] as $k3 => $v3) {
          if (!is_int($k3)) {
            unset($ver[$k][$k2][$k3]);
          }
          else {
            if (!empty($v3)) {
              $ver2[$k][$k2][$k3] = $v3;

            }
            else {
              $ver2[$k][$k2]['empty'] = 0;
            }
          }
        }
      }
    }
    if ($_POST['new'] == 'Submit') {
      //      $t = 0;
      //      foreach ($_POST as $key => $value) {
      //        $key_to_value = explode('_', $key);
      //        if ($t != $key_to_value[0]) {
      //          unset($row);
      //          $t = $key_to_value[0];
      //        }
      //        if (is_numeric($key_to_value[2])) {
      //          $arr[] = $value;
      //          $row[$key_to_value[1]] = $arr;
      //          $table[$key_to_value[0]] = $row;
      //          foreach ($arr as $k => $v) {
      //            if ($key_to_value[2] == 12) {
      //              unset($arr);
      //            }
      //          }
      //        }
      //      }
      //      $t = 0;
      //      $rows = 0;
      //      for ($y = 0; $y < count($table); $y++) {
      //        for ($r = 1; $r <= count($table[$y]); $r++) {
      //          if ($t != $y) {
      //            unset($arr1);
      //            unset($row1);
      //            $t = $y;
      //          }
      //          if ($rows != $r) {
      //            unset($arr1);
      //            $rows = $r;
      //          }
      //          foreach ($table[$y][$r] as $key => $val) {
      //            if (is_numeric($val)) {
      //              $arr1[$key] = $val;
      //              $row1[$r] = $arr1;
      //              $table1[$y] = $row1;
      //            }
      //            else {
      //              $arr1['empty'] = 0;
      //              $row1[$r] = $arr1;
      //              $table1[$y] = $row1;
      //            }
      //          }
      //        }
      //      }
      for ($y = 0; $y < count($ver); $y++) {
        $arra = [];
        for ($r = 1; $r <= count($ver[$y]); $r++) {
          krsort($ver[$y][$r]);
          $arra = array_merge($arra, $ver[$y][$r]);
        }
        $ver[$y] = $arra;
        unset($arra);
        for ($i = 0; $i < count($ver[$y]); $i++) {
          if (is_numeric($ver[$y][$i])) {
            $a = $i;
            if (!is_numeric($ver[$y][$i + 1])) {
              for ($i = $a + 1; $i < count($ver[$y]); $i++) {
                if (is_numeric($ver[$y][$i])) {
                  $form_state->set('valid', FALSE);
                  break 2;
                }
              }
            }
          }
        }
      }

      if ($form_state->get('valid')) {
        for ($y = 0; $y < count($ver2); $y++) {
          for ($i = 0; $i < count($ver2[$y]); $i++) {
            if ($ver2[$y + 1] != NULL) {
              $a = array_intersect_key($ver2[$y], $ver2[$y + 1]);
              $b = array_intersect_key($ver2[$y + 1], $ver2[$y]);
              foreach ($a as $ka => $va) {
                foreach ($b as $kb => $vb) {
                  if ($ka == $kb) {
                    $a1 = array_diff_key($va, $vb);
                    $b1 = array_diff_key($vb, $va);
                    if ((!empty($a1)) || (!empty($b1))) {
                      $form_state->set('valid', FALSE);
                      break 4;
                    }
                  }
                }
              }
            }
            /*Output Invalid when table is empty*/
            /*if (count($table1[$y][$i]) == 1) {
            $form_state->set('valid', FALSE);
            }*/
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $valid = $form_state->get('valid');
    $messenger = \Drupal::messenger();
    if ($valid) {
      $messenger->addMessage('Valid', $messenger::TYPE_STATUS);
    }
    else {
      $messenger->addMessage('Invalid', $messenger::TYPE_ERROR);
    }
    $form_state->setRebuild();
  }

  /**
   * Function addRow.
   *  For add row to table.
   *
   * @param  array  $form
   * @param  \Drupal\Core\Form\FormStateInterface  $form_state
   */
  public function addRow(array &$form, FormStateInterface $form_state) {
    $year = $form_state->get('year');
    /*find from what table we get request*/
    foreach ($_POST as $key => $value) {
      if ($value == 'Add Year') {
        $key_name = substr($key, -1, 1);
        /*$key_name now equal number table*/
        break;
      }
    }
    $year[$key_name]--;
    $form_state->set('year', $year);
    /*rebuild table for output new row*/
    $form_state->setRebuild();
  }

  /**
   * Function AddTable.
   *  Add new table.
   *
   * @param  array  $form
   * @param  \Drupal\Core\Form\FormStateInterface  $form_state
   */
  public function addTable(array &$form, FormStateInterface $form_state) {
    $num_of_table = $form_state->get('num_of_table');
    $num_of_table++;
    $form_state->set('num_of_table', $num_of_table);
    /*rebuild form for output new table*/
    $form_state->setRebuild();
  }

}
