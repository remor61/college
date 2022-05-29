function showFormVacina(){
  exitEstoque();
  document.getElementById("cadastroVacina").style.display = 'block';

  btnVacina = document.getElementById('btnVacina');
  btnVacina.style.backgroundColor = '#007BFF'
  btnVacina.style.borderColor = '#007BFF'

}

function cancelVacina(){
  document.getElementById("cadastroVacina").style.display = 'none';
  document.getElementById('marca').value = '';
  document.getElementById('lote').value = '';

  btnVacina = document.getElementById('btnVacina');
  btnVacina.style.backgroundColor = '#212529'
  btnVacina.style.borderColor = '#212529'
}

function showTableEstoque(){
  cancelVacina();
  document.getElementById("tableEstoque").style.display = 'block';

  btnEstoque = document.getElementById('btnEstoque');
  btnEstoque.style.backgroundColor = '#007BFF'
  btnEstoque.style.borderColor = '#007BFF'
}

function exitEstoque(){
  document.getElementById("tableEstoque").style.display = 'none';

  btnEstoque = document.getElementById('btnEstoque');
  btnEstoque.style.backgroundColor = '#212529'
  btnEstoque.style.borderColor = '#212529'
}

