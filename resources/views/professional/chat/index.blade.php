@extends('professional.layout.layout')

@section('title', 'Chat with Admin')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('content')
<div class="chat-page min-h-full bg-slate-50 px-4 py-6 text-slate-900 sm:px-6 lg:px-8">
    <div class="chat-shell mx-auto max-w-5xl space-y-6">
        <section class="chat-hero flex flex-col gap-4 rounded-2xl border border-brand-100 bg-gradient-to-br from-white via-brand-50 to-white p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="hero-meta">
                <span class="hero-eyebrow"><i class="ri-message-3-line"></i>Support</span>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-brand-950 sm:text-3xl">Chat with Admin</h1>
                <ul class="hero-breadcrumb">
                    <li><a href="{{ route('professional.dashboard') }}">Home</a></li>
                    <li class="active" aria-current="page">Chat</li>
                </ul>
            </div>
            <span class="status-pill" id="connectionStatus">
                <i class="ri-flashlight-line"></i>
                Connected
            </span>
        </section>

        <section class="chat-card overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-card">
            <header class="chat-card__head border-b border-slate-100 px-5 py-4">
                <h2 class="font-semibold text-brand-950"><i class="ri-chat-voice-line"></i>Conversation</h2>
                <p>Stay connected with the admin team for quick assistance.</p>
            </header>
            <div class="chat-card__body">
                <div class="chat-messages-wrapper min-h-[24rem] max-h-[65vh] overflow-y-auto p-5">
                    <div id="chatMessages">
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="mt-3">Loading chat...</span>
                        </div>
                    </div>
                </div>
                <div class="chat-input-area">
                    <form id="chatMessageForm" class="border-t border-slate-100 p-4" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group flex items-center gap-2">
                            <input type="text" class="form-control form-control-lg min-w-0 flex-1 rounded-xl border border-slate-300 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-brand-500 focus:outline-none" id="messageInput" name="message" placeholder="Type your message to admin..." autocomplete="off">
                            <input type="file" id="fileInput" name="attachment" style="display:none" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif">
                            <button type="button" class="btn-attach" id="attachBtn" title="Attach File">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button type="submit" class="btn-send" id="sendBtn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <div id="filePreview" class="attachment-preview" style="display:none">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-attachment-line"></i>
                                    <span id="fileName"></span>
                                    <span id="fileSize" class="text-muted"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0" id="removeFileBtn">
                                    <i class="ri-close-line"></i> Remove
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentChatId = null;
    let isLoading = false;
    loadChat();
    function loadChat() {
        if (isLoading) return;
        isLoading = true;
        showLoadingState();
        $.ajax({
            url: '{{ route("professional.admin-chat.messages") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.chat) {
                    currentChatId = response.chat.id;
                    displayMessages(response.chat.messages);
                    updateConnectionStatus(true);
                } else {
                    showEmptyState();
                }
            },
            error: function() {
                showErrorState('Failed to load chat. Please refresh the page.');
                updateConnectionStatus(false);
            },
            complete: function() {
                isLoading = false;
            }
        });
    }
    function displayMessages(messages) {
        if (!messages || messages.length === 0) { showEmptyState(); return; }
        messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        const html = messages.map(buildMessageHtml).join('');
        $('#chatMessages').html(html);
        scrollToBottom();
    }
    function buildMessageHtml(message) {
        const isProfessional = message.sender_type === 'App\\Models\\Professional';
        const bubbleClass = isProfessional ? 'message-bubble max-w-[85%] rounded-2xl rounded-br-md bg-brand-700 px-4 py-3 text-white shadow-sm sm:max-w-[70%]' : 'message-bubble max-w-[85%] rounded-2xl rounded-bl-md border border-brand-100 bg-white px-4 py-3 text-slate-800 shadow-sm sm:max-w-[70%]';
        const alignClass = isProfessional ? 'text-right' : 'text-left';
        let attachmentHtml = '';
        if (message.attachments && message.attachments.length > 0) {
            message.attachments.forEach(function(attachment) {
                attachmentHtml += `
                    <div class="attachment-card mt-2 rounded-lg border border-white/30 p-2">
                        <a href="/professional/admin-chat/attachment/${attachment.id}/download" target="_blank">
                            <i class="${attachment.file_icon || 'ri-attachment-line'}"></i>
                            ${attachment.original_name}
                            <small>(${attachment.human_file_size || 'N/A'})</small>
                        </a>
                    </div>`;
            });
        }
        return `
            <div class="message-row flex ${isProfessional ? 'justify-end' : 'justify-start'}">
                <div class="${bubbleClass}">
                    <div class="message-sender ${alignClass}">${isProfessional ? 'You' : (message.sender_name || 'Admin')}</div>
                    ${message.message ? `<div class="message-text whitespace-pre-wrap break-words">${escapeHtml(message.message)}</div>` : ''}
                    ${attachmentHtml}
                    <div class="message-time mt-2 text-xs opacity-75 ${alignClass}">${formatDateTime(message.created_at)}</div>
                </div>
            </div>`;
    }
    $('#chatMessageForm').on('submit', function(e) {
        e.preventDefault();
        const message = $('#messageInput').val().trim();
        const hasFile = $('#fileInput')[0].files.length > 0;
        if (!message && !hasFile) { return; }
        const formData = new FormData(this);
        const sendBtn = $('#sendBtn');
        const originalBtnHtml = sendBtn.html();
        sendBtn.prop('disabled', true).html('<i class="ri-loader-4-line fa-spin"></i>');
        $('#messageInput').prop('disabled', true);
        $.ajax({
            url: '{{ route("professional.admin-chat.send-message") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#messageInput').val('');
                    clearFileSelection();
                    appendMessage(response.message);
                    scrollToBottom();
                    updateConnectionStatus(true);
                } else {
                    toastr.error('Failed to send message');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to send message. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join(', ');
                }
                toastr.error(errorMsg);
                updateConnectionStatus(false);
            },
            complete: function() {
                sendBtn.prop('disabled', false).html(originalBtnHtml);
                $('#messageInput').prop('disabled', false).focus();
            }
        });
    });
    function appendMessage(message) {
        const html = buildMessageHtml(message);
        if ($('#chatMessages').find('.chat-empty-state').length > 0) {
            $('#chatMessages').html(html);
        } else {
            $('#chatMessages').append(html);
        }
    }
    $('#attachBtn').on('click', function() { $('#fileInput').click(); });
    $('#fileInput').on('change', function() { const file = this.files[0]; if (file) showFilePreview(file); });
    $('#removeFileBtn').on('click', function() { clearFileSelection(); });
    function showFilePreview(file) {
        $('#fileName').text(file.name);
        $('#fileSize').text(`(${formatFileSize(file.size)})`);
        $('#filePreview').show();
    }
    function clearFileSelection() {
        $('#fileInput').val('');
        $('#filePreview').hide();
        $('#fileName').text('');
        $('#fileSize').text('');
    }
    function scrollToBottom() {
        const container = $('#chatMessages');
        container.scrollTop(container[0].scrollHeight);
    }
    function showLoadingState() {
        $('#chatMessages').html(`
            <div class="loading-spinner flex min-h-64 flex-col items-center justify-center text-brand-700">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-brand-200 border-t-brand-700" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="mt-3 text-sm text-slate-500">Loading chat...</span>
            </div>`);
    }
    function showEmptyState() {
        $('#chatMessages').html(`
            <div class="chat-empty-state flex min-h-64 flex-col items-center justify-center text-center text-slate-500">
            <i class="ri-message-3-line text-3xl text-brand-500"></i>
            <h5 class="mt-3 font-semibold text-brand-950">No messages yet</h5>
            <p class="mt-1 text-sm">Start a conversation with the admin team</p>
            </div>`);
    }
    function showErrorState(message) {
        $('#chatMessages').html(`
            <div class="chat-empty-state text-danger">
                <i class="ri-error-warning-line"></i>
                <h5>Error</h5>
                <p class="mb-0">${message}</p>
            </div>`);
    }
    function updateConnectionStatus(connected) {
        const statusEl = $('#connectionStatus');
        if (connected) {
            statusEl.removeClass('badge bg-danger').addClass('status-pill').html('<i class="ri-flashlight-line"></i> Connected');
        } else {
            statusEl.removeClass('status-pill').addClass('badge bg-danger').text('Disconnected');
        }
    }
    function formatDateTime(dateTime) {
        const date = new Date(dateTime);
        return date.toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    }
    function formatFileSize(bytes) {
        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let unitIndex = 0;
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        return `${Math.round(size * 100) / 100} ${units[unitIndex]}`;
    }
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    $('#messageInput').focus();
    $('#messageInput').on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            $('#chatMessageForm').submit();
        }
    });
    setInterval(function() {
        if (!isLoading && currentChatId) {
            loadChat();
        }
    }, 30000);
});
</script>
@endsection