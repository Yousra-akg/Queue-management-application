export default (initialCandidats = []) => ({
    candidats: initialCandidats,
    searchQuery: '',
    showAddCandidateModal: false,
    showDeleteModal: false,
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

    init() {
        this.$watch('searchQuery', () => this.currentPage = 1);
    }
});
