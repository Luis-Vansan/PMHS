// Função para validar o formulário de cadastro
function validarCadastro() {
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value;
    const confirmarSenha = document.getElementById('confirmarSenha').value;

    let isValido = true;

    // Validar nome
    if (nome.length < 3) {
        exibirErro('nome', 'O nome deve ter pelo menos 3 caracteres');
        isValido = false;
    } else {
        limparErro('nome');
    }

    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        exibirErro('email', 'Por favor, insira um email válido');
        isValido = false;
    } else {
        limparErro('email');
    }

    // Validar senha
    let errosSenha = [];
    if (senha.length < 8) {
        errosSenha.push('A senha deve ter pelo menos 8 caracteres');
    }
    if (!/[A-Z]/.test(senha) || !/[a-z]/.test(senha) || !/[0-9]/.test(senha)) {
        errosSenha.push('A senha deve conter letras maiúsculas, minúsculas e números');
    }
    if (errosSenha.length > 0) {
        exibirErro('senha', errosSenha.join('<br>'));
        isValido = false;
    } else {
        limparErro('senha');
    }

    // Validar confirmação de senha
    if (senha !== confirmarSenha) {
        exibirErro('confirmarSenha', 'As senhas não coincidem');
        isValido = false;
    } else {
        limparErro('confirmarSenha');
    }

    return isValido;
}

// Função para validar o formulário de login
function validarLogin() {
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value;

    let isValido = true;

    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        exibirErro('email', 'Por favor, insira um email válido');
        isValido = false;
    } else {
        limparErro('email');
    }

    // Validar senha
    if (senha.length < 8) {
        exibirErro('senha', 'A senha deve ter pelo menos 8 caracteres');
        isValido = false;
    } else {
        limparErro('senha');
    }

    return isValido;
}

// Função para exibir mensagens de erro
function exibirErro(campo, mensagem) {
    const elemento = document.getElementById(campo);
    let erroElemento = document.getElementById(campo + 'Erro');
    
    elemento.classList.add('campo-invalido');
    
    if (!erroElemento) {
        erroElemento = document.createElement('div');
        erroElemento.id = campo + 'Erro';
        erroElemento.className = 'erro-mensagem';
        elemento.parentNode.insertBefore(erroElemento, elemento.nextSibling);
    }
    erroElemento.innerHTML = mensagem;
    erroElemento.style.display = 'block';

}


// Função para limpar mensagens de erro
function limparErro(campo) {
    const elemento = document.getElementById(campo);
    const erroElemento = document.getElementById(campo + 'Erro');
    
    elemento.classList.remove('campo-invalido');
    
    if (erroElemento) {
        erroElemento.style.display = 'none';
    }
}

// Adicionar event listeners aos formulários
document.addEventListener('DOMContentLoaded', function() {
    const formCadastro = document.getElementById('formCadastro');
    const formLogin = document.getElementById('formLogin');

    if (formCadastro) {
        formCadastro.addEventListener('submit', function(e) {
            if (!validarCadastro()) {
                e.preventDefault();
            }
        });
    }

    if (formLogin) {
        formLogin.addEventListener('submit', function(e) {
            if (!validarLogin()) {
                e.preventDefault();
            }
        });
    }

     // Seleciona o elemento da mensagem de erro, se ele existir
     const messageElement = document.getElementById('message');

     // Se a mensagem existir, define um timer para ocultá-la após 5 segundos
     if (messageElement) {
        setTimeout(function() {
            messageElement.style.display = 'none'; // Oculta a mensagem
        }, 5000); // 5000 milissegundos = 5 segundos
    }
});
