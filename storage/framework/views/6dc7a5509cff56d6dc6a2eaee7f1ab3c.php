<?php $__env->startSection('title', 'AI Arsip'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.page-header', [
    'title' => 'AI Arsip',
    'subtitle' => 'Tanya AI tentang arsip dan dokumen Anda.',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card border-0 shadow-sm" style="border-radius: 12px; height: calc(100vh - 200px); display: flex; flex-direction: column;">
    <div class="card-body p-0 d-flex flex-column" style="flex: 1; overflow: hidden;">
        <!-- Chat Container -->
        <div id="chatContainer" class="flex-grow-1 overflow-auto p-4" style="background: #f8fafc; min-height: 400px;">
            <!-- Welcome Message -->
            <div id="welcomeMessage" class="text-center py-5">
                <i class="fas fa-robot fa-4x mb-3" style="color: #d4af37;"></i>
                <h4 style="color: #334155; font-weight: 600;">Selamat Datang di AI Arsip</h4>
                <p class="text-muted">Tanyakan apa saja tentang arsip dan dokumen Anda.</p>
                <div class="mt-4">
                    <span class="badge bg-light text-dark me-2" style="border: 1px solid #e2e8f0;">
                        <i class="fas fa-lightbulb me-1"></i>Contoh: "Cari surat tentang kebijakan tahun 2024"
                    </span>
                </div>
            </div>
            
            <!-- Chat Messages -->
            <div id="chatMessages"></div>
        </div>
        
        <!-- Input Container -->
        <div class="p-4 border-top" style="background: white; border-top: 1px solid #e2e8f0;">
            <div class="input-group" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <textarea 
                    id="messageInput" 
                    class="form-control" 
                    rows="1" 
                    placeholder="Ketik pesan Anda... (Enter untuk kirim, Shift+Enter untuk baris baru)"
                    style="border: none; resize: none; padding: 12px 16px; font-size: 0.95rem; max-height: 120px;"
                ></textarea>
                <button 
                    id="sendMessage" 
                    class="btn" 
                    style="background: linear-gradient(135deg, #d4af37, #aa7c11); border: none; padding: 0 20px;"
                >
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.8rem;">
                <i class="fas fa-info-circle me-1"></i>AI menggunakan OpenAI API.
            </div>
        </div>
    </div>
</div>

<style>
.chat-bubble {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 16px;
    position: relative;
    word-wrap: break-word;
}

.user-bubble {
    background: linear-gradient(135deg, #d4af37, #aa7c11);
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 4px;
}

.ai-bubble {
    background: white;
    color: #334155;
    margin-right: auto;
    border-bottom-left-radius: 4px;
    border: 1px solid #e2e8f0;
}

.chat-bubble strong {
    font-weight: 600;
}

.chat-bubble em {
    font-style: italic;
}

.chat-bubble code {
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
}

.chat-bubble pre {
    background: #1e293b;
    color: #e2e8f0;
    padding: 12px;
    border-radius: 8px;
    overflow-x: auto;
    margin: 8px 0;
}

.chat-bubble ul, .chat-bubble ol {
    margin: 8px 0;
    padding-left: 20px;
}

.reference-section {
    margin-top: 12px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 3px solid #d4af37;
}

.reference-title {
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.reference-item {
    padding: 8px;
    background: white;
    border-radius: 6px;
    margin-bottom: 6px;
    border: 1px solid #e2e8f0;
}

.reference-item:last-child {
    margin-bottom: 0;
}

.typing-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 12px 16px;
    background: white;
    border-radius: 12px;
    border-bottom-left-radius: 4px;
    border: 1px solid #e2e8f0;
    max-width: 80%;
    margin-right: auto;
    margin-bottom: 16px;
}

.typing-dot {
    width: 8px;
    height: 8px;
    background: #d4af37;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-8px);
    }
}

#messageInput:focus {
    outline: none;
    box-shadow: none;
}

#chatContainer::-webkit-scrollbar {
    width: 6px;
}

#chatContainer::-webkit-scrollbar-track {
    background: #f1f5f9;
}

#chatContainer::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#chatContainer::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script>
let conversationHistory = [];
let isProcessing = false;

