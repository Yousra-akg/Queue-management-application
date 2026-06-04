export default (initialFormateurs = []) => ({
    formateurs: initialFormateurs,
    searchQuery: '',
    showAddModal: false,
    showDeleteModal: false,
    formateurToDelete: null,
    formateurForm: { id: null, nom: '', email: '' },

    get filteredFormateurs() {
        if (this.searchQuery === '') return this.formateurs;
        const lowerCaseSearch = this.searchQuery.toLowerCase();
        return this.formateurs.filter(f => {
            return f.nom.toLowerCase().includes(lowerCaseSearch) ||
                   f.email.toLowerCase().includes(lowerCaseSearch);
        });
    },

    // Pagination
    currentPage: 1,
    itemsPerPage: 10,
    get paginatedFormateurs() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.filteredFormateurs.slice(start, end);
    },
    get totalPages() {
        return Math.ceil(this.filteredFormateurs.length / this.itemsPerPage) || 1;
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
        this.formateurForm = { id: null, nom: '', email: '' };
        this.showAddModal = true;
    },

    editFormateur(f) {
        this.formateurForm = { 
            id: f.id, 
            nom: f.nom, 
            email: f.email
        };
        this.showAddModal = true;
    },

    deleteFormateur(id) {
        this.formateurToDelete = id;
        this.showDeleteModal = true;
    },
    
    confirmDelete() {
        if (!this.formateurToDelete) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/formateurs/${this.formateurToDelete}`;
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
