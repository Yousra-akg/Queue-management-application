export default (initialEntretiens = []) => ({
    entretiens: initialEntretiens,
    searchQuery: '',
    statusFilter: 'all',
    showEntretienModal: false,
    entretienForm: {
        id: null,
        nom: '',
        dateEntretien: '',
        capaciteMax: '',
        heureDebut: '',
        heureFin: '',
        codePresence: '',
        statut: 'planifiée'
    },

    get filteredEntretiens() {
        return this.entretiens.filter(s => {
            const matchesSearch = s.nom.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchesStatus = this.statusFilter === 'all' || s.statut === this.statusFilter;
            return matchesSearch && matchesStatus;
        });
    },

    // Pagination
    currentPage: 1,
    itemsPerPage: 10,
    get paginatedEntretiens() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.filteredEntretiens.slice(start, end);
    },
    get totalPages() {
        return Math.ceil(this.filteredEntretiens.length / this.itemsPerPage) || 1;
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

    editEntretien(entretien) {
        this.entretienForm = { ...entretien };
        
        // Map existing formateurs from pivot to affectations array
        if (entretien.formateurs && entretien.formateurs.length > 0) {
            this.entretienForm.affectations = entretien.formateurs.map(f => ({
                formateur_id: f.id,
                salle_id: f.pivot.salle_id
            }));
        } else {
            this.entretienForm.affectations = [{ formateur_id: '', salle_id: '' }];
        }
        
        this.showEntretienModal = true;
    },

    async deleteEntretien(id) {
        if (window.Swal) {
            const result = await window.Swal.fire({
                title: 'Supprimer la entretien ?',
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
            if (!confirm('Supprimer la entretien ?')) return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/entretiens/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    },

    openAddEntretienModal() {
        this.resetEntretienForm();
        this.generateEntretienCode();
        this.showEntretienModal = true;
    },

    generateEntretienCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 4; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        this.entretienForm.codePresence = code;
    },

    resetEntretienForm() {
        this.entretienForm = {
            id: null,
            nom: '',
            dateEntretien: '',
            capaciteMax: '60',
            heureDebut: '09:00',
            heureFin: '17:00',
            codePresence: '',
            statut: 'planifiée',
            affectations: [{ formateur_id: '', salle_id: '' }]
        };
    },

    addAffectation() {
        this.entretienForm.affectations.push({ formateur_id: '', salle_id: '' });
    },

    removeAffectation(index) {
        if (this.entretienForm.affectations.length > 1) {
            this.entretienForm.affectations.splice(index, 1);
        }
    }
});

