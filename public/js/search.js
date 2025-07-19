class BusSearch {
    constructor() {
        this.initializeEventListeners();
        this.loadOrigins();
        this.initializeDatePicker();
    }

    /**
     * Initialize event listeners
     */
    initializeEventListeners() {
        // Origin change event
        const originSelect = document.getElementById('origin');
        if (originSelect) {
            originSelect.addEventListener('change', () => {
                this.loadDestinations();
            });
        }

        // Search form submission
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.performSearch();
            });
        }

        // Clear filters button
        const clearBtn = document.getElementById('clearFilters');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.clearFilters();
            });
        }
    }

    /**
     * Initialize date picker with restrictions
     */
    initializeDatePicker() {
        const dateInput = document.getElementById('date');
        if (dateInput) {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
            
            // Set default date to today if empty
            if (!dateInput.value) {
                dateInput.value = today;
            }
        }
    }

    /**
     * Load origins from API
     */
    async loadOrigins() {
        try {
            const response = await fetch('api/search.php?action=origins');
            const result = await response.json();

            if (result.success) {
                this.populateSelect('origin', result.data, 'Select Origin');
            } else {
                this.showAlert('Failed to load origins', 'danger');
            }
        } catch (error) {
            console.error('Error loading origins:', error);
            this.showAlert('Failed to load origins', 'danger');
        }
    }

    /**
     * Load destinations based on selected origin
     */
    async loadDestinations() {
        const originSelect = document.getElementById('origin');
        const destinationSelect = document.getElementById('destination');
        
        if (!originSelect || !destinationSelect) return;

        const origin = originSelect.value;
        
        // Clear destinations
        destinationSelect.innerHTML = '<option value="">Select Destination</option>';

        if (!origin) return;

        try {
            this.showLoading(destinationSelect);
            
            const response = await fetch(`api/search.php?action=destinations&origin=${encodeURIComponent(origin)}`);
            const result = await response.json();

            if (result.success) {
                this.populateSelect('destination', result.data, 'Select Destination');
            } else {
                this.showAlert('Failed to load destinations', 'danger');
            }
        } catch (error) {
            console.error('Error loading destinations:', error);
            this.showAlert('Failed to load destinations', 'danger');
        }
    }

    /**
     * Populate select element with options
     */
    populateSelect(elementId, data, placeholder) {
        const select = document.getElementById(elementId);
        if (!select) return;

        select.innerHTML = `<option value="">${placeholder}</option>`;
        
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;
            select.appendChild(option);
        });
    }

    /**
     * Show loading state for select element
     */
    showLoading(element) {
        element.innerHTML = '<option value="">Loading...</option>';
        element.disabled = true;
        
        setTimeout(() => {
            element.disabled = false;
        }, 1000);
    }

    /**
     * Perform bus search
     */
    async performSearch() {
        const form = document.getElementById('searchForm');
        const resultsContainer = document.getElementById('searchResults');
        
        if (!form || !resultsContainer) return;

        // Validate form
        if (!this.validateSearchForm()) return;

        // Get form data
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        try {
            // Show loading
            this.showSearchLoading();

            const response = await fetch(`api/search.php?action=search&${params.toString()}`);
            const result = await response.json();

            if (result.success) {
                this.displaySearchResults(result);
            } else {
                this.showSearchError(result.message || 'Search failed');
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showSearchError('An error occurred while searching. Please try again.');
        }
    }

    /**
     * Validate search form
     */
    validateSearchForm() {
        const origin = document.getElementById('origin').value;
        const destination = document.getElementById('destination').value;
        const date = document.getElementById('date').value;

        // Clear previous errors
        this.clearFieldErrors();

        let isValid = true;

        if (!origin) {
            this.showFieldError('origin', 'Please select an origin');
            isValid = false;
        }

        if (!destination) {
            this.showFieldError('destination', 'Please select a destination');
            isValid = false;
        }

        if (!date) {
            this.showFieldError('date', 'Please select a travel date');
            isValid = false;
        } else {
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                this.showFieldError('date', 'Travel date cannot be in the past');
                isValid = false;
            }
        }

        const maxFare = document.getElementById('maxFare').value;
        if (maxFare && (isNaN(maxFare) || parseFloat(maxFare) < 0)) {
            this.showFieldError('maxFare', 'Please enter a valid fare amount');
            isValid = false;
        }

        return isValid;
    }

    /**
     * Show field error
     */
    showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        field.classList.add('is-invalid');
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    /**
     * Clear field errors
     */
    clearFieldErrors() {
        const invalidFields = document.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => {
            field.classList.remove('is-invalid');
        });

        const errorMessages = document.querySelectorAll('.invalid-feedback');
        errorMessages.forEach(error => {
            error.remove();
        });
    }

    /**
     * Show search loading state
     */
    showSearchLoading() {
        const resultsContainer = document.getElementById('searchResults');
        if (!resultsContainer) return;

        resultsContainer.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
            <p class="text-center mt-3">Searching for buses...</p>
        `;
        resultsContainer.style.display = 'block';
    }

    /**
     * Show search error
     */
    showSearchError(message) {
        const resultsContainer = document.getElementById('searchResults');
        if (!resultsContainer) return;

        resultsContainer.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${this.escapeHtml(message)}
            </div>
        `;
        resultsContainer.style.display = 'block';
    }

    /**
     * Display search results
     */
    displaySearchResults(result) {
        const resultsContainer = document.getElementById('searchResults');
        if (!resultsContainer) return;

        if (!result.data || result.data.length === 0) {
            resultsContainer.innerHTML = `
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    No buses found for your search criteria. Please try different filters.
                </div>
            `;
            resultsContainer.style.display = 'block';
            return;
        }

        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Search Results (${result.total} buses found)</h4>
                <button type="button" class="btn btn-outline-primary btn-sm" id="clearFilters">
                    <i class="fas fa-times me-1"></i> Clear Filters
                </button>
            </div>
        `;

        result.data.forEach(bus => {
            html += this.createBusCard(bus);
        });

        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';

        // Re-bind clear filters event
        document.getElementById('clearFilters')?.addEventListener('click', () => {
            this.clearFilters();
        });
    }

    /**
     * Create bus card HTML
     */
    createBusCard(bus) {
        const availabilityClass = this.getAvailabilityClass(bus.available_seats, bus.total_seats);
        const availabilityText = this.getAvailabilityText(bus.available_seats);

        return `
            <div class="bus-card">
                <div class="bus-header">
                    <div class="bus-info">
                        <h5>${this.escapeHtml(bus.bus_number)} - ${this.escapeHtml(bus.bus_type)}</h5>
                        <div class="operator-name">${this.escapeHtml(bus.operator_name)}</div>
                    </div>
                    <div class="fare-info">
                        <div class="fare-amount">Rs. ${parseFloat(bus.fare).toLocaleString()}</div>
                        <div class="per-seat">per seat</div>
                    </div>
                </div>
                
                <div class="bus-details">
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt detail-icon"></i>
                        <span>${this.escapeHtml(bus.origin)} → ${this.escapeHtml(bus.destination)}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-clock detail-icon"></i>
                        <span>${this.formatTime(bus.departure_time)} - ${this.formatTime(bus.arrival_time)}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-route detail-icon"></i>
                        <span>${bus.distance} km (${bus.estimated_duration})</span>
                    </div>
                    ${bus.amenities ? `
                    <div class="detail-item">
                        <i class="fas fa-star detail-icon"></i>
                        <span>${this.escapeHtml(bus.amenities)}</span>
                    </div>
                    ` : ''}
                </div>
                
                <div class="bus-actions">
                    <div class="availability">
                        <span class="availability-badge ${availabilityClass}">
                            ${bus.available_seats} seats available
                        </span>
                        <small class="text-muted">${availabilityText}</small>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="selectBus(${bus.schedule_id})">
                        <i class="fas fa-ticket-alt me-1"></i> Book Now
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Get availability CSS class based on seat count
     */
    getAvailabilityClass(available, total) {
        const percentage = (available / total) * 100;
        if (percentage > 50) return 'availability-high';
        if (percentage > 20) return 'availability-medium';
        return 'availability-low';
    }

    /**
     * Get availability text
     */
    getAvailabilityText(available) {
        if (available > 10) return 'Good availability';
        if (available > 5) return 'Limited seats';
        return 'Few seats left';
    }

    /**
     * Format time for display
     */
    formatTime(timeString) {
        if (!timeString) return '';
        const time = new Date(`2000-01-01 ${timeString}`);
        return time.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
    }

    /**
     * Clear all filters and results
     */
    clearFilters() {
        document.getElementById('searchForm').reset();
        document.getElementById('searchResults').style.display = 'none';
        this.clearFieldErrors();
        this.initializeDatePicker();
        
        // Reset destination dropdown
        const destinationSelect = document.getElementById('destination');
        if (destinationSelect) {
            destinationSelect.innerHTML = '<option value="">Select Destination</option>';
        }
    }

    /**
     * Show alert message
     */
    showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${this.escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        alertContainer.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, (m) => map[m]);
    }
}

/**
 * Global function to handle bus selection
 */
function selectBus(scheduleId) {
    // Store selected schedule ID and redirect to booking page
    sessionStorage.setItem('selectedScheduleId', scheduleId);
    window.location.href = 'booking.php';
}

/**
 * Initialize the application when DOM is loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    new BusSearch();
});

/**
 * Utility function to format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-LK', {
        style: 'currency',
        currency: 'LKR',
        minimumFractionDigits: 2
    }).format(amount);
}
