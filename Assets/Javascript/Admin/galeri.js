(function() {
    'use strict';
    
    let editMode = false;
    let selectedItems = [];
    let currentPage = 1;
    let itemsPerPage = 9;
    let allItems = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Galeri.js loaded'); 
        initializeGallery();
        updatePagination();
        setupGalleryItemHandlers();
    });

    function initializeGallery() {
        const galleryGrid = document.getElementById('galleryGrid');
        if (!galleryGrid) {
            console.error('Gallery grid not found!');
            return;
        }
        
        allItems = Array.from(galleryGrid.querySelectorAll('.gallery-item'));
        console.log('Gallery items found:', allItems.length); 
        
        
        showPage(1);
    }
    
    function setupGalleryItemHandlers() {
        const galleryItems = document.querySelectorAll('.gallery-item');
        console.log('Setting up handlers for', galleryItems.length, 'items'); 
        
        galleryItems.forEach(item => {
            
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Item clicked, edit mode:', editMode); 
                
                if (editMode) {
                    const id = parseInt(this.dataset.id);
                    toggleItemSelection(id);
                }
            });
            
            
            const checkbox = item.querySelector('.select-checkbox');
            if (checkbox) {
                checkbox.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (editMode) {
                        const id = parseInt(item.dataset.id);
                        toggleItemSelection(id);
                    }
                });
            }
        });
    }

    
    window.toggleEditMode = function() {
        const toggleInput = document.getElementById('editToggle');
        editMode = toggleInput.checked;
        
        console.log('Toggle clicked! Edit mode now:', editMode); 
        
        if (editMode) {
            document.body.classList.add('edit-mode-active');
            console.log('Edit mode ACTIVATED - checkboxes should show'); 
        } else {
            document.body.classList.remove('edit-mode-active');
            clearAllSelections();
            console.log('Edit mode DEACTIVATED'); 
        }
    };

    
    function toggleItemSelection(id) {
        const item = document.querySelector(`.gallery-item[data-id="${id}"]`);
        
        if (!item) {
            console.error('Item not found:', id);
            return;
        }
        
        if (item.classList.contains('selected')) {
            
            item.classList.remove('selected');
            selectedItems = selectedItems.filter(itemId => itemId !== id);
            console.log('Deselected item:', id, 'Total selected:', selectedItems.length);
        } else {
            
            item.classList.add('selected');
            if (!selectedItems.includes(id)) {
                selectedItems.push(id);
            }
            console.log('Selected item:', id, 'Total selected:', selectedItems.length);
        }
        
        updateDeleteNotification();
    }

    
    window.selectAll = function() {
        if (!editMode) {
            alert('Aktifkan mode edit terlebih dahulu!');
            return;
        }

        const visibleItems = getVisibleItems();
        if (visibleItems.length === 0) return;

        
        const allVisibleSelected = visibleItems.every(item =>
            item.classList.contains('selected')
        );

        if (allVisibleSelected) {
            
            visibleItems.forEach(item => {
                const id = parseInt(item.dataset.id);
                item.classList.remove('selected');
                selectedItems = selectedItems.filter(itemId => itemId !== id);
            });
            console.log('Deselected all visible items');
        } else {
            
            visibleItems.forEach(item => {
                const id = parseInt(item.dataset.id);
                item.classList.add('selected');
                if (!selectedItems.includes(id)) selectedItems.push(id);
            });
            console.log('Selected all visible items:', selectedItems);
        }

        updateDeleteNotification();
    };

    
    function clearAllSelections() {
        selectedItems = [];
        document.querySelectorAll('.gallery-item').forEach(item => {
            item.classList.remove('selected');
        });
        updateDeleteNotification();
    }

    
    function updateDeleteNotification() {
        const notification = document.getElementById('deleteNotification');
        const countSpan = document.getElementById('deleteCount');
        
        if (!notification || !countSpan) {
            console.error('Delete notification elements not found');
            return;
        }
        
        if (selectedItems.length > 0) {
            countSpan.textContent = `${selectedItems.length} item dipilih`;
            notification.classList.add('show');
            console.log('Delete notification shown:', selectedItems.length, 'items'); 
        } else {
            notification.classList.remove('show');
            console.log('Delete notification hidden'); 
        }
    }

    
    window.confirmDelete = function() {
        if (selectedItems.length === 0) return;
        
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.add('show');
        }
    };

    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.remove('show');
        }
    };

    
    window.deleteSelected = function() {
        if (selectedItems.length === 0) return;
        
        
        const confirmBtn = document.querySelector('.btn-confirm');
        const originalText = confirmBtn.textContent;
        confirmBtn.textContent = 'Menghapus...';
        confirmBtn.disabled = true;
        
        
        fetch('proses_galeri.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `action=delete&ids=${JSON.stringify(selectedItems)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                
                selectedItems.forEach(id => {
                    const item = document.querySelector(`.gallery-item[data-id="${id}"]`);
                    if (item) {
                        item.remove();
                    }
                });
                
                
                allItems = Array.from(document.querySelectorAll('.gallery-item'));
                
                
                selectedItems = [];
                
                
                window.closeModal();
                
                
                updatePagination();
                
                
                showAlert('success', data.message);
                
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('error', data.error || 'Gagal menghapus data');
                confirmBtn.textContent = originalText;
                confirmBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Terjadi kesalahan saat menghapus data');
            confirmBtn.textContent = originalText;
            confirmBtn.disabled = false;
        });
    };

    
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `
            <span>${type === 'success' ? '✓' : '✗'}</span>
            <span>${message}</span>
        `;
        
        const mainContent = document.querySelector('.main-content');
        const controls = document.querySelector('.controls');
        mainContent.insertBefore(alertDiv, controls);
        
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    
    function getVisibleItems() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        return allItems.slice(start, end);
    }

    function showPage(page) {
        currentPage = page;
        
        
        allItems.forEach(item => {
            item.style.display = 'none';
        });
        
        
        const visibleItems = getVisibleItems();
        visibleItems.forEach(item => {
            item.style.display = 'block';
        });
        
        updatePagination();
    }

    function updatePagination() {
        const totalPages = Math.ceil(allItems.length / itemsPerPage);
        const pageInfo = document.getElementById('pageInfo');
        
        if (pageInfo) {
            pageInfo.textContent = `${currentPage} of ${totalPages}`;
        }
        
        
        const prevBtn = document.querySelector('.pagination-btn:first-child');
        const nextBtn = document.querySelector('.pagination-btn:last-child');
        
        if (prevBtn) {
            prevBtn.disabled = currentPage === 1;
        }
        
        if (nextBtn) {
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
        }
    }

    window.prevPage = function() {
        if (currentPage > 1) {
            showPage(currentPage - 1);
        }
    };

    window.nextPage = function() {
        const totalPages = Math.ceil(allItems.length / itemsPerPage);
        if (currentPage < totalPages) {
            showPage(currentPage + 1);
        }
    };

    
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            window.closeModal();
        }
    });

    
    document.addEventListener('sidebarToggled', function(e) {
        const mainContent = document.querySelector('.main-content');
        if (e.detail.isOpen) {
            mainContent.classList.add('shifted');
        } else {
            mainContent.classList.remove('shifted');
        }
    });

})();