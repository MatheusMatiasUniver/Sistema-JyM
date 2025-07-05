document.getElementById('loginForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const email = document.getElementById('email').value;
  const senha = document.getElementById('senha').value;

  try {
    const response = await fetch('backend/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, senha })
    });

    const result = await response.json();

    if (result.status === 'sucesso') {
      window.location.href = result.redirect;
    } else {
      alert('Login falhou: ' + result.mensagem);
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
    alert('Erro de conexão com o servidor.');
  }
});
