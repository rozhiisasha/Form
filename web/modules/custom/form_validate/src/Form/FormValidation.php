<?php

namespace Drupal\form_validate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FormValidation
 *
 * @package Drupal\form_validate\Form
 */
class FormValidation extends FormBase {

  /**
   * @var
   */
  public $year;

  /**
   * @return string
   */
  public function getFormId() {
    return 'form_validation';
  }

  /**
   * @param  array  $form
   * @param  \Drupal\Core\Form\FormStateInterface  $form_state
   *
   * @return array
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
      '#formtarget'  => '_self',
    ];
    $form['actions']['newtable'] = [
      '#type'   => 'submit',
      '#value'  => $this->t('Add Table'),
      '#submit' => ['::addTable'],
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
              '#attributes' => [
                'readonly' => TRUE,
              ],
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
              '#attributes' => [
                'readonly' => TRUE,
              ],
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
          '#submit' => ['::addRow'],
        ];

      }
      /*sort, because we must output table after*/
      krsort($form['mounth'][$y]);
    }
    $form['#attached']['library'][] = 'form_validate/form_validate';
    return $form;
  }

  /**
   * Function AddRow.
   *
   *   For add row to table.
   *
   * @param array $form
   *
   *   Param $form.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   *   Param $form_state.
   */
  public function addRow(array &$form, FormStateInterface $form_state) {
    $year = $form_state->get('year');
    foreach ($_POST as $key => $value) {
      if ($value == 'Add Year') {
        $key_name = substr($key, -1, 1);
        break;
      }
    }
    $year[$key_name]--;
    $form_state->set('year', $year);
    $form_state->setRebuild();
  }

  /**
   * Function AddTable.
   *
   *  Add new table.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function addTable(array &$form, FormStateInterface $form_state) {
    $num_of_table = $form_state->get('num_of_table');
    $num_of_table++;
    $form_state->set('num_of_table', $num_of_table);
    $form_state->setRebuild();
  }

  /**
   * @param  array  $form
   * @param  \Drupal\Core\Form\FormStateInterface  $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_state->set('valid', TRUE);
    if ($_POST['new'] == 'Submit') {
      $table = \Drupal::service('form_validate.gettableinfo')->getTable();
      $table_not_empty_el = \Drupal::service('form_validate.gettableinfo')->notEmptyElement($table);
      $value_valid_storag = \Drupal::service('form_validate.validation')
        ->validateMount($table, $table_not_empty_el);
      $form_state->set('valid', $value_valid_storag);
      if ($form_state->get('valid')) {
        $valid = TRUE;
        $value_valid_storage = \Drupal::service('form_validate.validation')->validateTable($valid, $table_not_empty_el);
        $form_state->set('valid', $value_valid_storage);
      }
    }
  }

  /**
   * @param  array  $form
   * @param  \Drupal\Core\Form\FormStateInterface  $form_state
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

}

