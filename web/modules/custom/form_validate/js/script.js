/**
 * @file
 * @type {{attach: Drupal.behaviors.sumQuart.attach}}
 */

/*
 * Adds months and writes values ​​to the desired quarter,
 * adds quarters and writes them to the year.
 */
(function ($, Drupal) {
  Drupal.behaviors.sumQuart = {
    attach: function () {
      $('.form-number').change(function () {
        console.log();
        let id = this.id;
        let array_key = id.split('.');
        let table = array_key[0];
        let rows = array_key[1];
        let mounth = array_key[2];
        if ((mounth === '1') || (mounth === '2') || (mounth === '3')) {
          let va = 0;
          for (let i = 1; i <= 3; i++) {
            let val = Number(
                document.getElementById(table + '.' + rows + '.' + i).value);
            va = va + val;
          }
          let variable = (va + 1) / 3;
          if (va === 0) {
            variable = 0;
          }
          else {
            variable = variable.toFixed(2);
          }
          let sumquart = variable;
          document.getElementById('q3' + table + rows).value = sumquart;
        }
        else if ((mounth === '4') || (mounth === '5') || (mounth === '6')) {
          let va = 0;
          for (let i = 4; i <= 6; i++) {
            let val = Number(
                document.getElementById(table + '.' + rows + '.' + i).value);
            va = va + val;
          }
          let variable = (va + 1) / 3;
          if (va === 0) {
            variable = 0;
          }
          else {
            variable = variable.toFixed(2);
          }
          let sumquart = variable;
          document.getElementById('q6' + table + rows).value = sumquart;
        }
        else if ((mounth === '7') || (mounth === '8') || (mounth === '9')) {
          let va = 0;
          for (let i = 7; i <= 9; i++) {
            let val = Number(
                document.getElementById(table + '.' + rows + '.' + i).value);
            va = va + val;
          }
          let variable = (va + 1) / 3;
          if (va === 0) {
            variable = 0;
          }
          else {
            variable = variable.toFixed(2);
          }
          let sumquart = variable;
          document.getElementById('q9' + table + rows).value = sumquart;
        }
        else if ((mounth === '10') || (mounth === '11') || (mounth === '12')) {
          let va = 0;
          for (let i = 10; i <= 12; i++) {
            let val = Number(
                document.getElementById(table + '.' + rows + '.' + i).value);
            va = va + val;
          }
          let variable = (va + 1) / 3;
          if (va === 0) {
            variable = 0;
          }
          else {
            variable = variable.toFixed(2);
          }
          let sumquart = variable;
          document.getElementById('q12' + table + rows).value = sumquart;
        }
        let val = (Number(document.getElementById('q12' + table + rows).value) +
            Number(document.getElementById('q3' + table + rows).value) +
            Number(document.getElementById('q9' + table + rows).value) +
            Number(document.getElementById('q6' + table + rows).value));
        let variable = (val + 1) / 4;
        if (val === 0) {
          variable = 0;
        }
        else {
          variable = variable.toFixed(2);
        }
        let sumyear = variable;
        document.getElementById(table + rows + 'ytd').value = sumyear;
      })
    }
  };
})(jQuery, Drupal);
