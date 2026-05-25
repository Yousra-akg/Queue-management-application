export default (initialSessions = []) => ({
    sessions: initialSessions,
    searchQuery: '',
    statusFilter: 'all',
    showSessionModal: false,
    sessionForm: {
        id: null,
        nom: '',
        dateEntretien: '',
        capaciteMax: '',
        heureDebut: '',
        heureFin: '',
        codePresence: '',
        statut: 'planifiée'
    },

    get filteredSessions() {
        return this.sessions.filter(s => {
            const matchesSearch = s.nom.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchesStatus = this.statusFilter === 'all' || s.statut === this.statusFilter;
            return matchesSearch && matchesStatus;
        });
    },

    // Pagination
    currentPage: 1,
    itemsPerPage: 10,
    get paginatedSessions() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.filteredSessions.slice(start, end);
    },
    get totalPages() {
        return Math.ceil(this.filteredSessions.length / this.itemsPerPage) || 1;
    },
    get pages() {
        let pages = [];
        for (let i = 1; i <= this.totalPages; i++) pages.push(i);
        return pages;
    },
    nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
    prevPage() { if (this.currentPage > 1) this.currentPage--; },
    goToPage(p) { this.currentPage = p; },

    init() {
        this.$watch('searchQuery', () => this.currentPage = 1);
        this.$watch('statusFilter', () => this.currentPage = 1);
    },

    formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
    },

    editSession(session) {
        this.sessionForm = { ...session };
        this.showSessionModal = true;
    },

    async deleteSession(id) {
        if (window.Swal) {
            const result = await window.Swal.fire({
                title: 'Supprimer la session ?',
                text: 'Cette action est irréversible.',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#f3f4f6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            });
            if (!result.isConfirmed) return;
        } else {
            if (!confirm('Supprimer la session ?')) return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/sessions/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    },

    openAddSessionModal() {
        this.resetSessionForm();
        this.generateSessionCode();
        this.showSessionModal = true;
    },

    generateSessionCode() {
        this.sessionForm.codePresence = Math.floor(1000 + Math.random() * 9000).toString();
    },

    resetSessionForm() {
        this.sessionForm = {
            id: null,
            nom: '',
            dateEntretien: '',
            capaciteMax: '60',
            heureDebut: '09:00',
            heureFin: '17:00',
            codePresence: '',
            statut: 'planifiée'
        };
    }
});
