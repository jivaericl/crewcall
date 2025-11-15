<?php

namespace App\Livewire\Travel;

use App\Models\Hotel;
use App\Models\HotelReservation;
use App\Models\Travel;
use Livewire\Component;

class HotelReservations extends Component
{
    public $travelId;
    public $travel;
    public $showAddModal = false;
    public $showEditModal = false;
    public $reservationId;
    public $hotelId;
    public $reservationNumber;
    public $checkInDate;
    public $checkOutDate;
    public $notes;

    protected $rules = [
        'hotelId' => 'required|exists:hotels,id',
        'reservationNumber' => 'nullable|string|max:255',
        'checkInDate' => 'required|date',
        'checkOutDate' => 'required|date|after_or_equal:checkInDate',
        'notes' => 'nullable|string',
    ];

    public function mount($travelId)
    {
        $this->travelId = $travelId;
        $this->travel = Travel::with('user')->findOrFail($travelId);
    }

    public function openAddModal()
    {
        $this->reset(['hotelId', 'reservationNumber', 'checkInDate', 'checkOutDate', 'notes']);
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function openEditModal($reservationId)
    {
        $this->reservationId = $reservationId;
        $reservation = HotelReservation::findOrFail($reservationId);
        $this->hotelId = $reservation->hotel_id;
        $this->reservationNumber = $reservation->reservation_number;
        $this->checkInDate = $reservation->check_in_date->format('Y-m-d');
        $this->checkOutDate = $reservation->check_out_date->format('Y-m-d');
        $this->notes = $reservation->notes;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function save()
    {
        $this->validate();

        HotelReservation::create([
            'travel_id' => $this->travelId,
            'hotel_id' => $this->hotelId,
            'reservation_number' => $this->reservationNumber,
            'check_in_date' => $this->checkInDate,
            'check_out_date' => $this->checkOutDate,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Hotel reservation added successfully.');
        $this->closeAddModal();
    }

    public function update()
    {
        $this->validate();

        $reservation = HotelReservation::findOrFail($this->reservationId);
        $reservation->update([
            'hotel_id' => $this->hotelId,
            'reservation_number' => $this->reservationNumber,
            'check_in_date' => $this->checkInDate,
            'check_out_date' => $this->checkOutDate,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Hotel reservation updated successfully.');
        $this->closeEditModal();
    }

    public function delete($reservationId)
    {
        $reservation = HotelReservation::findOrFail($reservationId);
        $reservation->delete();

        session()->flash('message', 'Hotel reservation deleted successfully.');
    }

    public function render()
    {
        $reservations = HotelReservation::with('hotel')
            ->where('travel_id', $this->travelId)
            ->orderBy('check_in_date')
            ->get();

        $hotels = Hotel::where('event_id', $this->travel->event_id)
            ->orderBy('name')
            ->get();

        return view('livewire.travel.hotel-reservations', [
            'reservations' => $reservations,
            'hotels' => $hotels,
        ]);
    }
}
