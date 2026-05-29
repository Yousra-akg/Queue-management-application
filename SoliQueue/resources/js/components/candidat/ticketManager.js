export default (targetTimestamp = 0, isAlreadyPresent = false, candidatId = 0, initialQueue = [], checkPresenceUrl = '', queueStatusUrl = '', csrfToken = '') => ({
    targetTimestamp,
    isAlreadyPresent,
    candidatId,
    queue: initialQueue,
    checkPresenceUrl,
    queueStatusUrl,
    csrfToken,
    pollInterval: null,
    timerInterval: null,
    notifications: [],
    showNotifDropdown: false,
    priorityAlert: null,

    get unreadCount() {
        return this.notifications.filter(n => !n.estLu).length;
    },

    // --- ELEMENTS CACHÉS ---
    timerEls: {},
    timerSection: null,
    initialHeader: null,
    notifBanner: null,
    presenceBtn: null,
    presenceHint: null,
    presenceActionArea: null,
    infoFooter: null,
    queueSection: null,
    queueBody: null,
    modal: null,
    modalContent: null,
    confirmBtn: null,
    closeBtn: null,
    otpInputs: [],
    confirmedBadge: null,
    confirmTimeEl: null,

    init() {
        // --- INITIALISATION DES ELEMENTS DOM ---
        this.timerEls = {
            days: document.getElementById('days'),
            hours: document.getElementById('hours'),
            minutes: document.getElementById('minutes'),
            seconds: document.getElementById('seconds')
        };
        this.timerSection = document.getElementById('timer-section');
        this.initialHeader = document.getElementById('initial-header');
        this.notifBanner = document.getElementById('notif-banner');
        this.presenceBtn = document.getElementById('presence-btn');
        this.presenceHint = document.getElementById('presence-hint');
        this.presenceActionArea = document.getElementById('presence-action-area');
        this.infoFooter = document.getElementById('info-footer');
        this.queueSection = document.getElementById('queue-section');
        this.queueBody = document.getElementById('queue-body');
        this.modal = document.getElementById('presence-modal');
        this.modalContent = document.getElementById('modal-content');
        this.confirmBtn = document.getElementById('confirm-presence-btn');
        this.closeBtn = document.getElementById('close-modal-btn');
        this.otpInputs = document.querySelectorAll('.otp-input');
        this.confirmedBadge = document.getElementById('presence-confirmed-badge');
        this.confirmTimeEl = document.getElementById('confirm-time');

        // --- GESTION DU COMPTE A REBOURS ET POLLEUR ---
        if (!this.isAlreadyPresent) {
            const updateTimer = () => {
                const diff = this.targetTimestamp - Date.now();

                if (diff <= 0) {
                    if (this.timerInterval) clearInterval(this.timerInterval);
                    this.switchToLiveState();
                    return;
                }

                if (this.timerEls.days) {
                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);

                    this.timerEls.days.textContent = String(d).padStart(2, '0');
                    this.timerEls.hours.textContent = String(h).padStart(2, '0');
                    this.timerEls.minutes.textContent = String(m).padStart(2, '0');
                    this.timerEls.seconds.textContent = String(s).padStart(2, '0');
                }
            };
            
            updateTimer();
            if (this.targetTimestamp - Date.now() > 0) {
                this.timerInterval = setInterval(updateTimer, 1000);
            }
        } else {
            this.startPolling();
        }

        // --- GESTION OTP / ACTIONS ---
        this.setupOtpListeners();
        this.setupActionListeners();

        // Si déjà présent au chargement
        if (this.isAlreadyPresent && this.queue && this.queue.length > 0) {
            this.renderQueue(this.queue);
            if (this.notifBanner) {
                this.notifBanner.classList.remove('hidden');
            }
        }
    },

    setupOtpListeners() {
        this.otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length > 1) {
                    e.target.value = e.target.value.slice(0, 1);
                }
                if (e.target.value && index < this.otpInputs.length - 1) {
                    this.otpInputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    this.otpInputs[index - 1].focus();
                    this.otpInputs[index - 1].value = '';
                }
                if (e.key === 'Enter') {
                    this.confirmBtn.click();
                }
            });
            input.addEventListener('focus', (e) => {
                e.target.select();
            });
        });
    },

    setupActionListeners() {
        if (this.presenceBtn) {
            this.presenceBtn.addEventListener('click', () => {
                if (!this.presenceBtn.disabled) this.openModal();
            });
        }
        if (this.closeBtn) this.closeBtn.addEventListener('click', () => this.closeModal());
        if (this.confirmBtn) this.confirmBtn.addEventListener('click', () => this.confirmPresence());
    },

    // --- STATE TRANSITIONS ---
    switchToLiveState() {
        if (this.timerSection) {
            this.timerSection.classList.add('opacity-0', '-translate-y-8');
            setTimeout(() => this.timerSection.classList.add('hidden'), 500);
        }
        if (this.initialHeader) {
            this.initialHeader.classList.add('opacity-0', '-translate-y-8');
            setTimeout(() => this.initialHeader.classList.add('hidden'), 500);
        }

        if (this.notifBanner) {
            this.notifBanner.classList.remove('hidden');
        }

        if (this.presenceBtn) {
            this.presenceBtn.disabled = false;
            this.presenceBtn.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed', 'shadow-slate-200/50');
            this.presenceBtn.classList.add('bg-[#1A73E8]', 'text-white', 'hover:bg-blue-700', 'shadow-blue-600/30', 'animate-pulse');
        }
        if (this.presenceHint) {
            this.presenceHint.textContent = "C'est le moment ! Validez votre présence maintenant.";
            this.presenceHint.classList.replace('text-slate-400', 'text-[#1A73E8]');
        }

        if (this.infoFooter) {
            this.infoFooter.classList.replace('bg-blue-50/50', 'bg-green-50');
            this.infoFooter.classList.replace('border-blue-100', 'border-green-100');
        }
        const footerText = document.getElementById('footer-text');
        if (footerText) {
            footerText.textContent = "C'est votre tour bientôt. Restez vigilant !";
            footerText.classList.replace('text-blue-800', 'text-[#34A853]');
        }
    },

    async fetchQueue() {
        try {
            const response = await fetch(this.queueStatusUrl);
            const result = await response.json();
            if (result.success) {
                this.renderQueue(result.queue);
                
                const newNotifs = result.notifications || [];
                
                newNotifs.forEach(notif => {
                    const exists = this.notifications.some(n => n.id === notif.id);
                    if (!exists && (notif.titre.includes("tour") || notif.titre.includes("Terminé"))) {
                        this.priorityAlert = notif;
                    }
                });

                this.notifications = newNotifs;
            }
        } catch (error) {
            console.error("Erreur polling queue:", error);
        }
    },

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (result.success) {
                this.notifications = this.notifications.filter(n => n.id !== notificationId);
            }
        } catch (error) {
            console.error("Erreur lecture notification:", error);
        }
    },

    async dismissPriorityAlert(notificationId) {
        await this.markAsRead(notificationId);
        this.priorityAlert = null;
    },

    startPolling() {
        if (this.pollInterval) clearInterval(this.pollInterval);
        this.fetchQueue();
        this.pollInterval = setInterval(() => this.fetchQueue(), 3000);
    },

    renderQueue(tickets) {
        if (!this.queueBody) return;
        this.queueBody.innerHTML = '';
        let enAttenteCount = 0;

        tickets.forEach((t, index) => {
            let rowClass = 'bg-white';
            let statusBadgeClass = '';
            let statusText = '';
            let isMe = t.candidat_id === this.candidatId;

            if (t.statut === 'en cours') {
                rowClass = 'bg-[#10b981] text-white font-bold shadow-sm z-20';
                statusBadgeClass = 'bg-white/20 border border-white/30 text-white';
                statusText = 'En cours';
            } else if (t.statut === 'en attente') {
                enAttenteCount++;
                if (enAttenteCount <= 3) {
                    rowClass = 'bg-emerald-50/80 text-emerald-800 border-b border-emerald-100';
                    statusBadgeClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                    statusText = 'Suivant';
                } else {
                    rowClass = 'bg-white text-slate-400';
                    statusBadgeClass = 'bg-slate-50 text-slate-400 border border-slate-100';
                    statusText = 'En attente';
                }
            } else if (t.statut === 'terminé' || t.statut === 'terminée') {
                rowClass = 'bg-[#064e3b] text-white font-bold';
                statusBadgeClass = 'bg-white/10 border border-white/30 text-white';
                statusText = 'Terminé';
            }

            if (isMe) {
                rowClass += ' border-l-[6px] border-blue-500 ring-1 ring-blue-100';
            }

            const initials = (t.candidat.prenom[0] + t.candidat.nom[0]).toUpperCase();

            const row = `
                <tr class="${rowClass} transition-all duration-300">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black w-24 text-[11px]">Pos ${index + 1}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-x-4">
                            <div class="size-9 rounded-full ${isMe ? 'bg-blue-100 text-[#1A73E8]' : 'bg-white/20'} flex items-center justify-center font-bold text-xs shadow-inner">
                                ${initials}
                            </div>
                            <div>
                                <p class="font-bold text-xs">${t.candidat.prenom} ${t.candidat.nom}</p>
                                ${isMe ? '<p class="text-[9px] text-[#1A73E8] font-black uppercase">C\'est vous</p>' : ''}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-end">
                        ${statusText ? `<span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest ${statusBadgeClass}">${statusText}</span>` : ''}
                    </td>
                </tr>
            `;
            this.queueBody.innerHTML += row;
        });

        if (this.queueSection) {
            this.queueSection.classList.remove('hidden');
            setTimeout(() => this.queueSection.classList.remove('opacity-0', 'translate-y-6'), 50);
        }
    },

    openModal() {
        if (!this.modal) return;
        this.modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => {
            this.modal.classList.add('opacity-100');
            this.modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        setTimeout(() => this.otpInputs[0].focus(), 100);
    },

    closeModal() {
        if (!this.modal) return;
        this.modal.classList.remove('opacity-100');
        this.modalContent.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            this.modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            this.otpInputs.forEach(input => input.value = '');
        }, 300);
    },

    async confirmPresence() {
        const code = Array.from(this.otpInputs).map(i => i.value).join('');
        if (code.length !== 4) {
            this.otpInputs.forEach(i => i.classList.add('animate-shake', 'border-red-500'));
            setTimeout(() => {
                this.otpInputs.forEach(i => i.classList.remove('animate-shake', 'border-red-500'));
            }, 1000);
            return;
        }

        this.confirmBtn.disabled = true;
        this.confirmBtn.innerHTML = '<span class="inline-block animate-spin border-2 border-white border-t-transparent rounded-full size-4 mr-2"></span>';

        try {
            const response = await fetch(this.checkPresenceUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ code: code })
            });

            const result = await response.json();

            if (result.success) {
                this.closeModal();
                this.switchToConfirmedState(result.time, result.queue);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            this.otpInputs.forEach(i => {
                i.classList.add('border-red-500', 'text-red-600', 'animate-shake');
            });
            this.confirmBtn.innerHTML = 'Erreur';
            this.confirmBtn.classList.remove('bg-green-600', 'hover:bg-green-700', 'shadow-green-600/20');
            this.confirmBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'shadow-red-600/20');

            setTimeout(() => {
                this.otpInputs.forEach(i => {
                    i.classList.remove('border-red-500', 'text-red-600', 'animate-shake');
                    i.value = '';
                });
                this.otpInputs[0].focus();
                this.confirmBtn.disabled = false;
                this.confirmBtn.innerHTML = 'Confirmer';
                this.confirmBtn.classList.remove('bg-red-600', 'hover:bg-red-700', 'shadow-red-600/20');
                this.confirmBtn.classList.add('bg-green-600', 'hover:bg-green-700', 'shadow-green-600/20');
            }, 1000);
        }
    },

    switchToConfirmedState(time, queueData) {
        if (this.confirmTimeEl) this.confirmTimeEl.textContent = time;

        if (this.presenceBtn) {
            this.presenceBtn.classList.add('hidden');
            this.presenceBtn.style.display = 'none';
        }
        if (this.presenceHint) {
            this.presenceHint.classList.add('hidden');
            this.presenceHint.style.display = 'none';
        }
        if (this.confirmedBadge) {
            this.confirmedBadge.classList.remove('hidden');
            this.confirmedBadge.classList.add('inline-flex');
        }

        const globalBanner = document.getElementById('notif-banner');
        const notifText = document.getElementById('notif-text');
        if (globalBanner) {
            globalBanner.classList.remove('hidden');
            if (notifText) {
                notifText.textContent = "C'est le jour de votre entretien ! Bonne chance pour votre passage.";
            }
        }

        const footerText = document.getElementById('footer-text');
        if (footerText) footerText.textContent = "C'est votre tour bientôt. Restez vigilant !";
        if (this.infoFooter) {
            this.infoFooter.classList.replace('bg-blue-50/50', 'bg-green-50');
            this.infoFooter.classList.replace('border-blue-100', 'border-green-100');
        }
        if (footerText) footerText.classList.replace('text-blue-800', 'text-[#34A853]');

        this.renderQueue(queueData);
        this.startPolling();
    }
});
