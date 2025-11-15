<?php

namespace App\Livewire\Travel;

use App\Models\Event;
use App\Models\Hotel;
use Livewire\Component;
use Livewire\WithPagination;

class Hotels extends Component
{
    use WithPagination;

    public $eventId;
    public $event;
    public $search = '';
    public $showAddModal = false;
    public $showEditModal = false;
    public $hotelId;
    public $name;
    public $address;
    public $city;
    public $state;
    public $zip;
    public $country;
    public $mapsLink;
    public $website;
    public $email;
    public $contactPerson;
    public $phone;
    public $notes;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'zip' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'mapsLink' => 'nullable|url|max:255',
        'website' => 'nullable|url|max:255',
        'email' => 'nullable|email|max:255',
        'contactPerson' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->reset(['name', 'address', 'city', 'state', 'zip', 'country', 'mapsLink', 'website', 'email', 'contactPerson', 'phone', 'notes']);
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function openEditModal($hotelId)
    {
        $this->hotelId = $hotelId;
        $hotel = Hotel::findOrFail($hotelId);
        $this->name = $hotel->name;
        $this->address = $hotel->address;
        $this->city = $hotel->city;
        $this->state = $hotel->state;
        $this->zip = $hotel->zip;
        $this->country = $hotel->country;
        $this->mapsLink = $hotel->maps_link;
        $this->website = $hotel->website;
        $this->email = $hotel->email;
        $this->contactPerson = $hotel->contact_person;
        $this->phone = $hotel->phone;
        $this->notes = $hotel->notes;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function save()
    {
        $this->validate();

        Hotel::create([
            'event_id' => $this->eventId,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'maps_link' => $this->mapsLink,
            'website' => $this->website,
            'email' => $this->email,
            'contact_person' => $this->contactPerson,
            'phone' => $this->phone,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Hotel added successfully.');
        $this->closeAddModal();
    }

    public function update()
    {
        $this->validate();

        $hotel = Hotel::findOrFail($this->hotelId);
        $hotel->update([
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'maps_link' => $this->mapsLink,
            'website' => $this->website,
            'email' => $this->email,
            'contact_person' => $this->contactPerson,
            'phone' => $this->phone,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Hotel updated successfully.');
        $this->closeEditModal();
    }

    public function delete($hotelId)
    {
        $hotel = Hotel::findOrFail($hotelId);

        // Check if hotel has any reservations
        if ($hotel->hotelReservations()->count() > 0) {
            session()->flash('error', 'Cannot delete hotel with existing reservations.');
            return;
        }

        $hotel->delete();

        session()->flash('message', 'Hotel deleted successfully.');
    }

    public function render()
    {
        $hotels = Hotel::where('event_id', $this->eventId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('city', 'like', '%' . $this->search . '%')
                        ->orWhere('contact_person', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount('hotelReservations')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.travel.hotels', [
            'hotels' => $hotels,
        ]);
    }
}
