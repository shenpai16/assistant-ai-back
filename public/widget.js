(function () {
    const script = document.currentScript;
    const companyId = script.getAttribute("data-company-id");

    if (!companyId) {
        console.error("Widget IA : data-company-id manquant.");
        return;
    }

    // Génération ou récupération du sessionId
    let sessionId = localStorage.getItem("ai_widget_session");
    if (!sessionId) {
        sessionId = crypto.randomUUID();
        localStorage.setItem("ai_widget_session", sessionId);
    }

    // --- Styles du widget ---
    const style = document.createElement("style");
    style.innerHTML = `
        #ai-bubble {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4f46e5;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 999999;
        }

        #ai-chatbox {
            position: fixed;
            bottom: 100px;
            right: 20px;
            width: 350px;
            height: 450px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 999999;
        }

        #ai-chat-header {
            background: #4f46e5;
            color: white;
            padding: 12px;
            font-weight: bold;
        }

        #ai-chat-messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            font-family: sans-serif;
            font-size: 14px;
        }

        .ai-msg {
            background: #f3f4f6;
            padding: 8px 10px;
            border-radius: 8px;
            margin-bottom: 8px;
            max-width: 80%;
        }

        .user-msg {
            background: #4f46e5;
            color: white;
            padding: 8px 10px;
            border-radius: 8px;
            margin-bottom: 8px;
            max-width: 80%;
            margin-left: auto;
        }

        #ai-chat-input {
            display: flex;
            border-top: 1px solid #ddd;
        }

        #ai-input {
            flex: 1;
            border: none;
            padding: 10px;
            outline: none;
        }

        #ai-send {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
        }
    `;
    document.head.appendChild(style);

    // --- Création des éléments ---
    const bubble = document.createElement("div");
    bubble.id = "ai-bubble";
    bubble.innerHTML = "💬";
    document.body.appendChild(bubble);

    const chatbox = document.createElement("div");
    chatbox.id = "ai-chatbox";
    chatbox.innerHTML = `
        <div id="ai-chat-header">Assistant IA</div>
        <div id="ai-chat-messages"></div>
        <div id="ai-chat-input">
            <input id="ai-input" type="text" placeholder="Écrivez un message..." />
            <button id="ai-send">➤</button>
        </div>
    `;
    document.body.appendChild(chatbox);

    const messagesDiv = document.getElementById("ai-chat-messages");
    const input = document.getElementById("ai-input");
    const sendBtn = document.getElementById("ai-send");

    // --- Ouverture / fermeture ---
    bubble.onclick = () => {
        chatbox.style.display = chatbox.style.display === "none" ? "flex" : "none";
    };

    // --- Fonction d'envoi ---
    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        // Affichage du message utilisateur
        addMessage(text, "user");

        input.value = "";

        // Appel API
        const response = await fetch("http://localhost:8000/api/chat", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                companyId,
                sessionId,
                message: text
            })
        });

        const data = await response.json();

        if (data.response) {
            addMessage(data.response, "ai");
        } else {
            addMessage("Erreur : impossible de contacter l'assistant.", "ai");
        }
    }

    // --- Affichage des messages ---
    function addMessage(text, type) {
        const div = document.createElement("div");
        div.className = type === "user" ? "user-msg" : "ai-msg";
        div.textContent = text;
        messagesDiv.appendChild(div);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    // --- Envoi via bouton ---
    sendBtn.onclick = sendMessage;

    // --- Envoi via Entrée ---
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") sendMessage();
    });
})();