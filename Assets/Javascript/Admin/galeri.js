// galeri.js - Gallery JavaScript Functions (FIXED VERSION - No Duplicate Variables)

// Wrap everything in IIFE to avoid global scope conflicts
(function() {
    'use strict';
    
    // Global variables (scoped to this file only)
    let editMode = false;
    let selectedItems = [];
    let currentPage = 1;
    let itemsPerPage = 9;
    let allItems = [];

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Galeri.js loaded'); // Debug
        initializeGallery();
        updatePagination();
        setupGalleryItemHandlers();
    });

    // Initialize gallery
    function initializeGallery() {
        const galleryGrid = document.getElementById('galleryGrid');
        if (!galleryGrid) {
            console.error('Gallery grid not found!');
            return;
        }
        
        allItems = Array.from(galleryGrid.querySelectorAll('.gallery-item'));
        console.log('Gallery items found:', allItems.length); // Debug
        
        // Show first page
        showPage(1);
    }

    // Setup click handlers for gallery items
    function setupGalleryItemHandlers() {
        const galleryItems = document.querySelectorAll('.gallery-item');
        console.log('Setting up handlers for', galleryItems.length, 'items'); // Debug
        
        galleryItems.forEach(item => {
            // Main item click
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Item clicked, edit mode:', editMode); // Debug
                
                if (editMode) {
                    const id = parseInt(this.dataset.id);
                    toggleItemSelection(id);
                }
            });
            
            // Checkbox click
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

    // Toggle edit mode
    window.toggleEditMode = function() {
        const toggleInput = document.getElementById('editToggle');
        editMode = toggleInput.checked;
        
        console.log('Toggle clicked! Edit mode now:', editMode); // Debug
        
        if (editMode) {
            document.body.classList.add('edit-mode-active');
            console.log('Edit mode ACTIVATED - checkboxes should show'); // Debug
        } else {
            document.body.classList.remove('edit-mode-active');
            clearAllSelections();
            console.log('Edit mode DEACTIVATED'); // Debug
        }
    };

    // Toggle item selection
    function toggleItemSelection(id) {
        const item = document.querySelector(`.gallery-item[data-id="${id}"]`);
        
        if (!item) {
            console.error('Item not found:', id);
            return;
        }
        
        if (item.classList.contains('selected')) {
            // Deselect
            item.classList.remove('selected');
            selectedItems = selectedItems.filter(itemId => itemId !== id);
            console.log('Deselected item:', id, 'Total selected:', selectedItems.length);
        } else {
            // Select
            item.classList.add('selected');
            if (!selectedItems.includes(id)) {
                selectedItems.push(id);
            }
            console.log('Selected item:', id, 'Total selected:', selectedItems.length);
        }
        
        updateDeleteNotification();
    }

    // Select all items
    window.selectAll = function() {
        if (!editMode) {
            alert('Aktifkan mode edit terlebih dahulu!');
            return;
        }
        
        const visibleItems = getVisibleItems();
        selectedItems = [];
        
        visibleItems.forEach(item => {
            const id = parseInt(item.dataset.id);
            item.classList.add('selected');
            if (!selectedItems.includes(id)) {
                selectedItems.push(id);
            }
        });
        
        console.log('Selected all items:', selectedItems); // Debug
        updateDeleteNotification();
    };

    // Clear all selections
    function clearAllSelections() {
        selectedItems = [];
        document.querySelectorAll('.gallery-item').forEach(item => {
            item.classList.remove('selected');
        });
        updateDeleteNotification();
    }

    // Update delete notification
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
            console.log('Delete notification shown:', selectedItems.length, 'items'); // Debug
        } else {
            notification.classList.remove('show');
            console.log('Delete notification hidden'); // Debug
        }
    }

    // Confirm delete
    window.confirmDelete = function() {
        if (selectedItems.length === 0) return;
        
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.add('show');
        }
    };

    // Close modal
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.remove('show');
        }
    };

    // Delete selected items
    window.deleteSelected = function() {
        if (selectedItems.length === 0) return;
        
        // Show loading state
        const confirmBtn = document.querySelector('.btn-confirm');
        const originalText = confirmBtn.textContent;
        confirmBtn.textContent = 'Menghapus...';
        confirmBtn.disabled = true;
        
        // Send delete request
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
                // Remove deleted items from DOM
                selectedItems.forEach(id => {
                    const item = document.querySelector(`.gallery-item[data-id="${id}"]`);
                    if (item) {
                        item.remove();
                    }
                });
                
                // Update allItems array
                allItems = Array.from(document.querySelectorAll('.gallery-item'));
                
                // Clear selections
                selectedItems = [];
                
                // Close modal
                window.closeModal();
                
                // Update pagination
                updatePagination();
                
                // Show success message
                showAlert('success', data.message);
                
                // Reload page after short delay
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

    // Show alert message
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
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Pagination functions
    function getVisibleItems() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        return allItems.slice(start, end);
    }

    function showPage(page) {
        currentPage = page;
        
        // Hide all items
        allItems.forEach(item => {
            item.style.display = 'none';
        });
        
        // Show current page items
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
        
        // Update button states
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

    // Handle clicks outside modal to close it
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            window.closeModal();
        }
    });

    // Handle sidebar toggle
    document.addEventListener('sidebarToggled', function(e) {
        const mainContent = document.querySelector('.main-content');
        if (e.detail.isOpen) {
            mainContent.classList.add('shifted');
        } else {
            mainContent.classList.remove('shifted');
        }
    });

})();