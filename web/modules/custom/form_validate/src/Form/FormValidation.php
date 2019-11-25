<?php

namespace Drupal\form_validate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class FormValidation extends FormBase {

  public $year;

  public function getFormId() {
    return 'form_validation';
  }

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
      '#formtarget'  => '_self',
    ];
    $form['actions']['newtable'] = [
      '#type'   => 'submit',
      '#value'  => $this->t('Add Table'),
      '#submit' => ['::AddTable'],
    ];
    $mounth['year'] = 'Year';
    for ($i = 1; $i <= 12; $i++) {
      $mounth[$i] = date('F', mktime(1, 0, 0, $i, 5));
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
          if ($_POST[$y . '_' . $i . '_' . $key] == NULL) {
            $value_k = '';
          }
          else {
            $value_k = $_POST[$y . '_' . $i . '_' . $key];
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
              '#type'     => 'textfield',
              '#size'     => '4',
              '#min'      => 0,
              '#value'    => $value_k,
              '#step'     => 0.001,
              '#attributes' => ['readonly'=>TRUE,],
              '#id'       => $key . $y . $i,
              '#name'     => $y . '.' . $i . '.' . $key,
            ];
          }
          elseif ($key == 'ytd') {
            $form['mounth'][$y][$i][$key] = [
              '#type'     => 'textfield',
              '#size'     => '4',
              '#value'    => $value_k,
              '#step'     => 0.001,
              '#min'      => 0,
              '#attributes' => ['readonly'=>TRUE,],
              '#name'     => $y . '.' . $i . '.' . $key,
              '#id'       => $y . $i . $key,
            ];
          }
          elseif (($key == 1) || ($key == 2) || ($key == 3)) {
            $form['mounth'][$y][$i][$key] = [
              '#type'       => 'number',
              '#min'        => 0,
              '#width'      => 4,
              '#attributes' => ['onchange' => 'SumQuart(this)'],
              '#value'      => $value_k,
              '#id'         => $y . '.' . $i . '.' . $key,
              '#name'       => $y . '.' . $i . '.' . $key,
            ];
          }
          elseif (($key == 4) || ($key == 5) || ($key == 6)) {
            $form['mounth'][$y][$i][$key] = [
              '#type'       => 'number',
              '#min'        => 0,
              '#width'      => 4,
              '#attributes' => ['onchange' => 'SumQuart(this)'],
              '#id'         => $y . '.' . $i . '.' . $key,
              '#value'      => $value_k,
              '#name'       => $y . '.' . $i . '.' . $key,
            ];
          }
          elseif (($key == 7) || ($key == 8) || ($key == 9)) {
            $form['mounth'][$y][$i][$key] = [
              '#type'       => 'number',
              '#min'        => 0,
              '#width'      => 4,
              '#attributes' => ['onchange' => 'SumQuart(this)'],
              '#id'         => $y . '.' . $i . '.' . $key,
              '#value'      => $value_k,
              '#name'       => $y . '.' . $i . '.' . $key,
            ];
          }
          elseif (($key == 10) || ($key == 11) || ($key == 12)) {
            $form['mounth'][$y][$i][$key] = [
              '#type'       => 'number',
              '#min'        => 0,
              '#width'      => 4,
              '#attributes' => ['onchange' => 'SumQuart(this)'],
              '#id'         => $y . '.' . $i . '.' . $key,
              '#value'      => $value_k,
              '#name'       => $y . '.' . $i . '.' . $key,
            ];
          }
        }
        $form['mounth'][$y]['actions']['newrow'] = [
          '#type'   => 'submit',
          '#value'  => $this->t('Add Year'),
          '#name'   => 'new_row_to' . $y,
          '#submit' => ['::AddRow'],
        ];

      }
      //sort, because we must output table after
      krsort($form['mounth'][$y]);
    }
    $form['#attached']['library'][] = 'form_validate/form_validate';
    //    dd($form);
    return $form;
  }

  /**
   * Function AddRow.
   *  For add row to table.
   *
   * @param  array  $form
   * @param  \Drupal\Core\Form\FormStateInterface  $form_state
   */
  public
  function AddRow(
    array &$form,
    FormStateInterface $form_state
  ) {
    $year = $form_state->get('year');
    //find from what table we get request
    foreach ($_POST as $key => $value) {
      if ($value == 'Add Year') {
        $key_name = substr($key, -1, 1);
        //$key_name now equal number table
        break;
      }
    }
    $year[$key_name]--;
    $form_state->set('year', $year);
    //rebuild table for output new row
    $form_state->setRebuild();
  }

  /**
   * Function AddTable.
   *  Add new table.
   *
   * @param  array  $form
   * @param  \Drupal\Core\Form\FormStateInterface  $form_state
   */
  public
  function AddTable(
    array &$form,
    FormStateInterface $form_state
  ) {
    //    get value
    $num_of_table = $form_state->get('num_of_table');
    $num_of_table++;
    $form_state->set('num_of_table', $num_of_table);
    //rebuild form for output new table
    $form_state->setRebuild();
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_state->set('valid', TRUE);
    if ($_POST['new'] == 'Submit') {
      $table = \Drupal::service('form_validate.gettableinfo')->getTable();
      $table_not_empty_el = \Drupal::service('form_validate.gettableinfo')->NotEmptyElement($table);

      for ($y = 0; $y < count($table); $y++) {
        $arra = [];
        for ($r = 1; $r <= count($table[$y]); $r++) {
          krsort($table[$y][$r]);
          $arra = array_merge($arra, $table[$y][$r]);
        }
        $table[$y] = $arra;
        unset($arra);
        for ($i = 0; $i < count($table[$y]); $i++) {
          if (is_numeric($table[$y][$i])) {
            $a = $i;
            if (!is_numeric($table[$y][$i + 1])) {
              for ($i = $a + 1; $i < count($table[$y]); $i++) {
                if (is_numeric($table[$y][$i])) {
                  $form_state->set('valid', FALSE);
                  break 2;
                }
              }
            }
          }
        }
      }
      if ($form_state->get('valid')) {
        $valid = TRUE;
        $value_valid_storage = \Drupal::service('form_validate.validation')->ValidateTable($valid,$table_not_empty_el);
        $form_state->set('valid',$value_valid_storage);
      }
    }
  }

  function submitForm(
    array &$form,
    FormStateInterface $form_state
  ) {
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
}
