export default (initialCandidats = []) => ({
    candidats: initialCandidats,
    searchQuery: '',
    showAddCandidateModal: false,
    showImportModal: false,
    showDeleteModal: false,
    showDetailsModal: false,
    detailsData: null,
    candidatToDelete: null,
    candidatForm: { id: null, prenom: '', nom: '', cin: '', scoreQCM: '' },

    get filteredCandidats() {
        if (this.searchQuery === '') return this.candidats;
        const lowerCaseSearch = this.searchQuery.toLowerCase();
        return this.candidats.filter(c => {
            return c.nom.toLowerCase().includes(lowerCaseSearch) ||
                   c.prenom.toLowerCase().includes(lowerCaseSearch) ||
                   c.cin.toLowerCase().includes(lowerCaseSearch);
        });
    },

    // Pagination
    currentPage: 1,
    itemsPerPage: 10,
    get paginatedCandidats() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.filteredCandidats.slice(start, end);
    },
    get totalPages() {
        return Math.ceil(this.filteredCandidats.length / this.itemsPerPage) || 1;
    },
    get pages() {
        let pages = [];
        for (let i = 1; i <= this.totalPages; i++) pages.push(i);
        return pages;
    },
    nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
    prevPage() { if (this.currentPage > 1) this.currentPage--; },
    goToPage(p) { this.currentPage = p; },

    openAddModal() {
        this.candidatForm = { id: null, prenom: '', nom: '', cin: '', scoreQCM: '' };
        this.showAddCandidateModal = true;
    },

    editCandidat(candidat) {
        this.candidatForm = { ...candidat };
        this.showAddCandidateModal = true;
    },

    deleteCandidat(id) {
        this.candidatToDelete = id;
        this.showDeleteModal = true;
    },
    
    confirmDelete() {
        if (!this.candidatToDelete) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/candidats/${this.candidatToDelete}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    },

    async showDetails(id) {
        this.detailsData = null;
        this.showDetailsModal = true;
        try {
            const response = await fetch(`/admin/candidats/${id}/details`);
            const data = await response.json();
            if (data.success) {
                this.detailsData = data;
            } else {
                alert(data.message || 'Erreur lors de la récupération des détails.');
                this.showDetailsModal = false;
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur serveur. Veuillez réessayer.');
            this.showDetailsModal = false;
        }
    },

    init() {
        this.$watch('searchQuery', () => this.currentPage = 1);
    },

    formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
    }
});
