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

// Calculate if lady seats are free (within 3 hours of departure)
let isLadySeatsFree = false;
if (departureTime) {
  const departure = new Date(`${travelDate}T${departureTime}:00`);
  const threeHoursBefore = new Date(departure.getTime() - 3 * 60 * 60 * 1000);
  const now = new Date();
  isLadySeatsFree = now >= threeHoursBefore;
}

// Fetch booked seats from PHP
async function fetchBookedSeats(model) {
  try {
    const response = await fetch(`view.php?api=seats&bus_id=${busId}&date=${travelDate}&model=${model}`);
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
  seat.className = 'seat btn';
  seat.type = 'button';

  // Lady seat (first 8 seats)
  if (seatNum <= 8) {
    seat.classList.add('lady-seat');
  }

  // Find seat data
  const seatInfo = seatData.find(s => s.seat == seatNum);
  
  if (seatInfo && seatInfo.status === 'booked') {
    seat.disabled = true;
    seat.classList.add('booked');
    
    // Color based on gender of booked passenger
    if (seatInfo.gender_preference === 'female') {
      seat.classList.add('female');
    } else if (seatInfo.gender_preference === 'male') {
      seat.classList.add('male');
    } else {
      seat.classList.add('undisclosed');
    }
  } else {
    seat.addEventListener('click', () => {
      const gender = document.getElementById('gender').value;
      if (!gender) {
        showMessage("Please select gender before choosing a seat.", "danger");
        return;
      }

      if (seatNum <= 8 && gender !== 'female' && !isLadySeatsFree) {
        showMessage("Seats 1 to 8 are reserved for female passengers.", "danger");
        return;
      }

      // Deselect any previously selected
      document.querySelectorAll('.seat.selected').forEach(s => {
        s.classList.remove('selected');
      });

      seat.classList.add('selected');
      selectedSeat = seatNum;
      selectedSeatInput.value = seatNum;
    });
  }

  return seat;
}

// Render all seats based on model
async function renderSeats(model) {
  const seatData = await fetchBookedSeats(model);
  seatMap.innerHTML = '';
  selectedSeat = null;
  selectedSeatInput.value = '';
  selectedModelInput.value = model;

  let config;
  if (model === '49') {
    config = { left: 2, right: 2, rows: 12, hasLastFull: true, lastRightAdjust: 0 };
  } else {
    config = { left: 2, right: 3, rows: 11, hasLastFull: false, lastRightAdjust: -1 };
  }

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
    for (let i = 0; i < config.left; i++) {
      seatNum++;
      seatMap.appendChild(createSeat(seatNum, seatData));
    }

    // Aisle or extra seat
    if (!isLast || !config.hasLastFull) {
      const aisle = document.createElement('div');
      aisle.className = 'aisle';
      seatMap.appendChild(aisle);
    } else {
      seatNum++;
      seatMap.appendChild(createSeat(seatNum, seatData));
    }

    // Right seats
    for (let i = 0; i < rightThis; i++) {
      seatNum++;
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

// Event listener for model change
busModelSelect.addEventListener('change', (e) => {
  renderSeats(e.target.value);
});

// Initial render with default model
renderSeats('49');