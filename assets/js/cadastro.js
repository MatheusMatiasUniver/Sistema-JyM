document.getElementById('cadastroForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const nome = document.getElementById('nome').value;
  const email = document.getElementById('email').value;
  const senha = document.getElementById('senha').value;
  const nivel = document.getElementById('nivel').value;

  const response = await fetch('backend/registrar.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ nome, email, senha, nivel })
  });
  const result = await response.json();
  if (result.status === 'sucesso') {
    alert('Usuário cadastrado com sucesso!');
    window.location.href = 'index.html';
  } else {
    alert('Erro ao cadastrar: ' + result.mensagem);
  }
});