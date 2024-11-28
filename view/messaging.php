<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging | Rush!</title>
    <link rel="stylesheet" href="../assets/messaging.css">
    <link rel="icon" type="image/x-con" href="../assets/favicon_rush.ico">
</head>
<body style="background-image: url('../assets/signuploginbackground.jpeg')">
    <header class="header">
        <h1>Discover and Experience Love</h1>
    </header>

    <div class="chat-container">
        <div class="users-list">
            <?php 
                include "../db/config.php"; 
                include "../actions/fetch_users.php"; 
            ?>
        </div>
        <div class="chat-box" id="messages">
            <!-- Chat messages will appear here -->
        </div>
        <div class="chat-input">
            <input type="hidden" id="senderId" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" id="receiverId">
            <textarea id="messageInput" placeholder="Type your message"></textarea>
            <button id="sendButton" disabled>Send</button>
        </div>
    </div>

    <script>
        class MessagingSystem {
            constructor() {
                this.chatBox = document.querySelector("#messages");
                this.messageInput = document.querySelector("#messageInput");
                this.sendButton = document.querySelector("#sendButton");
                this.receiverInput = document.querySelector("#receiverId");
                this.userElements = document.querySelectorAll(".user");
                this.lastTimestamp = null;
                this.pollingInterval = null;

                this.initializeEventListeners();
            }

            initializeEventListeners() {
                this.userElements.forEach(user => {
                    user.addEventListener("click", () => this.handleUserSelect(user));
                });

                this.sendButton.addEventListener("click", () => this.sendMessage());

                this.messageInput.addEventListener("keypress", (e) => {
                    if (e.key === "Enter" && !e.shiftKey) {
                        e.preventDefault();
                        this.sendMessage();
                    }
                });
            }

            handleUserSelect(user) {
                const userId = user.dataset.id;
                this.receiverInput.value = userId;

                this.userElements.forEach(u => u.classList.remove("selected"));
                user.classList.add("selected");
                this.sendButton.disabled = false;

                this.chatBox.innerHTML = "";
                this.lastTimestamp = null;
                if (this.pollingInterval) clearInterval(this.pollingInterval);

                this.fetchMessages();
                this.startPolling();
            }

            async fetchMessages() {
                try {
                    const receiverId = this.receiverInput.value;
                    if (!receiverId) return;

                    const url = `fetch_messages.php?receiver_id=${receiverId}${
                        this.lastTimestamp ? `&last_time=${this.lastTimestamp}` : ''
                    }`;

                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Network response was not ok');
                    
                    const data = await response.json();
                    if (data.status === "success") {
                        this.handleNewMessages(data.data);
                    }
                } catch (error) {
                    console.error("Error fetching messages:", error);
                }
            }

            handleNewMessages(messages) {
                if (!messages.length) return;

                this.lastTimestamp = messages[messages.length - 1].sent_at;

                messages.forEach(msg => {
                    const messageDiv = document.createElement("div");
                    messageDiv.className = `message ${msg.sender_id === parseInt(this.senderId.value) ? 'sent' : 'received'}`;
                    
                    messageDiv.innerHTML = `
                        <div class="message-content">
                            <span class="sender">${msg.sender_name}</span>
                            <p>${msg.message}</p>
                            <span class="timestamp">${msg.sent_at}</span>
                        </div>
                    `;
                    this.chatBox.appendChild(messageDiv);
                });

                this.scrollToBottom();
            }

            async sendMessage() {
                const receiverId = this.receiverInput.value;
                const message = this.messageInput.value.trim();

                if (!receiverId || !message) return;

                try {
                    const response = await fetch("send_message.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: new URLSearchParams({
                            receiver_id: receiverId,
                            message: message
                        })
                    });

                    const data = await response.json();
                    if (data.status === "success") {
                        this.messageInput.value = "";
                        await this.fetchMessages();
                    }
                } catch (error) {
                    console.error("Error sending message:", error);
                }
            }

            startPolling() {
                this.pollingInterval = setInterval(() => this.fetchMessages(), 5000);
            }

            scrollToBottom() {
                this.chatBox.scrollTop = this.chatBox.scrollHeight;
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            const messagingSystem = new MessagingSystem();
        });
    </script>
</body>
</html>
