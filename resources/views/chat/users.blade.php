<x-default-layout>

<div>

    <h4 style="margin-bottom:15px;">Messages</h4>

    <div id="chat_users_list" style="border:1px solid #ddd; border-radius:10px; overflow:hidden;">
        <div class="text-center text-muted py-10">Loading...</div>
    </div>

</div>

<script>
(function waitForJQuery() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        setTimeout(waitForJQuery, 100);
        return;
    }
    initChatUsersList(jQuery);
})();

function initChatUsersList($) {

    function renderChatUsersList(data)
    {
        let container = $('#chat_users_list');

        // ✅ Server আগে থেকেই sort করে পাঠায় (recent message top এ), তবু client এও নিশ্চিত করি
        data = data.slice().sort(function(a, b){
            return (b.last_message_time || 0) - (a.last_message_time || 0);
        });

        container.empty();

        if (!data || data.length === 0) {
            container.html('<div class="text-center text-muted py-10">No conversations yet</div>');
            return;
        }

        $.each(data, function(index, user) {

            let bgColor = user.has_new ? '#fff7e6' : 'white';
            let fontWeight = user.has_new ? '600' : 'normal';

            let unreadDot = user.unread > 0
                ? `<span style="display:inline-block; width:8px; height:8px; background:red; border-radius:50%; margin-left:6px;"></span>`
                : '';

            let unseenHtml = '';
            if (user.unread > 0 && user.unseen_messages && user.unseen_messages.length > 0) {

                let messagesHtml = '';
                $.each(user.unseen_messages, function(i, m) {
                    messagesHtml += `<div style="margin-bottom:5px;">${m.message}</div>`;
                });

                unseenHtml = `
                    <div class="unseen-hover-${user.id}" style="
                        display:none;
                        position:absolute;
                        left:70px;
                        top:10px;
                        background:#111;
                        color:#fff;
                        padding:10px;
                        border-radius:8px;
                        font-size:12px;
                        width:220px;
                        z-index:999;
                        max-height:200px;
                        overflow:auto;
                    ">
                        <b style="color:#25D366;">New Messages</b>
                        <hr style="border:0;border-top:1px solid #444;">
                        ${messagesHtml}
                    </div>
                `;
            }

            let rowHtml = `
                <a href="/chat/${user.id}" class="chat-user-link" data-id="${user.id}" style="text-decoration:none; color:#000;">
                    <div class="chat-user-row" style="
                        display:flex;
                        align-items:center;
                        padding:12px;
                        border-bottom:1px solid #eee;
                        transition:0.2s;
                        position:relative;
                        background: ${bgColor};
                        font-weight: ${fontWeight};
                    ">

                        <div style="
                            width:45px;
                            height:45px;
                            border-radius:50%;
                            background:#25D366;
                            color:#fff;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-weight:bold;
                            font-size:16px;
                            margin-right:10px;
                            flex-shrink:0;
                        ">
                            ${user.avatar_letter}
                        </div>

                        <div style="flex:1; position:relative;">

                            <div style="font-weight:600;">
                                ${user.name}
                                ${unreadDot}
                            </div>

                            <small style="
                                color:gray;
                                display:block;
                                max-width:90%;
                                overflow:hidden;
                                text-overflow:ellipsis;
                                white-space:nowrap;
                            ">
                                ${user.last_message}
                            </small>

                            ${unseenHtml}

                        </div>

                    </div>
                </a>
            `;

            container.append(rowHtml);
        });

        bindHoverEvents();

        $('.chat-user-row').off('mouseenter mouseleave').each(function(){
            let originalBg = $(this).css('background-color');
            $(this).on('mouseenter', function(){
                $(this).css('background', '#f5f5f5');
            }).on('mouseleave', function(){
                $(this).css('background', originalBg);
            });
        });
    }

    function bindHoverEvents()
    {
        $('.chat-user-link').each(function(){

            let popup = $(this).find('[class^="unseen-hover-"]');

            if (popup.length === 0) return;

            $(this).off('mouseenter mouseleave').on('mouseenter', function () {
                popup.show();
            }).on('mouseleave', function () {
                popup.hide();
            });

        });
    }

    let lastDataString = '';

    function loadChatUsersList()
    {
        $.ajax({
            url: "{{ route('chat.users.json') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                // ✅ ডেটা আগের মতোই থাকলে re-render না করাই ভালো (unnecessary DOM rebuild avoid করে smooth রাখে)
                let newDataString = JSON.stringify(data);
                if (newDataString === lastDataString) {
                    return;
                }
                lastDataString = newDataString;
                renderChatUsersList(data);
            },
            error: function(xhr) {
                console.log('Chat list load error:', xhr.status, xhr.responseText);
                $('#chat_users_list').html(
                    '<div class="text-center text-danger py-10">Failed to load chat list (Error ' + xhr.status + ')</div>'
                );
            }
        });
    }

    loadChatUsersList();

    setInterval(function(){
        loadChatUsersList();
    }, 3000);
}
</script>

</x-default-layout>