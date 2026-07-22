<x-default-layout>

<style>
.dots span {
    animation: blink 1.4s infinite;
    font-weight: bold;
    font-size: 18px;
}

.dots span:nth-child(2) { animation-delay: 0.2s; }
.dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes blink {
    0% { opacity: 0.2; transform: translateY(0px); }
    50% { opacity: 1; transform: translateY(-3px); }
    100% { opacity: 0.2; transform: translateY(0px); }
}
</style>

<h3>Chat with {{ $user->name ?? 'User' }}</h3>

<div id="chatBox"
     style="height:400px; overflow:auto; border:1px solid #ddd; padding:10px; background:#f9f9f9;">

    <div id="messagesContainer"></div>

    <!-- typing INSIDE chatBox -->
    <div id="typingRow"
         style="display:none; margin-bottom:10px; text-align:left;">
        <div style="
            display:inline-block;
            padding:10px 14px;
            border-radius:10px;
            background:#fff;
            font-style:italic;
            color:gray;
        ">
           typing
            <span class="dots">
                <span>.</span><span>.</span><span>.</span>
            </span>
        </div>
    </div>

</div>

<input type="text" id="message" class="form-control mt-2" placeholder="Type a message...">

<button onclick="sendMessage()" class="btn btn-primary mt-2">Send</button>

<script>

let conversationId = {{ $conversation->id }};
let myId = {{ auth()->id() }};
let otherUserId = {{ $user->id }};

/* =========================
   SCROLL
========================= */
function scrollDown(){
    let box = document.getElementById("chatBox");
    if (!box) return;
    box.scrollTop = box.scrollHeight;
}

/* =========================
   TYPING SEND
========================= */
document.getElementById('message').addEventListener('input', function () {

    fetch('/chat/typing', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            user_id: myId
        })
    });
});

/* =========================
   TYPING RECEIVE
========================= */
function checkTyping() {

    fetch(`/chat/typing-status/${conversationId}/${otherUserId}`)
        .then(res => res.json())
        .then(data => {

            let typingRow = document.getElementById('typingRow');
            if (!typingRow) return;

            typingRow.style.display = data.typing ? 'block' : 'none';
        });
}

/* =========================
   SEND MESSAGE (NO DUPLICATE FIX)
========================= */
function sendMessage() {

    let input = document.getElementById('message');
    let message = input.value;

    if (message.trim() === '') return;

    input.value = '';

    fetch('/chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            message: message
        })
    })
    .then(res => res.json())
    .then(() => {
        loadMessages();   // 🔥 IMPORTANT: reload only
        scrollDown();
    });
}

/* =========================
   LOAD MESSAGES (SINGLE SOURCE OF TRUTH)
========================= */
function loadMessages() {

    fetch('/chat/messages/{{ $conversation->id }}')
        .then(res => res.json())
        .then(data => {

            let box = document.getElementById("messagesContainer");
            if (!box) return;

            box.innerHTML = "";

            data.forEach(msg => {

                let align = (msg.sender_id == myId) ? 'right' : 'left';
                let bg = (msg.sender_id == myId) ? '#DCF8C6' : '#fff';

                let tick = '';

                if (msg.sender_id == myId) {
                    tick = msg.read_at
                        ? `<br><small style="color:#25D366;">✔✔</small>`
                        : `<br><small style="color:red;">✔✔</small>`;
                }

                box.innerHTML += `
                    <div style="margin-bottom:10px; text-align:${align};">
                        <div style="
                            display:inline-block;
                            padding:10px;
                            border-radius:10px;
                            background:${bg};
                            max-width:60%;
                            word-wrap:break-word;
                        ">
                            ${msg.message}
                            ${tick}
                        </div>
                    </div>
                `;
            });

            
        });
}

/* =========================
   SEEN SYSTEM (SAFE)
========================= */
let lastSeenCall = 0;

function markSeen() {

    if (document.hidden) return;

    let now = Date.now();
    if (now - lastSeenCall < 3000) return;

    lastSeenCall = now;

    fetch('/chat/seen/{{ $conversation->id }}');
}

/* =========================
   INIT
========================= */
loadMessages();
setInterval(loadMessages, 2000);
setInterval(checkTyping, 1000);
setInterval(markSeen, 5000);

document.addEventListener("visibilitychange", function () {
    if (!document.hidden) {
        markSeen();
    }
});

</script>

</x-default-layout>