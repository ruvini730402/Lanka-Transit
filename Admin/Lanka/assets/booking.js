const seatMap = document.getElementById('seat-map');
const selectedSeatInput = document.getElementById('selected-seat');
let selectedSeat = null;

// Fetch booked seats from PHP
async function fetchBookedSeats() {
  const response = await fetch('seats.php');
  const data = await response.json();
  return data; // returns [{seat: 2, gender: 'female'}, ...]
}

// Create a single seat button
function createSeat(seatNum, bookedSeats) {
  const seat = document.createElement('button');
  seat.textContent = seatNum;
  seat.className = 'seat btn btn-success';
  seat.style.width = '60px';
  seat.style.height = '60px';

  // Lady seat (first 8 seats)
  if (seatNum <= 8) {
    seat.classList.add('lady-seat');
  }

  // Check if seat is already booked
  const booking = bookedSeats.find(b => b.seat == seatNum);
  if (booking) {
    seat.disabled = true;
    seat.classList.add('booked', booking.gender); // Adds 'female', 'male', etc.
  } else {
    seat.addEventListener('click', () => {
      const gender = document.getElementById('gender').value;
      if (!gender) {
        showMessage("Please select gender before choosing a seat.", "danger");
        return;
      }

      if (seatNum <= 8 && gender !== 'female') {
        showMessage("Seats 1 to 8 are reserved for female passengers (unless < 3 hours before journey).", "danger");
        return;
      }

      // Deselect any previously selected
      document.querySelectorAll('.seat.selected').forEach(s => {
        s.classList.remove('selected', 'btn-warning');
        s.classList.add('btn-success');
      });

      seat.classList.remove('btn-success');
      seat.classList.add('selected', 'btn-warning');
      selectedSeat = seatNum;
      selectedSeatInput.value = seatNum;
    });
  }

  return seat;
}

// Render all 40 seats
async function renderSeats() {
  const bookedSeats = await fetchBookedSeats();
  seatMap.innerHTML = '';

  for (let row = 0; row < 10; row++) {
    // 2 seats on left
    for (let i = 0; i < 2; i++) {
      const seatNum = row * 4 + i + 1;
      seatMap.appendChild(createSeat(seatNum, bookedSeats));
    }

    // Aisle
    const aisle = document.createElement('div');
    aisle.className = 'aisle';
    seatMap.appendChild(aisle);

    // 2 seats on right
    for (let i = 2; i < 4; i++) {
      const seatNum = row * 4 + i + 1;
      seatMap.appendChild(createSeat(seatNum, bookedSeats));
    }
  }
}



// Submit booking form
// Submit booking form
document.getElementById('booking-form').addEventListener('submit', async function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const messageDiv = document.getElementById('form-message');
  messageDiv.innerHTML = '';

  if (!formData.get('seat')) {
    showMessage("Please select a seat before booking.", "danger");
    return;
  }

  try {
    const res = await fetch('book.php', {
      method: 'POST',
      body: formData
    });

    const result = await res.json();
    console.log(result); // Helpful for debugging

    if (result.success) {
      const bookingId = result.booking_id;
      showMessage(`Seat booked successfully! Your Booking ID: ${bookingId}`, "success");

      // Wait a moment to show the success message, then redirect
      setTimeout(() => {
        if (bookingId) {
          window.location.href = `payment.php?booking_id=${encodeURIComponent(bookingId)}`;
        }
      }, 1500);
    } else {
      showMessage(result.message, "danger");
    }
  } catch (err) {
    console.error(err);
    showMessage("Something went wrong. Please try again.", "danger");
  }
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



