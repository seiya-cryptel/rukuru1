<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User as Users;

class User extends Component
{
    public $users;
    public $name, $email, $password;
    public $userId, $updateUser = false, $addUser = false;

    /**
     * delete action listener
     */
    protected $listeners = [
        'deleteUserListener' => 'deleteUser'
    ];

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required'
    ];

    /**
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
    }

    /**
     * render the user data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $this->users = Users::select('id', 'name', 'email', 'password')->get();
        return view('livewire.user');
    }

    /**
     * Open Add User form
     * @return void
     */
    public function newUser()
    {
        $this->resetFields();
        $this->addUser = true;
        $this->updateUser = false;
    }

    /**
     * store the user input post data in the users table
     * @return void
     */
    public function storeUser()
    {
        $this->validate();
        try {
            Users::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password
            ]);
            $this->resetFields();
            $this->addUser = false;
            session()->flash('success', __('Create'). ' ' . __('Done'));
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong.'));
        }
    }

    /**
     * show existing user data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editUser($id) {
        $user = Users::findOrFail($id);
        if(!$user) {
            session()->flash('error', __('Not Found'));
        }
        else {
            $this->userId = $id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->password = $user->password;
            $this->updateUser = true;
            $this->addUser = false;
        }
    }

    /**
     * update the user data
     * @return void
     */
    public function updateUser() {
        $this->validate();
        try {
            Users::where('id', $this->userId)->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password
            ]);
            session()->flash('success', __('Update'). ' ' . __('Done'));
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong.'));
        }
    }

    /**
     * Cancel add/edit form and redirect to the user list
     * @return void
     */
    public function cancelUser() {
        $this->resetFields();
        $this->addUser = false;
        $this->updateUser = false;
    }

    /**
     * delete specific user data
     * @param mixed $id
     * @return void
     */
    public function deleteUser($id) {
        try {
            Users::where('id', $id)->delete();
            session()->flash('success', __('Delete'). ' ' . __('Done'));
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong.'));
        }
    }

}
