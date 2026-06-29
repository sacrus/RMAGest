/*
*****************************
*     Desenvolvido Por      *
*      Luis Patricio        *
*      www.lplogic.net      *
* Castelo Branco, Portugal  *
*****************************
*/
/**
 * RMA Gest - Funcionalidades e Efeitos JS Dinâmicos
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Focar a barra de pesquisa do RMA automaticamente
    const searchInput = document.getElementById('search-input-field');
    if (searchInput) {
        searchInput.focus();
    }

    // 2. Fazer scroll automático para o fundo de qualquer caixa de chat
    const clientChat = document.getElementById('chat-box-container');
    if (clientChat) {
        clientChat.scrollTop = clientChat.scrollHeight;
    }

    const techChat = document.getElementById('tech-chat-box');
    if (techChat) {
        techChat.scrollTop = techChat.scrollHeight;
    }

    // 3. Efeitos de foco interativos no "Google Search Bar"
    const searchBar = document.querySelector('.search-bar');
    if (searchInput && searchBar) {
        searchInput.addEventListener('focus', () => {
            searchBar.style.transform = 'scale(1.01)';
        });
        searchInput.addEventListener('blur', () => {
            searchBar.style.transform = 'scale(1)';
        });
    }

    // 4. Animação de fade-in suave para as tabelas e cards ao entrar
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 50 * index);
    });
});

