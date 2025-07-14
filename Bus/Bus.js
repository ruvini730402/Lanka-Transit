const seatMap = document.getElementById('seat-map');
const selectedSeatInput = document.getElementById('selected-seat');
const genderSelect = document.getElementById('gender');

let bookedSeats = [];
let selectedSeat = null;

const rows = 10;
const seatsPerRow = 4; // 2 left + 2 right
const ladySeatLimit = 8; // First 2 rows × 4 seats

document.addEventListener('DOMContentLoaded', async () => {
  await fetchBookedSeats();
  renderSeats();
});

async function fetchBookedSeats() {
  try {
    const res = await fetch('seats.php');
    if (!res.ok) throw new Error('Network issue');
    bookedSeats = await res.json(); // should return list of {seat: num, gender: "male"/"female"/"undisclosed"}
  } catch (e) {
    console.error('Error loading booked seats:', e);
    bookedSeats = [];
  }
}

function renderSeats() {
  seatMap.innerHTML = '';
  let seatNumber = 1;

  for (let row = 0; row < rows; row++) {
    const rowDiv = document.createElement('div');
    rowDiv.className = 'd-flex justify-content-center mb-2';

    // Left side (2 seats)
    for (let i = 0; i < 2; i++) {
      const seat = createSeat(seatNumber, row);
      rowDiv.appendChild(seat);
      seatNumber++;
    }

    // Aisle spacer
    const aisle = document.createElement('div');
    aisle.style.width = '40px';
    rowDiv.appendChild(aisle);

    // Right side (2 seats)
    for (let i = 0; i < 2; i++) {
      const seat = createSeat(seatNumber, row);
      rowDiv.appendChild(seat);
      seatNumber++;
    }

    seatMap.appendChild(rowDiv);
  }
}

function createSeat(seatNum, row) {
  const seat = document.createElement('button');
  seat.textContent = seatNum;
  seat.className = 'btn btn-sm mx-1 seat';
  seat.style.width = '45px';
  seat.style.height = '45px';

  const booking = bookedSeats.find(s => parseInt(s.seat) === seatNum);
  if (booking) {
    seat.disabled = true;
    seat.style.cursor = 'not-allowed';
    switch (booking.gender) {
      case 'female':
        seat.style.backgroundColor = '#ffb6c1'; // pink
        break;
      case 'male':
        seat.style.backgroundColor = '#add8e6'; // blue
        break;
      default:
        seat.style.backgroundColor = '#d3d3d3'; // grey
    }
  } else {
    seat.style.backgroundColor = '#90ee90'; // available

    seat.addEventListener('click', function () {
      const gender = genderSelect.value;
      if (!gender) {
        alert('Please select gender before choosing a seat.');
        return;
      }

      if (seatNum <= ladySeatLimit && gender !== 'female') {
        alert('First 2 rows are for female passengers only.');
        return;
      }

      // Deselect any previously selected
      document.querySelectorAll('.seat.selected').forEach(s => {
        s.classList.remove('selected');
        s.style.backgroundColor = '#90ee90';
      });

      seat.classList.add('selected');
      seat.style.backgroundColor = 'orange';
      selectedSeat = seatNum;
      selectedSeatInput.value = seatNum;
    });
  }

  return seat;
}
