<!-- The chat window will be hidden by default -->
       <!-- Implementing the HTML, CSS, and the JS in one file here for ease of access purposes
            because it's my first time implementing AI API -->
<button id="ai-chat-trigger" onclick="toggleChat()" title="Chat with CyberLens Bot">🤖</button>

<div id ="AI-chat-window">

    <div class="chat-header">
        <span>🤖 CyberLens Bot</span>
        <button onclick="toggleChat()" class="close-btn" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.2em;">
            &times;
        </button>
    </div>
<!-- this is for possible future updates (fullscreen UI for the AI Chat)

    <div class="chat-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <button onclick="toggleFullscreen()" class="fs-btn" title="Fullscreen" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.2em;">
                &times;
            </button>
        </div> -->

    <div id = "chat-messages">
        <div class="message bot-message">
           <!-- System Online. Ready to assist you! -->
            Hi there! How can I assist you today?
        </div>
    </div>

    <div class="chat-input-area">
        <input type="text" id="user-input" placeholder="Ask about anything on the cybersecurity topics" onkeypress="handleEnter(event)">
        <button onclick="sendMessage()" id="send-btn">Send</button>
    </div>
</div>

<style>
    #ai-chat-trigger {
        position: fixed;
        bottom: 30px;
        left: 30px;
        width: 60px;
        height: 60px;
        background: #e94560;
        border-radius: 50%;
        border: 2px solid #efc07b;
        color: white;
        font-size: 30px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(233, 69, 96, 0.4);
        z-index: 9998;
        transition: transform 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #ai-chat-trigger:hover {
        transform: scale(1.1);
        background: #ff6b81;
    }

    #AI-chat-window {
        display: none;
        position: fixed;
        bottom: 100px;
        left: 30px;
        width: 350px;
        height: 500px;
        background: #16213e;
        border: 1px solid #efc07b;
        border-radius: 10px;
        z-index: 9999;
        flex-direction: column;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        font-family: "Segoe UI" , sans-serif;
    }
    .chat-header {
        background: #1a1a2e;
        padding: 15px;
        color: #efc07b;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #1f4068;
        border-radius: 10px 10px 0 0;
    }
    .close-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 1.5em;
        line-height: 1;
    }
    #chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        display: flex;
        gap: 10px;
        flex-direction: column;
        background: #16213e;
    }

    #chat-messages::-webkit-scrollbar { width: 8px; }
    #chat-messages::-webkit-scrollbar-thumb { background: #1f4068; border-radius: 4px; }

    .message {
        padding: 10px 15px;
        border-radius: 8px;
        max-width: 85%;
        font-size: 0.9em;
        line-height: 1.4;
        word-wrap: break-word;
    }
    .bot-message {
        background: #0f3460;
        color: #ddd;
        align-self: flex-start;
        border-left: 3px solid #e94560;
    }
    .user-message {
        background: #e94560;
        color: white;
        align-self: flex-end;
    }
    .chat-input-area {
        padding: 15px;
        background: #1a1a2e;
        border-top: 1px solid #1f4068;
        display: flex;
        gap: 10px;
        border-radius: 0 0 10px 10px;
    }
    #user-input {
        flex: 1;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #1f4068;
        background: #0f3460;
        color: white;
        outline: none;
    }
    #send-btn {
        width: auto;
        padding: 0 20px;
        background: #e94560;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        margin-top: 0;
    }
    #send-btn:hover { background: #ff6b81; }
</style>

<script>
    function toggleChat() {
        const chat = document.getElementById('AI-chat-window');
        const btn = document.getElementById('ai-chat-trigger');

        if (chat.style.display === 'flex') {
            chat.style.display = 'none';
            if (btn) btn.style.display = 'flex';
            //btn.style.display = 'flex';
        } else {
            chat.style.display = 'flex';
            if (btn) btn.style.display = 'none';
            setTimeout(() => document.getElementById('user-input')?.focus(), 100);
        }
    }
    function handleEnter(e) {
        if (e.key === 'Enter') sendMessage();
    }
    function sendMessage() {
        const input = document.getElementById('user-input');
        const message = input.value.trim();
        const chatBox = document.getElementById('chat-messages');

        //personal notes for future revision purposes
        //add the user message
        if(!message) return;

        chatBox.innerHTML += `<div class="message user-message">${message}</div>`;
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

        //then add loading indicator
        const loadingId= 'loading-' + Date.now();
        chatBox.innerHTML += `<div id="${loadingId}" class="message bot-message">CyberLens Bot is analyzing...</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;

        //Here it sends to the API in the backend
        fetch('controllers/ai_chat_handler.php', {
            method: 'POST',
            body: JSON.stringify({message: message}),
            headers: {'Content-Type': 'application/json'}
        })
        .then(res => res.json())
        .then(data => {
            //to remove the loading message
            const loadingElement = document.getElementById(loadingId);
            if (loadingElement) loadingElement.remove();

            //here, the AI bot reply comes in
            chatBox.innerHTML += `<div class="message bot-message">${data.reply}</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
            .catch(err => {
                const loadingElement = document.getElementById(loadingId);
                if (loadingElement) loadingElement.innerText = "Error: System Offline.";
                console.error(err);
            });
    }
</script>