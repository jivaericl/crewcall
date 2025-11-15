<?php

namespace App\Livewire\Travel;

use App\Models\Flight;
use App\Models\Travel;
use Livewire\Component;

class Flights extends Component
{
    public $travelId;
    public $travel;
    public $showAddModal = false;
    public $showEditModal = false;
    public $flightId;
    public $airline;
    public $flightNumber;
    public $departureAirport;
    public $departureTime;
    public $arrivalAirport;
    public $arrivalTime;
    public $notes;

    protected $rules = [
        'airline' => 'required|string|max:255',
        'flightNumber' => 'required|string|max:255',
        'departureAirport' => 'required|string|max:255',
        'departureTime' => 'required|date',
        'arrivalAirport' => 'required|string|max:255',
        'arrivalTime' => 'required|date|after:departureTime',
        'notes' => 'nullable|string',
    ];

    public function mount($travelId)
    {
        $this->travelId = $travelId;
        $this->travel = Travel::with('user')->findOrFail($travelId);
    }

    public function openAddModal()
    {
        $this->reset(['airline', 'flightNumber', 'departureAirport', 'departureTime', 'arrivalAirport', 'arrivalTime', 'notes']);
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function openEditModal($flightId)
    {
        $this->flightId = $flightId;
        $flight = Flight::findOrFail($flightId);
        $this->airline = $flight->airline;
        $this->flightNumber = $flight->flight_number;
        $this->departureAirport = $flight->departure_airport;
        $this->departureTime = $flight->departure_time->format('Y-m-d\TH:i');
        $this->arrivalAirport = $flight->arrival_airport;
        $this->arrivalTime = $flight->arrival_time->format('Y-m-d\TH:i');
        $this->notes = $flight->notes;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function save()
    {
        $this->validate();

        Flight::create([
            'travel_id' => $this->travelId,
            'airline' => $this->airline,
            'flight_number' => $this->flightNumber,
            'departure_airport' => $this->departureAirport,
            'departure_time' => $this->departureTime,
            'arrival_airport' => $this->arrivalAirport,
            'arrival_time' => $this->arrivalTime,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Flight added successfully.');
        $this->closeAddModal();
    }

    public function update()
    {
        $this->validate();

        $flight = Flight::findOrFail($this->flightId);
        $flight->update([
            'airline' => $this->airline,
            'flight_number' => $this->flightNumber,
            'departure_airport' => $this->departureAirport,
            'departure_time' => $this->departureTime,
            'arrival_airport' => $this->arrivalAirport,
            'arrival_time' => $this->arrivalTime,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Flight updated successfully.');
        $this->closeEditModal();
    }

    public function delete($flightId)
    {
        $flight = Flight::findOrFail($flightId);
        $flight->delete();

        session()->flash('message', 'Flight deleted successfully.');
    }

    public function render()
    {
        $flights = Flight::where('travel_id', $this->travelId)
            ->orderBy('departure_time')
            ->get();

        return view('livewire.travel.flights', [
            'flights' => $flights,
        ]);
    }
}
