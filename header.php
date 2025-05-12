<?php
require 'config.php';
require 'helpers.php';
?>

<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuvajmo</title>
    <link rel="stylesheet" href="/cook/css/style.css">
</head>
<body>
    <nav>
        <div class="container">
            <a href="index.php">
                <h1>Kuvajmo</h1>
            </a>
            <div>
                <div class="container">
                    <a href="index.php">Početna</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="recipe_create.php">Dodaj recept</a>
                        <a href="favorites.php">Omiljeni recepti</a> 
                        <a href="profile.php">Moj profil</a>
                        <a href="#" onclick="toggleChat()">Kuvajmo Live</a> <!-- Dodat link za chat -->
                    <?php else: ?>
                        <a href="login.php">Prijava</a>
                        <a href="register.php">Registracija</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Chat prozor -->
    <?php if (isLoggedIn()): ?>
        <div id="chat-window" class="chat-window hidden">
            <div class="chat-header">
                <h3>Kuvajmo Live</h3>
                <button onclick="toggleChat()" class="close-chat">×</button>
            </div>
            <div id="chat-messages" class="chat-messages"></div>
            <form id="chat-form" class="chat-form">
                <input type="text" id="chat-input" placeholder="Unesite poruku..." required>
                <button type="submit">Pošalji</button>
            </form>
        </div>
    <?php endif; ?>

    <script>
    // Funkcija za prikazivanje/sakrivanje chata
    function toggleChat() {
        const chatWindow = document.getElementById('chat-window');
        chatWindow.classList.toggle('hidden');
        if (!chatWindow.classList.contains('hidden')) {
            loadMessages();
            scrollToBottom();
        }
    }

    // Funkcija za učitavanje poruka
    function loadMessages() {
        fetch('/cook/get_messages.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Greška pri učitavanju poruka: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error('Greška u get_messages.php:', data.error);
                    return;
                }
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.innerHTML = '';
                data.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('chat-message');
                    messageDiv.innerHTML = `<span class="chat-name">${msg.name}:</span> ${msg.message} <span class="chat-time">(${msg.created_at})</span>`;
                    chatMessages.appendChild(messageDiv);
                });
                scrollToBottom();
            })
            .catch(error => console.error('Greška pri učitavanju poruka:', error));
    }

    // Funkcija za slanje poruke
    function setupChatForm() {
        const chatForm = document.getElementById('chat-form');
        if (!chatForm) {
            console.error('Chat forma nije pronađena.');
            return;
        }

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageInput = document.getElementById('chat-input');
            const message = messageInput.value.trim();

            if (!message) {
                alert('Poruka ne može biti prazna.');
                return;
            }

            fetch('/cook/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `message=${encodeURIComponent(message)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Greška pri slanju poruke: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    loadMessages();
                } else {
                    alert('Greška pri slanju poruke: ' + (data.error || 'Nepoznata greška'));
                }
            })
            .catch(error => {
                console.error('Greška pri slanju poruke:', error);
                alert('Greška pri slanju poruke: ' + error.message);
            });
        });
    }

    // Skrolovanje na dno chata
    function scrollToBottom() {
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Inicijalizacija chata kada se stranica učita
    document.addEventListener('DOMContentLoaded', function() {
        setupChatForm();
    });

    // Automatsko osvežavanje poruka svakih 5 sekundi
    setInterval(() => {
        if (!document.getElementById('chat-window').classList.contains('hidden')) {
            loadMessages();
        }
    }, 5000);
</script>