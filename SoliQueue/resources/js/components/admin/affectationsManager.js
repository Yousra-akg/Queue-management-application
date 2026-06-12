export default (initialAvailableCandidates = [], initialentretiens = []) => ({
    availableCandidates: initialAvailableCandidates.map(c => ({ ...c, selected: false })),
    entretiens: initialentretiens,
    selectedEntretienId: '',
    searchQuery: '',
    statusFilter: 'all',
    showAddCandidateModal: false,
    showEntretienModal: false,
    isDragging: false,
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

    get selectedEntretien() {
        return this.entretiens.find(s => s.id == this.selectedEntretienId);
    },

    get allSelected() {
        return this.availableCandidates.length > 0 && this.availableCandidates.every(c => c.selected);
    },

    get filteredentretiens() {
        return this.entretiens.filter(s => {
            const matchesSearch = s.nom.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchesStatus = this.statusFilter === 'all' || s.statut === this.statusFilter;
            return matchesSearch && matchesStatus;
        });
    },

    init() {
        // Select first entretien by default
        if (this.entretiens.length > 0) {
            this.selectedEntretienId = this.entretiens[0].id;
        }

        // Initialize Sortable on candidates pool
        const SortableClass = window.Sortable;
        if (SortableClass) {
            new SortableClass(document.getElementById('candidate-pool'), {
                group: {
                    name: 'candidates',
                    pull: 'clone',
                    put: false
                },
                sort: false,
                animation: 150,
                revertClone: true, // Crucial for Alpine: don't mess with the source DOM
                onStart: () => { this.isDragging = true; },
                onEnd: () => { this.isDragging = false; }
            });

            // Initialize Sortable on drop zone
            new SortableClass(document.getElementById('drop-zone'), {
                group: 'candidates',
                animation: 150,
                onAdd: (evt) => {
                    const candidateId = evt.item.dataset.id;
                    this.assignCandidate(candidateId);
                    evt.item.remove(); // Remove the element Sortable just added
                }
            });
        }
    },

    toggleAllCandidates() {
        const newState = !this.allSelected;
        this.availableCandidates.forEach(c => c.selected = newState);
    },

    async assignCandidate(id) {
        if (!this.selectedEntretienId) {
            alert('Veuillez sélectionner une entretien.');
            return;
        }

        const entretienIndex = this.entretiens.findIndex(s => s.id == this.selectedEntretienId);
        const entretien = this.entretiens[entretienIndex];

        // If multiple are selected, assign all selected ones
        let selectedItems = this.availableCandidates.filter(c => c.selected);
        let selectedIds = selectedItems.map(c => c.id);

        if (!selectedIds.includes(parseInt(id))) {
            const targetCand = this.availableCandidates.find(c => c.id == id);
            if (targetCand) {
                selectedItems.push(targetCand);
                selectedIds.push(parseInt(id));
            }
        }

        if (entretien.candidats_count + selectedIds.length > entretien.capaciteMax) {
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'error',
                    title: 'Capacité atteinte',
                    text: `Impossible d'ajouter ces candidats. La entretien est pleine (${entretien.capaciteMax} max).`,
                    confirmButtonColor: '#1A73E8'
                });
            } else {
                alert(`Impossible d'ajouter ces candidats. La entretien est pleine (${entretien.capaciteMax} max).`);
            }
            // Trigger Alpine re-render to revert Sortable's DOM manipulation
            this.availableCandidates = [...this.availableCandidates];
            return;
        }

        try {
            const response = await fetch(`/admin/entretiens/${this.selectedEntretienId}/assign`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ candidate_ids: selectedIds })
            });

            if (response.ok) {
                // Update local state: move from available to entretien
                const entretienIndex = this.entretiens.findIndex(s => s.id == this.selectedEntretienId);

                selectedItems.forEach(cand => {
                    this.entretiens[entretienIndex].candidats.push({ ...cand, selected: false });
                });

                this.availableCandidates = this.availableCandidates.filter(c => !selectedIds.includes(c.id));
                this.entretiens[entretienIndex].candidats_count = this.entretiens[entretienIndex].candidats.length;

                // Show bottom alert
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'success',
                        text: 'Candidats affectés avec succès.',
                        position: 'bottom',
                        toast: true,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: '#0B1120',
                        color: '#fff',
                        customClass: {
                            popup: 'rounded-full px-6'
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error assigning candidates:', error);
        }
    },

    async unassignCandidate(id) {
        if (window.Swal) {
            const result = await window.Swal.fire({
                title: 'Retrait du candidat',
                text: 'Voulez-vous vraiment retirer ce candidat de la entretien ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1A73E8',
                cancelButtonColor: '#f3f4f6',
                confirmButtonText: 'Oui, retirer',
                cancelButtonText: 'Annuler'
            });
            if (!result.isConfirmed) return;
        } else {
            if (!confirm('Voulez-vous vraiment retirer ce candidat de la entretien ?')) return;
        }

        try {
            const response = await fetch(`/admin/candidates/${id}/unassign`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                const entretienIndex = this.entretiens.findIndex(s => s.id == this.selectedEntretienId);
                const candIndex = this.entretiens[entretienIndex].candidats.findIndex(c => c.id == id);
                const cand = this.entretiens[entretienIndex].candidats[candIndex];

                this.availableCandidates.push({ ...cand, selected: false });
                this.entretiens[entretienIndex].candidats.splice(candIndex, 1);
                this.entretiens[entretienIndex].candidats_count = this.entretiens[entretienIndex].candidats.length;

                // Show bottom alert
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'success',
                        text: 'Candidat retiré de la entretien.',
                        position: 'bottom',
                        toast: true,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: '#0B1120',
                        color: '#fff',
                        customClass: {
                            popup: 'rounded-full px-6'
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error unassigning candidate:', error);
        }
    },

    formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
    },

    editentretien(entretien) {
        this.entretienForm = { ...entretien };
        this.showEntretienModal = true;
    },

    async deleteentretien(id) {
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

    openAddentretienModal() {
        this.resetentretienForm();
        this.generateentretienCode();
        this.showEntretienModal = true;
    },

    generateentretienCode() {
        this.entretienForm.codePresence = Math.floor(1000 + Math.random() * 9000).toString();
    },

    resetentretienForm() {
        this.entretienForm = {
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
