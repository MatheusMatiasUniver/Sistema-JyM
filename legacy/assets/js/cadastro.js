new Vue({
    el: '#app-cadastro',
    data: {
        nome: '',
        usuario: '', 
        email: '',  
        senha: '',
        nivel: '',
        mensagemErro: '',
        mensagemSucesso: ''
    },
    methods: {
        async cadastrarUsuario() {
            this.mensagemErro = '';
            this.mensagemSucesso = '';

            try {
                const response = await fetch('backend/registrar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        nome: this.nome,
                        usuario: this.usuario, 
                        email: this.email,     
                        senha: this.senha,
                        nivel: this.nivel
                    })
                });

                const result = await response.json();

                if (result.status === 'sucesso') {
                    this.mensagemSucesso = 'Usuário cadastrado com sucesso!';
                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 1500);
                } else {
                    this.mensagemErro = 'Erro ao cadastrar: ' + result.mensagem;
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                this.mensagemErro = 'Erro de conexão com o servidor. Por favor, tente novamente.';
            }
        }
    }
});