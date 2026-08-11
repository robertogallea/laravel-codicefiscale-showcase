<?php

namespace App\Livewire\People;

use App\Models\Person;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PeopleIndex extends Component
{
    use WithPagination;

    public function render(): View
    {
        return view('livewire.people.people-index', [
            'people' => $this->people(),
        ]);
    }

    /** @return LengthAwarePaginator<int, Person> */
    private function people(): LengthAwarePaginator
    {
        return Person::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10);
    }
}
