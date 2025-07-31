const seatMap = document.getElementById('seat-map');
const selectedSeatInput = document.getElementById('selected-seat');
let selectedSeat = null;

// Get bus data from URL parameters
const urlParams = new URLSearchParams(window.location.search);
const busId = urlParams.get('bus_id');
const travelDate = urlParams.get('date');

// Fetch booked seats from PHP
async function fetchBookedSeats() {
  try {
    const response = await fetch(`view.php?api=seats&bus_id=${busId}&date=${travelDate}`);
    const data = await response.json();
    
    if (data.error) {
      console.error('Error fetching seats:', data.error);
      return [];
    }
    
    return data.seats || [];
  } catch (error) {
    console.error('Failed to fetch seat data:', error);
    return [];
  }
}

// Create a single seat button
function createSeat(seatNum, seatData) {
  const seat = document.createElement('button');
  seat.textContent = seatNum;
  seat.className = 'seat btn btn-success';
  seat.style.width = '60px';
  seat.style.height = '60px';
  seat.type = 'button'; // Prevent form submission

  // Lady seat (first 8 seats)
  if (seatNum <= 8) {
    seat.classList.add('lady-seat');
    seat.style.border = '2px solid red';
  }

  // Find seat data
  const seatInfo = seatData.find(s => s.seat == seatNum);
  
  if (seatInfo && seatInfo.status === 'booked') {
    seat.disabled = true;
    seat.classList.remove('btn-success');
    
    // Color based on gender of booked passenger
    if (seatInfo.gender_preference === 'female') {
      seat.classList.add('btn-secondary');
      seat.style.backgroundColor = '#ffb6c1'; // Light pink
    } else if (seatInfo.gender_preference === 'male') {
      seat.style.backgroundColor = '#add8e6'; // Light blue
    } else {
      seat.style.backgroundColor = '#A9A9A9'; // Gray
    }
    seat.style.color = '#000';
  } else {
    seat.addEventListener('click', () => {
      const gender = document.getElementById('gender').value;
      if (!gender) {
        showMessage("Please select gender before choosing a seat.", "danger");
        return;
      }

      if (seatNum <= 8 && gender !== 'female') {
        showMessage("Seats 1 to 8 are reserved for female passengers.", "danger");
        return;
      }

      // Deselect any previously selected
      document.querySelectorAll('.seat.selected').forEach(s => {
        s.classList.remove('selected', 'btn-warning');
        s.classList.add('btn-success');
      });

      seat.classList.remove('btn-success');
      seat.classList.add('selected', 'btn-warning');
      seat.style.backgroundColor = '#FFA500'; // Orange
      selectedSeat = seatNum;
      selectedSeatInput.value = seatNum;
    });
  }

  return seat;
}

// Render all 40 seats
async function renderSeats() {
  const seatData = await fetchBookedSeats();
  seatMap.innerHTML = '';

  for (let row = 0; row < 10; row++) {
    // 2 seats on left
    for (let i = 0; i < 2; i++) {
      const seatNum = row * 4 + i + 1;
      seatMap.appendChild(createSeat(seatNum, seatData));
    }

    // Aisle
    const aisle = document.createElement('div');
    aisle.className = 'aisle';
    seatMap.appendChild(aisle);

    // 2 seats on right
    for (let i = 2; i < 4; i++) {
      const seatNum = row * 4 + i + 1;
      seatMap.appendChild(createSeat(seatNum, seatData));
    }
  }
}



// Submit booking form
document.getElementById('booking-form').addEventListener('submit', function (e) {
  if (!selectedSeat) {
    e.preventDefault();
    showMessage("Please select a seat before booking.", "danger");
    return;
  }
  
  // Form will submit normally to book.php
  showMessage("Processing your booking...", "info");
});


// Bootstrap Alert Helper
function showMessage(message, type = 'info') {
  const messageDiv = document.getElementById('form-message');
  messageDiv.innerHTML = `
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;
  setTimeout(() => {
    const alertEl = document.querySelector('#form-message .alert');
    if (alertEl) alertEl.remove();
  }, 4000);
}
// Call this on page load to populate seat map
renderSeats();

// INITIALIZE SEAT MAP
