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
   * Variable $year.
   *
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
    $this->year = $this->getYear();
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
        $year[$y] = $this->getYear();
        $form_state->set('year', $year);
      }

      $num_of_rows = $this->getYear() + 1 - $year[$y];
      for ($i = 1; $i <= $num_of_rows; $i++) {
        $year = $this->year - $i + 1;
        foreach ($mounth as $key => $value) {
          if (!empty($_POST[$y . '_' . $i . '_' . $key])) {
            $value_k = $_POST[$y . '_' . $i . '_' . $key];
          }
          else {
            if (!empty($_POST[$key . $y . $i])) {
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
              '#type'       => 'textfield',
              '#size'       => '4',
              '#value'      => $value_k,
              '#step'       => 0.001,
              '#min'        => 0,
              '#attributes' => [
                'readonly' => TRUE,
              ],
              '#name'       => $y . '.' . $i . '.' . $key,
              '#id'         => $y . $i . $key,
            ];
          }
          else {
            $form['mounth'][$y][$i][$key] = [
              '#type'  => 'number',
              '#min'   => 0,
              '#step'  => 0.001,
              '#width' => 4,
              '#value' => $value_k,
              '#id'    => $y . '.' . $i . '.' . $key,
              '#name'  => $y . '.' . $i . '.' . $key,
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

      // sort, because we must output table after.
      krsort($form['mounth'][$y]);
    }
    $form['#attached']['library'][] = 'form_validate/form_validate';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $tr_el = $form_state->getTriggeringElement()['#name'] == 'new'
      ?: NULL;
    if ($tr_el) {
      $t = 0;
      $form_state->set('valid', TRUE);
      foreach ($form_state->getUserInput() as $key => $value) {
        // Explode $key from input, it was look: table_row_element.
        $key_to_value = explode('_', $key);
        // Validate number of table.
        if ($t != $key_to_value[0] && $this->isNumeric($key_to_value[0])) {
          unset($row);
          unset($row2);
          $t = $key_to_value[0];
        }
        // Create two array for validation.
        if (!empty($key_to_value[2])) {
          if ($this->isNumeric($key_to_value[2])) {
            $row[] = $value;
            // Ver - array for validate rows.
            $ver[$key_to_value[0]] = $row;
            if (!empty($value)) {
              $arr2[] = $value;
            }
            else {
              $arr2['empty'] = 0;
            }
            $row2[$key_to_value[1]] = $arr2;
            // Ver2 - array fo validate table (contains only !empty variable).
            $ver2[$key_to_value[0]] = $row2;
            if ($key_to_value[2] == 12) {
              unset($arr2);
            }
          }
        }
      }
      if (!empty($ver)) {
        $this->validateRows($ver, $form_state);
      }
      if (!empty($ver2)) {
        if ($form_state->get('valid')) {
          $this->validateTable($ver2, $form_state);
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
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function addRow(array &$form, FormStateInterface $form_state) {
    $year = $form_state->get('year');
    // Find from what table we get request.
    foreach ($_POST as $key => $value) {
      if ($value == 'Add Year') {
        $key_name = substr($key, -1, 1);
        // Key_name now equal number table.
        break;
      }
    }
    $year[$key_name]--;
    $form_state->set('year', $year);
    // Rebuild table for output new row.
    $form_state->setRebuild();
  }

  /**
   * Function AddTable.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function addTable(array &$form, FormStateInterface $form_state) {
    $num_of_table = $form_state->get('num_of_table');
    $num_of_table++;
    $form_state->set('num_of_table', $num_of_table);
    // Rebuild table for output new table.
    $form_state->setRebuild();
  }

  /**
   * Function validateRows.
   *
   * @param  array  $ver
   *   Array fo validation.
   *
   * @param  \Drupal\Core\Form\FormStateInterface  $form_state
   */
  public function validateRows(array $ver, FormStateInterface $form_state) {
    for ($y = 0; $y < count($ver); $y++) {
      for ($i = 0; $i < count($ver[$y]); $i++) {
        // Search first number in table.
        if ($this->isNumeric($ver[$y][$i])) {
          // Remember key.
          $a = $i;
          // If next element not numeric, search from next`s element.
          if (!$this->isNumeric($ver[$y][$i + 1])) {
            for ($i = $a + 1; $i < count($ver[$y]); $i++) {
              if ($this->isNumeric($ver[$y][$i])) {
                $form_state->set('valid', FALSE);
                return;
              }
            }
          }
        }
      }
    }
  }

  /**
   * Function validateTable.
   *
   * @param  array  $ver2
   *   Array fo validation.
   *
   * @param  \Drupal\Core\Form\FormStateInterface  $form_state
   */
  public function validateTable(array $ver2, FormStateInterface $form_state) {
    foreach ($ver2 as $key => $value) {
      if (!empty($ver2[$key + 1])) {
        // Find common rows.
        $b = array_intersect_key($ver2[$key + 1], $ver2[$key]);
        $a = array_intersect_key($ver2[$key], $ver2[$key + 1]);
        for ($i = 1; $i <= count($a); $i++) {
          // In common rows, find is there different key.
          $a1 = array_diff_key($a[$i], $b[$i]);
          $b1 = array_diff_key($b[$i], $a[$i]);
          // If common rows has different key - table non validate.
          if ((!empty($a1)) || (!empty($b1))) {
            $form_state->set('valid', FALSE);
            return;
          }
        }
      }
    }
  }

  /**
   * Function getYear.
   *
   * @return mixed
   *   Return current year.
   */
  public function getYear() {
    $current_date = \Drupal::time()->getCurrentTime();
    $year = \Drupal::service('date.formatter')
      ->format($current_date, 'custom', 'Y');
    return $year;
  }

  /**
   * Function isNumeric.
   *
   * @param  mixed  $var
   *   Variable for validate.
   *
   * @return bool
   *   Return true if $var is numeric.
   */
  public function isNumeric($var) {
    return is_numeric($var);
  }

}
