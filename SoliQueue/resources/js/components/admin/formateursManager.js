export default (initialFormateurs = []) => ({
    formateurs: initialFormateurs,
    searchQuery: '',
    showAddModal: false,
    showDeleteModal: false,
    formateurToDelete: null,
    formateurForm: { id: null, nom: '', email: '', specialite: '', codeInterne: '' },

    get filteredFormateurs() {
        if (this.searchQuery === '') return this.formateurs;
        const lowerCaseSearch = this.searchQuery.toLowerCase();
        return this.formateurs.filter(f => {
            return f.user.nom.toLowerCase().includes(lowerCaseSearch) ||
                   f.user.email.toLowerCase().includes(lowerCaseSearch) ||
                   f.specialite.toLowerCase().includes(lowerCaseSearch) ||
                   f.codeInterne.toLowerCase().includes(lowerCaseSearch);
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
        this.formateurForm = { id: null, nom: '', email: '', specialite: '', codeInterne: '' };
        this.showAddModal = true;
    },

    editFormateur(f) {
        this.formateurForm = { 
            id: f.id, 
            nom: f.user.nom, 
            email: f.user.email, 
            specialite: f.specialite, 
            codeInterne: f.codeInterne 
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
