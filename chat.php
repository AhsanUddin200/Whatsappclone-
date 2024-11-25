<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch logged-in user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch all contacts except the logged-in user
$stmt = $pdo->prepare("SELECT id, name, unique_number, profile_picture FROM users WHERE id != ?");
$stmt->execute([$user_id]);
$contacts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            background-color: #eaeaea;
        }
        .contacts-section {
            width: 30%;
            background-color: #075E54;
            color: white;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .current-user {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #128C7E;
            color: white;
        }
        .current-user img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .search-bar {
            padding: 10px;
            background-color: #0b8f70;
        }
        .search-bar input {
            width: 90%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            outline: none;
        }
        .contact-item {
            display: flex;
            align-items: center;
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
            background-color: #075E54;
            transition: background-color 0.3s;
        }
        .contact-item:hover {
            background-color: #128C7E;
        }
        .contact-item img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .contact-details {
            flex: 1;
        }
        .contact-details small {
            display: block;
            font-size: 12px;
            opacity: 0.8;
        }
        .contact-item.hidden {
            display: none;
        }
        .no-results {
            padding: 20px;
            text-align: center;
            color: white;
            font-style: italic;
            display: none;
        }
        /* Rest of the CSS remains the same */
        .chat-section {
            width: 70%;
            display: flex;
            flex-direction: column;
            background-color: #fff;
        }
        .placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #666;
            text-align: center;
        }
        .placeholder img {
            width: 200px;
            margin-bottom: 20px;
        }
        .chat-header {
            background-color: #075E54;
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            display: none;
        }
        .chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #f0f0f0;
            display: none;
        }
        .message {
            padding: 10px;
            border-radius: 10px;
            margin: 5px 0;
            max-width: 70%;
        }
        .sent {
            background-color: #DCF8C6;
            margin-left: auto;
        }
        .received {
            background-color: #fff;
            margin-right: auto;
        }
        .message small {
            display: block;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .typing-indicator {
            font-style: italic;
            padding: 10px;
            display: none;
        }
        .input-area {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-top: 1px solid #ddd;
            background-color: #f0f0f0;
        }
        .input-area button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: #075E54;
            margin-right: 10px;
        }
        .input-area button:hover {
            color: #128C7E;
        }
        .input-area textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            resize: none;
            outline: none;
            font-size: 14px;
            margin-right: 10px;
        }
        .input-area .send-button {
            background-color: #075E54;
            color: white;
            font-size: 16px;
            padding: 8px 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .input-area .send-button:hover {
            background-color: #128C7E;
        }
    </style>
</head>
<body>
    <div class="contacts-section">
        <!-- Logged-in user info -->
        <div class="current-user">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'default_dp.png'); ?>" alt="User DP">
            <div>
                <strong><?php echo htmlspecialchars($user['name']); ?></strong><br>
                <small><?php echo htmlspecialchars($user['unique_number']); ?></small>
            </div>
        </div>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by name or number" oninput="filterContacts()">
        </div>
        <!-- No Results Message -->
        <div class="no-results" id="noResults">
            No contacts found
        </div>
        <!-- Contact List -->
        <div id="contactsList">
            <?php foreach ($contacts as $contact): ?>
                <div class="contact-item" 
                     data-name="<?php echo htmlspecialchars(strtolower($contact['name'])); ?>"
                     data-number="<?php echo htmlspecialchars($contact['unique_number']); ?>"
                     onclick="selectContact(<?php echo $contact['id']; ?>, '<?php echo htmlspecialchars($contact['name']); ?>', '<?php echo htmlspecialchars($contact['profile_picture']); ?>')">
                    <img src="<?php echo htmlspecialchars($contact['profile_picture'] ?: 'default_dp.png'); ?>" alt="Contact DP">
                    <div class="contact-details">
                        <strong><?php echo htmlspecialchars($contact['name']); ?></strong>
                        <small><?php echo htmlspecialchars($contact['unique_number']); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="chat-section">
        <!-- Rest of the HTML remains the same -->
        <div class="placeholder" id="placeholder">
            <img src="https://cdn.prod.website-files.com/63f20c8c5a0b87b753a18231/6501a6a0d4049af8220d69e7_Layer_1%20(1).svg" alt="Placeholder">
            <h2>WhatsApp Web</h2>
            <p>Send and receive messages without keeping your phone online.</p>
            <p>Use WhatsApp on up to 4 linked devices and 1 phone at the same time.</p>
        </div>

        <div class="chat-header" id="chatHeader">
            <img id="chatHeaderImg" src="default_dp.png" alt="Contact DP">
            <div>
                <span id="chatHeaderName">Select a contact to start chatting</span><br>
                <small id="chatHeaderStatus">Last seen...</small>
            </div>
        </div>
        <div class="messages" id="messages"></div>
        <div class="typing-indicator" id="typingIndicator">Typing...</div>
        <div class="input-area" id="inputArea" style="display: none;">
            <button class="emoji-button" onclick="toggleEmojiPicker()">&#128515;</button>
            <div id="emojiPicker" style="display: none;">
                <span onclick="insertEmoji('😀')">😀</span>
                <span onclick="insertEmoji('😂')">😂</span>
                <span onclick="insertEmoji('😍')">😍</span>
                <span onclick="insertEmoji('😢')">😢</span>
                <span onclick="insertEmoji('😎')">😎</span>
            </div>
            <button class="attachment-button" onclick="toggleAttachmentMenu()">&#128206;</button>
            
            <!-- Attachment Menu -->
            <div id="attachmentMenu" style="display: none;">
                <div onclick="sendAttachment('document')">Document</div>
                <div onclick="sendAttachment('camera')">Camera</div>
                <div onclick="sendAttachment('gallery')">Gallery</div>
                <div onclick="sendAttachment('audio')">Audio</div>
                <div onclick="sendAttachment('location')">Location</div>
                <div onclick="sendAttachment('contact')">Contact</div>
            </div>

            <textarea id="messageInput" placeholder="Type a message..." oninput="notifyTyping()"></textarea>
            <button class="send-button" onclick="sendMessage()">&#9658;</button>
        </div>
    </div>

    <script>
        let currentReceiverId = null;

        function filterContacts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const contacts = document.querySelectorAll('.contact-item');
            const noResults = document.getElementById('noResults');
            let hasVisibleContacts = false;

            contacts.forEach(contact => {
                const name = contact.getAttribute('data-name');
                const number = contact.getAttribute('data-number');
                
                // Check if search term matches either name or number
                if (name.includes(searchTerm) || number.includes(searchTerm)) {
                    contact.classList.remove('hidden');
                    hasVisibleContacts = true;
                } else {
                    contact.classList.add('hidden');
                }
            });

            // Show/hide the "No contacts found" message
            noResults.style.display = hasVisibleContacts ? 'none' : 'block';
        }

        // Rest of the JavaScript functions remain the same
        function selectContact(receiverId, name, profilePicture) {
            currentReceiverId = receiverId;

            document.getElementById('placeholder').style.display = 'none';
            document.getElementById('chatHeader').style.display = 'flex';
            document.getElementById('messages').style.display = 'block';
            document.getElementById('inputArea').style.display = 'flex';

            document.getElementById('chatHeaderName').textContent = name;
            document.getElementById('chatHeaderImg').src = profilePicture || 'default_dp.png';

            loadMessages();
        }

        function loadMessages() {
            fetch(`get_messages.php?receiver_id=${currentReceiverId}`)
                .then(response => response.json())
                .then(messages => {
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.innerHTML = '';
                    messages.forEach(msg => {
                        const div = document.createElement('div');
                        div.className = `message ${msg.sender_id == <?php echo $user_id; ?> ? 'sent' : 'received'}`;
                        div.innerHTML = `<p>${msg.message}</p>`;
                        messagesDiv.appendChild(div);
                    });
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                });
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            if (!message || !currentReceiverId) return;

            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ receiver_id: currentReceiverId, message })
            }).then(() => {
                messageInput.value = '';
                loadMessages();
            });
        }

        function notifyTyping() {
            if (!currentReceiverId) return;

            fetch('typing_indicator.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `receiver_id=${currentReceiverId}&typing=true`
            });

            setTimeout(() => {
                fetch('typing_indicator.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `receiver_id=${currentReceiverId}&typing=false`
                });
            }, 3000);
        }

        // Poll for new messages every second
        setInterval(() => {
            if (currentReceiverId) {
                loadMessages();
            }
        }, 1000);

        // Add event listener for Enter key in message input
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        function toggleEmojiPicker() {
            const emojiPicker = document.getElementById('emojiPicker');
            emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
        }

        function insertEmoji(emoji) {
            const messageInput = document.getElementById('messageInput');
            messageInput.value += emoji;
        }

        function toggleAttachmentMenu() {
            const attachmentMenu = document.getElementById('attachmentMenu');
            attachmentMenu.style.display = attachmentMenu.style.display === 'none' ? 'block' : 'none';
        }

        function sendAttachment(type) {
            // Handle the attachment based on the type
            console.log(`Selected attachment type: ${type}`);
            // You can implement the logic to handle each attachment type here
            toggleAttachmentMenu(); // Hide the menu after selection
        }
    </script>
</body>
</html>