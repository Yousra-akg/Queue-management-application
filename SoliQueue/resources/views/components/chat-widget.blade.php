@props(['entretienId' => null])
@php
    $prefix = 'guest';
    if (request()->is('admin*') || request()->is('formateur*')) {
        $prefix = 'staff_';
        if (Auth::guard('web')->check()) {
            $prefix .= Auth::guard('web')->id();
        } else {
            $prefix .= 'guest';
        }
    } else {
        $prefix = 'candidat_';
        if (Auth::guard('candidat')->check()) {
            $prefix .= Auth::guard('candidat')->id();
        } elseif (Auth::guard('web')->check()) {
            $prefix .= 'test_' . Auth::guard('web')->id();
        } else {
            $prefix .= 'guest';
        }
    }
    $storageKey = 'soliqueue_chat_history_' . $prefix;
@endphp
<div x-data="chatbot('{{ $storageKey }}')" class="fixed bottom-6 right-6 z-50">
    <!-- Chat Button -->
    <button @click="toggle()" class="bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-colors duration-300 focus:outline-none flex items-center justify-center">
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Chat Window -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed inset-0 z-[100] sm:absolute sm:inset-auto sm:bottom-16 sm:right-0 sm:w-80 sm:h-[450px] bg-white sm:rounded-lg sm:shadow-2xl flex flex-col overflow-hidden sm:border sm:border-gray-200"
         style="display: none;">
        
        <!-- Header -->
        <div class="bg-blue-600 text-white p-4 font-bold flex justify-between items-center">
            <span>Assistant IA SoliQueue</span>
            <div class="flex items-center gap-3">
                <button @click="clearHistory()" class="text-blue-200 hover:text-white transition-colors" title="Effacer l'historique">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <button @click="toggle()" class="text-blue-200 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 p-4 overflow-y-auto bg-gray-50 flex flex-col gap-3" id="chat-messages">
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'self-end bg-blue-100 text-blue-900 rounded-bl-lg' : 'self-start bg-white text-gray-800 rounded-br-lg border border-gray-200'" 
                     class="max-w-[80%] rounded-t-lg p-3 text-sm shadow-sm relative">
                    <span x-html="msg.content"></span>
                    <div x-show="msg.file" class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                        <span x-text="msg.fileName"></span>
                    </div>
                </div>
            </template>
            <!-- Loading Indicator -->
            <div x-show="loading" class="self-start bg-white border border-gray-200 text-gray-500 rounded-t-lg rounded-br-lg p-3 text-sm shadow-sm flex space-x-2">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white border-t border-gray-200 flex items-center gap-2">
            
            <!-- File Upload (Admin uniquement, simulé par la présence du bouton pour tous ou géré par rôle plus tard) -->
            <label class="cursor-pointer text-gray-400 hover:text-blue-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                <input type="file" @change="handleFile" class="hidden" accept=".xlsx,.xls,.csv" x-ref="fileInput">
            </label>

            <input type="text" 
                   x-model="newMessage" 
                   @keydown.enter="sendMessage()" 
                   placeholder="Posez votre question..." 
                   class="flex-1 bg-gray-100 border-transparent focus:bg-white focus:ring-0 focus:border-blue-600 text-sm rounded-full py-2 px-4 outline-none"
                   :disabled="loading">
            
            <button @click="sendMessage()" :disabled="loading || (newMessage.trim() === '' && !selectedFile)" class="text-blue-600 hover:text-blue-800 disabled:opacity-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform rotate-90" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>
            </button>
        </div>
        <div x-show="selectedFile" class="px-3 pb-2 text-xs text-gray-500 bg-white flex items-center justify-between">
            <span class="truncate">Fichier: <span x-text="selectedFile?.name"></span></span>
            <button @click="removeFile()" class="text-red-500 hover:text-red-700 ml-2">✖</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatbot', (storageKey) => ({
            storageKey: storageKey,
            entretienId: '{{ $entretienId }}',
            open: false,
            loading: false,
            newMessage: '',
            selectedFile: null,
            messages: [
                { role: 'assistant', content: 'Bonjour ! Je suis l\'Assistant IA de SoliQueue. Comment puis-je vous aider aujourd\'hui ?' }
            ],

            init() {
                const saved = localStorage.getItem(this.storageKey);
                if (saved) {
                    try { this.messages = JSON.parse(saved); } catch(e) {}
                }
            },

            saveHistory() {
                localStorage.setItem(this.storageKey, JSON.stringify(this.messages));
            },

            clearHistory() {
                if(confirm("Voulez-vous supprimer l'historique de discussion ?")) {
                    this.messages = [
                        { role: 'assistant', content: 'Bonjour ! Je suis l\'Assistant IA de SoliQueue. Comment puis-je vous aider aujourd\'hui ?' }
                    ];
                    localStorage.removeItem(this.storageKey);
                }
            },

            toggle() {
                this.open = !this.open;
            },

            handleFile(e) {
                if (e.target.files.length > 0) {
                    this.selectedFile = e.target.files[0];
                }
            },

            removeFile() {
                this.selectedFile = null;
                this.$refs.fileInput.value = '';
            },

            scrollToBottom() {
                setTimeout(() => {
                    const container = document.getElementById('chat-messages');
                    if(container) container.scrollTop = container.scrollHeight;
                }, 100);
            },

            async sendMessage() {
                if (this.newMessage.trim() === '' && !this.selectedFile) return;

                const msgContent = this.newMessage;
                const file = this.selectedFile;
                
                // Obtenir l'historique (10 derniers messages max)
                const historyToSend = JSON.stringify(this.messages.slice(-10).map(m => ({
                    role: m.role,
                    content: m.content
                })));

                // Add user message to UI
                this.messages.push({ 
                    role: 'user', 
                    content: msgContent,
                    file: file ? true : false,
                    fileName: file ? file.name : null
                });
                this.saveHistory();
                
                this.newMessage = '';
                this.selectedFile = null;
                this.$refs.fileInput.value = '';
                this.loading = true;
                this.scrollToBottom();

                try {
                    let formData = new FormData();
                    formData.append('message', msgContent);
                    formData.append('history', historyToSend);
                    if (file) {
                        formData.append('file', file);
                    }
                    if (this.entretienId) {
                        formData.append('entretien_id', this.entretienId);
                    }
                    
                    // Add CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    const response = await fetch('/chat', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token || ''
                        },
                        credentials: 'same-origin',
                        body: formData
                    });

                    if (!response.ok) {
                        if (response.status === 419) throw new Error('CSRF_EXPIRED');
                        throw new Error('Network response was not ok');
                    }
                    
                    const data = await response.json();
                    
                    this.messages.push({ role: 'assistant', content: data.message });
                    this.saveHistory();
                    
                    // Si l'action modifie l'état, on met à jour l'interface dynamiquement
                    if (data.action && data.action !== 'respond_user' && data.action !== 'get_stats') {
                        // 1. Déclenche l'événement local (ex: animation dashboard formateur)
                        window.dispatchEvent(new CustomEvent('ai-action', { detail: { action: data.action } }));
                        
                        // 2. Recharge discrètement la vue principale (soft-reload SPA)
                        setTimeout(async () => {
                            try {
                                const res = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                const html = await res.text();
                                const doc = new DOMParser().parseFromString(html, 'text/html');
                                
                                const currentContent = document.querySelector('#dynamic-view') || document.querySelector('main');
                                const newContent = doc.querySelector('#dynamic-view') || doc.querySelector('main');
                                
                                if (currentContent && newContent) {
                                    currentContent.innerHTML = newContent.innerHTML;
                                }
                            } catch (e) {
                                console.error('Erreur lors du rafraîchissement dynamique:', e);
                            }
                        }, 1500); // Court délai pour lire la réponse du bot
                    }

                } catch (error) {
                    console.error('Erreur:', error);
                    let errorMessage = 'Désolé, une erreur de connexion est survenue.';
                    if (error.message === 'CSRF_EXPIRED') {
                        errorMessage = 'Votre session a expiré. Veuillez rafraîchir la page pour continuer.';
                    }
                    this.messages.push({ role: 'assistant', content: errorMessage });
                } finally {
                    this.loading = false;
                    this.scrollToBottom();
                }
            }
        }));
    });
</script>

