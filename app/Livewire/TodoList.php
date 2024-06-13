<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required')]
    public $name;
    public $search;

    #[Rule('required')]
    public  $editingNewName;
    public $editingTodoId;

    public function create()
    {
        $validated = $this->validateOnly('name');

        Todo::create($validated);

        $this->reset('name');

        session()->flash('success', 'Created.');

        $this->resetPage();
    }

    public function delete(Todo $todo)
    {
        $todo->delete();
    }

    public function toggle(Todo $todo)
    {
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit(Todo $todo)
    {
        $this->editingTodoId = $todo->id;
        $this->editingNewName = $todo->name;
    }

    public function cancelEdit()
    {
        $this->reset('editingNewName', 'editingTodoId');
    }

    public function update()
    {
        $this->validateOnly('editingNewName');
        Todo::find($this->editingTodoId)->update([
            'name' => $this->editingNewName
        ]);

        $this->cancelEdit();
    }

    public function render()
    {
        $search = '%' . $this->search . '%';  // Escape and build search term
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name', 'like', $search)->paginate(5)
        ]);
    }
}
