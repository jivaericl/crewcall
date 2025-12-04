<?php

namespace App\Livewire\Travel;

use App\Models\Hotel;
use App\Models\HotelReservation;
use App\Models\Travel;
use Livewire\Component;
use Livewire\WithPagination;

class HotelRegistrationsList extends Component
{
    use WithPagination;

    public $eventId;
    public $hotelId;
    public $hotel;
    public $search = '';
    public $showAddModal = false;
    public $showEditModal = false;
    public $reservationId;
    public $travelId;
    public $reservationNumber;
    public $checkInDate;
    public $checkOutDate;
    public $notes;

    protected $rules = [
        'travelId' => 'required|exists:travels,id',
        'reservationNumber' => 'nullable|string|max:255',
        'checkInDate' => 'required|date',
        'checkOutDate' => 'required|date|after_or_equal:checkInDate',
        'notes' => 'nullable|string',
    ];

    protected $queryString = ['search'];

    public function mount($eventId, $hotelId)
    {
        $this->eventId = $eventId;
        $this->hotelId = $hotelId;
        $this->hotel = Hotel::findOrFail($hotelId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->reset(['travelId', 'reservationNumber', 'checkInDate', 'checkOutDate', 'notes']);
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function openEditModal($reservationId)
    {
        try {
            $this->reservationId = $reservationId;
            $reservation = HotelReservation::with('travel')->findOrFail($reservationId);
            $this->travelId = $reservation->travel_id;
            $this->reservationNumber = $reservation->reservation_number;
            $this->checkInDate = $reservation->check_in_date ? $reservation->check_in_date->format('Y-m-d') : '';
            $this->checkOutDate = $reservation->check_out_date ? $reservation->check_out_date->format('Y-m-d') : '';
            $this->notes = $reservation->notes;
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading reservation: ' . $e->getMessage());
        }
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
            'travel_id' => $this->travelId,
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
        $reservations = HotelReservation::with(['travel.user'])
            ->where('hotel_id', $this->hotelId)
            ->when($this->search, function ($query) {
                $query->whereHas('travel.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhere('reservation_number', 'like', '%' . $this->search . '%');
            })
            ->orderBy('check_in_date')
            ->paginate(20);

        $travels = Travel::where('event_id', $this->eventId)
            ->with('user')
            ->orderBy('id')
            ->get();

        return view('livewire.travel.hotel-registrations-list', [
            'reservations' => $reservations,
            'travels' => $travels,
        ]);
    }
}
