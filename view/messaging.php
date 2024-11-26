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
            <?php include "../db/config.php"; include "../actions/fetch_users.php"; ?>
        </div>
        <div class="chat-box" id="messages">
            <!-- Chat messages will appear here -->
        </div>
        <div class="chat-input">
            <input type="hidden" id="senderId" value="<!-- Current user's ID -->">
            <input type="hidden" id="receiverId">
            <textarea id="messageInput" placeholder="Type your message"></textarea>
            <button id="sendButton">Send</button>
        </div>
    </div>



    <script>
        document.addEventListener("DOMContentLoaded", () => {
        // Fetch and display chat history when a user is clicked
        const userElements = document.querySelectorAll(".user");
        const chatBox = document.querySelector("#messages");
        const receiverInput = document.querySelector("#receiverId");
        const sendButton = document.querySelector("#sendButton");
        const messageInput = document.querySelector("#messageInput");

        sendButton.disabled = true; // Initially disable send button

        userElements.forEach(user => {
            user.addEventListener("click", () => {
                const userId = user.dataset.id;
                receiverInput.value = userId;

                // Highlight the selected user
                document.querySelectorAll(".user").forEach(u => u.classList.remove("selected"));
                user.classList.add("selected");

                // Enable send button
                sendButton.disabled = false;

                // Fetch chat messages for the selected user
                fetchMessages(userId);
            });
        });

        // Function to fetch messages
        function fetchMessages(receiverId) {
            fetch(`fetch_messages.php?receiver_id=${receiverId}`)
                .then(response => {
                    if (!response.ok) throw new Error("Network response was not ok");
                    return response.json(); // Parse JSON response
                })
                .then(data => {
                    if (data.status === "success") {
                        const messages = data.data;
                        const chatBox = document.querySelector("#messages");
                        chatBox.innerHTML = ""; // Clear chat box
                        messages.forEach(msg => {
                            chatBox.innerHTML += `<p><strong>${msg.sender}:</strong> ${msg.text}</p>`;
                        });
                    } else {
                        console.error("Error fetching messages:", data.message);
                    }
                })
                .catch(error => console.error("Error fetching messages:", error));
        }


        // Handle sending messages
        sendButton.addEventListener("click", () => {
            const receiverId = receiverInput.value.trim();
            const message = messageInput.value.trim();

            if (!receiverId || !message) {
                alert("Please select a user and type a message before sending.");
                return;
            }

            // Send message to send_message.php
            fetch("send_message.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ receiver_id: receiverId, message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    console.log("Message sent successfully");

                    // Display the sent message in the chat box
                    chatBox.innerHTML += `<p><strong>You:</strong> ${message}</p>`;
                    messageInput.value = ""; // Clear input field
                } else {
                    console.error("Error sending message:", data.message);
                }
            })
            .catch(error => console.error("Error sending message:", error));
        });

        // Periodically fetch new messages
        setInterval(() => {
            const receiverId = receiverInput.value.trim();
            if (receiverId) fetchMessages(receiverId);
        }, 2000);
    });
    </script>


</body>
</html>
