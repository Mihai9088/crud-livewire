<?php

namespace App\Livewire;

use Exception;
use App\Models\Todo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;

class TodoList extends Component
{
    
    use WithPagination;
    #[Rule('required|min:3|max:20')]
    public $name;
    public $search;

    public $editTodoID;

    #[Rule('required|min:3|max:20')]
     public $editName;


    public function create()
    {
       
         $validated = $this->validateOnly('name');	
      
         Todo::create(
            $validated
        );

        $this->reset('name');
        
        session()->flash('message', 'Todo created successfully.');
        $this->resetPage();
        
    }

    public function delete(Todo $todo)
    {
        try{  $todo->delete();
            session()->flash('message', 'Todo deleted successfully.');

        }
        catch(Exception $e)
        {
            session()->flash('error', 'Failed to delete Todo. ');
            return;
        }
      
    }

    public function toggle(Todo $todo)
    { $todo = Todo::find($todo->id);
      $todo->completed = !$todo->completed;
      $todo->save();
    }

    public function edit (Todo $todo)
    {
        $this->editTodoID = $todo->id;
        $this->editName = $todo->name;
        session()->flash('message', 'Todo updated successfully.');
    }

    public function cancel()
    {

       $this ->reset('editTodoID' , 'editName');
     

    }

    public function update()
    {
        $this->validateOnly('editName');
        Todo::find($this->editTodoID)->update([
            'name' => $this->editName
        ]);
        $this->cancel();
    }
    public function render()
    {
        
        return view('livewire.todo-list' , [
            'todos' => Todo::latest()->where('name' , 'like' , "%{$this->search}%")->paginate(3)
        ]);
    }
}
