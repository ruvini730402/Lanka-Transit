const seatMap = document.querySelector('.seat-grid');
const selectedSeatInput = document.getElementById('selected-seat');
const selectedModelInput = document.getElementById('selected-model');
const busModelSelect = document.getElementById('bus-model');
let selectedSeat = null;
// Get bus data from URL parameters
const urlParams = new URLSearchParams(window.location.search);
const busId = urlParams.get('bus_id');
const travelDate = urlParams.get('date');
const departureTime = urlParams.get('departure') || '';
// Validate URL parameters
if (!busId || !travelDate) {
  showMessage('Invalid bus ID or travel date. Please try again.', 'danger');
  console.error('Missing parameters:', { busId, travelDate });
}
// Calculate if lady seats are free (within 3 hours of departure)
let isLadySeatsFree = false;
if (departureTime) {
  try {
    const departure = new Date(`${travelDate}T${departureTime}:00`);
    const threeHoursBefore = new Date(departure.getTime() - 3 * 60 * 60 * 1000);
    const now = new Date();
    isLadySeatsFree = now >= threeHoursBefore;
  } catch (e) {
    console.error('Error parsing departure time:', e);
    showMessage('Invalid departure time format.', 'danger');
  }
}
// Fetch bus capacity and booked seats
async function fetchBusData() {
  try {
    const response = await fetch(`view.php?api=bus_details&bus_id=${encodeURIComponent(busId)}&date=${encodeURIComponent(travelDate)}`);
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const data = await response.json();
   
    if (data.error) {
      console.error('API error response:', data.error);
      showMessage('Failed to load bus details: ' + data.error, 'danger');
      return { capacity: 49, seats: [], available_seats: 49 };
    }
   
    console.log('Fetched bus data:', data); // Debug log
    return {
      capacity: parseInt(data.capacity) || 49,
      seats: Array.isArray(data.seats) ? data.seats : [],
      available_seats: parseInt(data.available_seats) || data.capacity
    };
  } catch (error) {
    console.error('Failed to fetch bus data:', error.message);
    showMessage('Error loading bus data. Please try again.', 'danger');
    return { capacity: 49, seats: [], available_seats: 49 };
  }
}
// Create a single seat button
function createSeat(seatNum, seatData) {
  const seat = document.createElement('button');
  seat.textContent = seatNum;
  seat.className = 'seat btn';
  seat.type = 'button';
  // Lady seat (first 8 seats)
  if (seatNum <= 8) {
    seat.classList.add('lady-seat');
    seat.title = 'Lady Seat'; // Add accessibility title
  }
  // Find seat data
  const seatInfo = seatData.find(s => s.seat == seatNum);
 
  if (seatInfo && seatInfo.status === 'booked') {
    seat.disabled = true;
    seat.classList.add('booked');
   
    // Apply gender-based styling
    const gender = seatInfo.gender_preference ? seatInfo.gender_preference.toLowerCase() : 'undisclosed';
    if (gender === 'female') {
      seat.classList.add('female');
    } else if (gender === 'male') {
      seat.classList.add('male');
    } else {
      seat.classList.add('undisclosed');
    }
  } else {
    seat.classList.add('available');
    seat.addEventListener('click', () => {
      const gender = document.getElementById('gender')?.value.toLowerCase();
      if (!gender) {
        showMessage("Please select gender before choosing a seat.", "danger");
        return;
      }
      if (seatNum <= 8 && gender !== 'female' && !isLadySeatsFree) {
        showMessage("Seats 1 to 8 are reserved for female passengers.", "danger");
        return;
      }
      // Toggle selection
      if (selectedSeat === seatNum) {
        seat.classList.remove('selected');
        seat.classList.add('available');
        selectedSeat = null;
        selectedSeatInput.value = '';
      } else {
        document.querySelectorAll('.seat.selected').forEach(s => {
          s.classList.remove('selected');
          s.classList.add('available');
        });
        seat.classList.remove('available');
        seat.classList.add('selected');
        selectedSeat = seatNum;
        selectedSeatInput.value = seatNum;
      }
    });
  }
  return seat;
}
// Render all seats based on capacity
async function renderSeats() {
  const { capacity, seats, available_seats } = await fetchBusData();
  seatMap.innerHTML = '';
  selectedSeat = null;
  selectedSeatInput.value = '';
  selectedModelInput.value = capacity;
  // Update available seats display
  const availableSeatsDisplay = document.createElement('div');
  availableSeatsDisplay.className = 'text-center mb-3';
  availableSeatsDisplay.innerHTML = `<strong>Available Seats: ${available_seats}</strong>`;
  seatMap.parentElement.insertBefore(availableSeatsDisplay, seatMap);
  // Dynamic seat layout configuration
  const config = capacity <= 49
    ? { left: 2, right: 2, rows: Math.ceil(capacity / 4), hasLastFull: capacity % 4 === 0, lastRightAdjust: 0 }
    : { left: 2, right: 3, rows: Math.ceil(capacity / 5), hasLastFull: capacity % 5 === 0, lastRightAdjust: -1 };
  seatMap.style.gridTemplateColumns = `repeat(${config.left}, 55px) 50px repeat(${config.right}, 55px)`;
  seatMap.style.gridAutoRows = '55px';
  // Render seats
  let seatNum = 0;
  for (let row = 0; row < config.rows; row++) {
    const isLast = row === config.rows - 1;
    let rightThis = config.right;
    if (isLast && !config.hasLastFull) {
      rightThis += config.lastRightAdjust;
    }
    // Left seats
    for (let i = 0; i < config.left && seatNum < capacity; i++) {
      seatNum++;
      seatMap.appendChild(createSeat(seatNum, seats));
    }
    // Aisle or extra seat
    if (!isLast || !config.hasLastFull) {
      const aisle = document.createElement('div');
      aisle.className = 'aisle';
      seatMap.appendChild(aisle);
    } else if (seatNum < capacity) {
      seatNum++;
      seatMap.appendChild(createSeat(seatNum, seats));
    }
    // Right seats
    for (let i = 0; i < rightThis && seatNum < capacity; i++) {
      seatNum++;
      seatMap.appendChild(createSeat(seatNum, seats));
    }
  }
}
// Submit booking form
document.getElementById('booking-form').addEventListener('submit', async function (e) {
  if (!selectedSeat) {
    e.preventDefault();
    showMessage("Please select a seat before booking.", "danger");
    return;
  }
 
  showMessage("Processing your booking...", "info");
});
// Bootstrap Alert Helper
function showMessage(message, type = 'info') {
  const messageDiv = document.getElementById('form-message');
  if (messageDiv) {
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
}
// Event listener for model change (retained for potential future use)
if (busModelSelect) {
  busModelSelect.addEventListener('change', (e) => {
    renderSeats();
  });
}
// Initial render
renderSeats();