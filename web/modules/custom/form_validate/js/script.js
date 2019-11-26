function SumQuart(now) {
  let id = now.id;
  let array_key = id.split('.');
  let table = array_key[0];
  let rows = array_key[1];
  let mounth = array_key[2];
  // if(document.getElementById(id).value === '0'){
  //   document.getElementById(id).value = '';
  // }
  if ((mounth === '1') || (mounth === '2') || (mounth === '3')) {
    let va = 0;
    for (let i = 1; i <= 3; i++) {
      let val = Number(
          document.getElementById(table + '.' + rows + '.' + i).value);
      va = va+val;
    }
    va = (va + 1) / 3;
    if (va <= 0.4) {
      va = 0;
    }
    else {
      va = va.toFixed(2);
    }
    let sumquart = va;
    document.getElementById('q3' + table + rows).value = sumquart;
  }
  else if ((mounth === '4') || (mounth === '5') || (mounth === '6')) {
    let va = 0;
    for (let i = 4; i <= 6; i++) {
      let val = Number(
          document.getElementById(table + '.' + rows + '.' + i).value);
      va = va + val;
    }
    va = (va + 1) / 3;
    if (va <= 0.4) {
      va = 0;
    }
    else {
      va = va.toFixed(2);
    }
    let sumquart = va;
    document.getElementById('q6' + table + rows).value = sumquart;
  }
  else if ((mounth === '7') || (mounth === '8') || (mounth === '9')) {
    let va = 0;
    for (let i = 7; i <= 9; i++) {
      let val = Number(
          document.getElementById(table + '.' + rows + '.' + i).value);
      va = va + val;
    }
    va = (va + 1) / 3;
    if (va <= 0.4) {
      va = 0;
    }
    else {
      va = va.toFixed(2);
    }
    let sumquart = va;
    document.getElementById('q9' + table + rows).value = sumquart;
  }
  else if ((mounth === '10') || (mounth === '11') || (mounth === '12')) {
    let va = 0;
    for (let i = 10; i <= 12; i++) {
      let val = Number(
          document.getElementById(table + '.' + rows + '.' + i).value);
      va = va + val;
    }
    va = (va + 1) / 3;
    if (va <= 0.4) {
      va = 0;
    }
    else {
      va = va.toFixed(2);
    }
    let sumquart = va;
    document.getElementById('q12' + table + rows).value = sumquart;
  }
  // let va = 0;
  let val = (Number(document.getElementById('q12' + table + rows).value) +
      Number(document.getElementById('q3' + table + rows).value) +
      Number(document.getElementById('q9' + table + rows).value) +
      Number(document.getElementById('q6' + table + rows).value) + 1) / 4;
  if (val <= 0.25) {
    val = 0;
  }
  else {
    val = val.toFixed(2);
  }
  let sumyear = val;
  document.getElementById(table+rows+'ytd').value = sumyear;
}