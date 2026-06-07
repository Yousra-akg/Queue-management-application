export default (initialTickets = [], entretien = {}, csrfToken = '', reorderRoute = '', canManageQueue = false) => ({
    tickets: initialTickets,
    searchQuery: '',
    entretien: entretien,
    csrfToken: csrfToken,
    reorderRoute: reorderRoute,
    canManageQueue: canManageQueue,

    get waitingCount() {
        return this.tickets.filter(t => t.statut === 'en attente').length;
    },

    get filteredTickets() {
        if (!this.searchQuery) return this.tickets;
        const q = this.searchQuery.toLowerCase();
        return this.tickets.filter(t =>
            t.candidat.nom.toLowerCase().includes(q) ||
            t.candidat.prenom.toLowerCase().includes(q)
        );
    },

    init() {
        if (this.canManageQueue) {
            const SortableClass = window.Sortable;
            const el = document.getElementById('candidate-list');
            if (SortableClass && el) {
                SortableClass.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'bg-blue-50',
                    onEnd: (evt) => {
                        const newOrder = Array.from(el.querySelectorAll('.candidate-card'))
                            .map(card => card.getAttribute('data-id'));
                        this.saveNewOrder(newOrder);
                    }
                });
            }
        }

        window.addEventListener('ai-action', (e) => {
            const action = e.detail.action;
            if (action === 'next_candidate') {
                const current = this.tickets.find(t => t.statut === 'en cours');
                if (current) current.statut = 'terminée';
                const next = this.tickets.find(t => t.statut === 'en attente');
                if (next) next.statut = 'en cours';
                this.tickets = [...this.tickets];
            } else if (action === 'mark_absent') {
                const current = this.tickets.find(t => t.statut === 'en cours');
                if (current) current.statut = 'absent';
                this.tickets = [...this.tickets];
            } else if (action === 'close_entretien') {
                window.location.href = '/formateur/entretiens';
            }
        });
    },

    async updateStatus(ticket, newStatus) {
        const axiosInstance = window.axios;
        const SwalInstance = window.Swal;
        try {
            const response = await axiosInstance.post(`/formateur/status/${ticket.id}`, {
                statut: newStatus,
                _token: this.csrfToken
            });
            if (response.data.success) {
                ticket.statut = newStatus;
                this.tickets = [...this.tickets];
            }
        } catch (err) {
            if (SwalInstance) {
                SwalInstance.fire('Erreur', 'Impossible de mettre à jour le statut.', 'error');
            } else {
                alert('Impossible de mettre à jour le statut.');
            }
        }
    },

    async saveNewOrder(orderIds) {
        const axiosInstance = window.axios;
        const SwalInstance = window.Swal;
        try {
            const response = await axiosInstance.post(this.reorderRoute, {
                order: orderIds,
                _token: this.csrfToken
            });
            if (response.data.success) {
                // Update local tickets order numbers
                orderIds.forEach((id, index) => {
                    const ticket = this.tickets.find(t => t.id == id);
                    if (ticket) ticket.numeroOrdre = index + 1;
                });
                // Resort local array
                this.tickets.sort((a, b) => a.numeroOrdre - b.numeroOrdre);
                this.tickets = [...this.tickets];
            }
        } catch (err) {
            if (SwalInstance) {
                SwalInstance.fire('Erreur', 'Impossible de mettre à jour l\'ordre.', 'error');
            } else {
                alert("Impossible de mettre à jour l'ordre.");
            }
        }
    },

    callNext() {
        const SwalInstance = window.Swal;
        const next = this.tickets.find(t => t.statut === 'en attente');
        if (next) {
            this.updateStatus(next, 'en cours');
        } else {
            if (SwalInstance) {
                SwalInstance.fire({
                    title: 'Plus personne !',
                    text: 'Tous les candidats en attente ont été appelés.',
                    icon: 'info',
                    confirmButtonColor: '#1d4ed8'
                });
            } else {
                alert('Tous les candidats en attente ont été appelés.');
            }
        }
    }
});