// Auto-resize textarea
document.getElementById('messageInput').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Handle Enter and Shift+Enter
document.getElementById('messageInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// Send message on button click
document.getElementById('sendMessage').addEventListener('click', sendMessage);

async function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || isProcessing) return;
    
    // Hide welcome message
    document.getElementById('welcomeMessage').style.display = 'none';
    
    // Add user message
    addChatBubble(message, 'user');
    conversationHistory.push({ role: 'user', content: message });
    
    // Clear input
    input.value = '';
    input.style.height = 'auto';
    
    // Show typing indicator
    isProcessing = true;
    showTypingIndicator();
    
    try {
        const response = await fetch('/ai-arsip/tanya', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                pertanyaan: message
            })
        });
        
        const data = await response.json();
        
        // Remove typing indicator
        hideTypingIndicator();
        
        if (data.success) {
            // Add AI response with references
            addAIResponse(data.jawaban, data.sumber_arsip);
            conversationHistory.push({ role: 'assistant', content: data.jawaban });
        } else {
            addChatBubble(data.message, 'ai');
        }
    } catch (error) {
        hideTypingIndicator();
        addChatBubble('Terjadi kesalahan: ' + error.message, 'ai');
    }
    
    isProcessing = false;
}

function addChatBubble(message, type) {
    const container = document.getElementById('chatMessages');
    const bubble = document.createElement('div');
    bubble.className = `chat-bubble ${type}-bubble`;
    bubble.innerHTML = parseMarkdown(message);
    container.appendChild(bubble);
    scrollToBottom();
}

function addAIResponse(message, references) {
    const container = document.getElementById('chatMessages');
    
    // AI message bubble
    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble ai-bubble';
    bubble.innerHTML = parseMarkdown(message);
    container.appendChild(bubble);
    
    // References section
    if (references && references.length > 0) {
        const refSection = document.createElement('div');
        refSection.className = 'reference-section';
        
        const refTitle = document.createElement('div');
        refTitle.className = 'reference-title';
        refTitle.innerHTML = `<i class="fas fa-file-alt me-2"></i>Sumber Arsip (${references.length})`;
        refSection.appendChild(refTitle);
        
        references.forEach((ref, index) => {
            const refItem = document.createElement('div');
            refItem.className = 'reference-item';
            
            let fileButton = '';
            if (ref.url_file) {
                fileButton = `
                    <a href="${ref.url_file}" target="_blank" class="btn btn-sm btn-primary ms-2" style="background: linear-gradient(135deg, #d4af37, #aa7c11); border: none; font-weight: 600; border-radius: 6px; box-shadow: 0 2px 6px rgba(212,175,55,0.25);">
                        <i class="fas fa-file-alt me-1"></i>Buka File
                    </a>
                `;
            }
            
            // Relevance score badge
            const scoreBadge = ref.relevansi_score > 0 
                ? `<span class="badge bg-light text-dark ms-2" style="border: 1px solid #d4af37;">Skor: ${ref.relevansi_score.toFixed(1)}</span>`
                : '';
            
            refItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div style="flex: 1;">
                        <div class="d-flex align-items-center mb-1">
                            <strong>${index + 1}. ${ref.nomor_surat || '—'}</strong>
                            ${scoreBadge}
                        </div>
                        <div style="font-size: 0.9rem;">${ref.nama_arsip || '—'}</div>
                        <div style="font-size: 0.85rem; color: #64748b;">${ref.perihal || '—'}</div>
                    </div>
                    ${fileButton}
                </div>
            `;
            refSection.appendChild(refItem);
        });
        
        container.appendChild(refSection);
    }
    
    scrollToBottom();
}

function showTypingIndicator() {
    const container = document.getElementById('chatMessages');
    const indicator = document.createElement('div');
    indicator.id = 'typingIndicator';
    indicator.className = 'typing-indicator';
    indicator.innerHTML = `
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <span class="ms-2 text-muted" style="font-size: 0.9rem;">AI sedang mengetik...</span>
    `;
    container.appendChild(indicator);
    scrollToBottom();
}

function hideTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) {
        indicator.remove();
    }
}

function scrollToBottom() {
    const container = document.getElementById('chatContainer');
    container.scrollTop = container.scrollHeight;
}

function parseMarkdown(text) {
    if (!text) return '';
    
    // Escape HTML
    let html = text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
    
    // Bold
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    
    // Italic
    html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
    
    // Code inline
    html = html.replace(/`(.*?)`/g, '<code>$1</code>');
    
    // Code blocks
    html = html.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');
    
    // Unordered lists
    html = html.replace(/^\- (.+)$/gm, '<li>$1</li>');
    html = html.replace(/(<li>.*<\/li>\n?)+/g, '<ul>$&</ul>');
    
    // Ordered lists
    html = html.replace(/^\d+\. (.+)$/gm, '<li>$1</li>');
    
    // Line breaks
    html = html.replace(/\n/g, '<br>');
    
    return html;
}

// Focus input on load
document.getElementById('messageInput').focus();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/ai-arsip/index.blade.php ENDPATH**/ ?>