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
            <input type="hidden" id="senderId" value="<?php echo $_SESSION['user_id']; ?>"> <!-- Current user's ID -->
            <input type="hidden" id="receiverId">
            <textarea id="messageInput" placeholder="Type your message"></textarea>
            <button id="sendButton" disabled>Send</button> <!-- Initially disabled -->
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const userElements = document.querySelectorAll(".user");
            const chatBox = document.querySelector("#messages");
            const receiverInput = document.querySelector("#receiverId");
            const sendButton = document.querySelector("#sendButton");
            const messageInput = document.querySelector("#messageInput");

            sendButton.disabled = true; // Keep send button disabled until a user is selected

            // When a user is clicked
            userElements.forEach(user => {
                user.addEventListener("click", () => {
                    const userId = user.dataset.id;
                    receiverInput.value = userId;

                    // Highlight the selected user
                    document.querySelectorAll(".user").forEach(u => u.classList.remove("selected"));
                    user.classList.add("selected");

                    // Enable send button
                    sendButton.disabled = false;

                    // Fetch messages for the selected user
                    fetchMessages(userId);
                });
            });

            // Fetch messages for a specific user
            function fetchMessages(receiverId) {
                fetch(`fetch_messages.php?receiver_id=${receiverId}`)
                    .then(response => {
                        if (!response.ok) throw new Error("Failed to fetch messages");
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === "success") {
                            const messages = data.data;
                            chatBox.innerHTML = ""; // Clear chat box

                            messages.forEach(msg => {
                                chatBox.innerHTML += `<p><strong>${msg.sender}:</strong> ${msg.text}</p>`;
                            });

                            // Scroll to the bottom of the chat box
                            chatBox.scrollTop = chatBox.scrollHeight;
                        } else {
                            console.error("Error fetching messages:", data.message);
                        }
                    })
                    .catch(error => console.error("Error fetching messages:", error));
            }

            // Send a message
            sendButton.addEventListener("click", () => {
                const receiverId = receiverInput.value.trim();
                const message = messageInput.value.trim();

                if (!receiverId || !message) {
                    alert("Please select a user and type a message before sending.");
                    return;
                }

                fetch("send_message.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({ receiver_id: receiverId, message: message })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        // Display the sent message
                        chatBox.innerHTML += `<p><strong>You:</strong> ${message}</p>`;
                        messageInput.value = ""; // Clear the input field
                        chatBox.scrollTop = chatBox.scrollHeight; // Auto-scroll
                    } else {
                        console.error("Error sending message:", data.message);
                    }
                })
                .catch(error => console.error("Error sending message:", error));
            });

            // Periodic fetching of new messages
            setInterval(() => {
                const receiverId = receiverInput.value.trim();
                if (receiverId) fetchMessages(receiverId);
            }, 5000); // Fetch new messages every 5 seconds
        });
    </script>

</body>
</html>
