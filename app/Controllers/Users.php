<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IDUserModel;
use CodeIgniter\Shield\Entities\User;

class Users extends BaseController
{
    public function index()
    {
        if (!auth()->user()->can('users.access')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Users";
            $data['page_description'] = "Individuals accessing content and services via ITN Digital Portal.";

            return view('backend/users/index', $data);
        }
    }

    public function create()
    {
        if (!auth()->user()->can('users.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Create a User";
            $data['page_description'] = "Individuals accessing content and services via ITN Digital Portal.";

            return view('backend/users/add_user', $data);
        }
    }

    public function store()
    {
        if (!auth()->user()->can('users.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            // $user = new User();
            $usersModel = auth()->getProvider();
            $validation =  \Config\Services::validation();

            // Prepare data to insert            
            $data = $this->request->getPost();
            $rules = $validation->getRuleGroup('registration');

            if ($this->validate($rules)) {
                $user = new User();
                $user->fill($data);
                if ($usersModel->save($user)) {
                    // To get the complete user object with ID, we need to get from the database
                    $user = $usersModel->findById($usersModel->getInsertID());

                    // Add to default group
                    $usersModel->addToDefaultGroup($user);

                    $status = 'success';
                    $message = 'The user was addded successfully!';
                } else {
                    $status = 'error';
                    $message = 'There was an error while adding the user!';
                }
            } else {
                $status = 'error';
                $message = $this->validator->getErrors();
            }

            return redirect()->to('/users')->with($status, $message);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('users.edit')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            // Get the User Provider (UserModel by default)
            $users = auth()->getProvider();

            $user = $users->findbyId($id);

            if (isset($user) && !empty($user)) {
                $groups = setting('AuthGroups.groups');
                asort($groups);

                $data['user'] = $user;
                $data['groups'] = $groups;
                $data['user_groups'] = $user->getGroups();
                $userName = $user->first_name;

                $data['page_title'] = "Edit User - " . $userName;
                $data['page_description'] = "Individuals accessing content and services via ITN Digital Portal.";

                return view('backend/users/edit_user', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
    }

    public function updateProfile()
    {
        // Get the User Provider (UserModel by default)
        $users = auth()->getProvider();

        $user = auth()->user();

        if (isset($user)) {
            $user->fill([
                'first_name' => $this->request->getVar('first_name'),
                'last_name' => $this->request->getVar('last_name'),
                'email' => $this->request->getVar('email'),
            ]);

            // Insert into database
            if ($users->save($user)) {
                $status = 'success';
                $message = 'The profile was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the profile!';
            }

            return redirect()->to('/users/profile')->with($status, $message);
        }
    }

    public function updateUser()
    {
        if (!auth()->user()->can('users.edit')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $user_id = $this->request->getVar('user_id');

            // Get the User Provider (UserModel by default)
            $users = auth()->getProvider();

            if (isset($user_id)) {
                // Get user by ID
                $user = $users->findById($user_id);
            }

            if (isset($user)) {
                $user->fill([
                    'first_name' => $this->request->getVar('first_name'),
                    'last_name' => $this->request->getVar('last_name'),
                    'email' => $this->request->getVar('email'),
                ]);

                // Insert into database
                if ($users->save($user)) {
                    $status = 'success';
                    $message = 'The user was updated successfully!';
                } else {
                    $status = 'error';
                    $message = 'There was an error while updating the user!';
                }

                return redirect()->to('/users')->with($status, $message);
            }
        }
    }

    public function deleteUser()
    {
        if (!auth()->user()->can('users.edit')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $user_id = $this->request->getVar('user_id');

            // Get the User Provider (UserModel by default)
            $users = auth()->getProvider();

            if (isset($user_id)) {
                // Get user by ID
                $user = $users->findById($user_id);
            }

            if ($users->delete($user->id, true)) {
                echo json_encode(['code' => 1, 'message' => 'The user was deleted successfully']);
            } else {
                echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the user']);
            }
        }
    }

    public function changePassword()
    {
        $credentials = [
            'email'    => auth()->user()->email,
            'password' => $this->request->getPost('current-password'),
        ];

        $validCreds = auth()->check($credentials);

        if (!$validCreds->isOK()) {
            $status = 'error';
            $message = 'Current Password is Incorrect or Required';

            return redirect()->to('/users/profile')->with($status, $message);
        } else {
            // Get the User Provider (UserModel by default)
            $users = auth()->getProvider();

            // Get current user
            $user = auth()->user();
            $user->fill([
                'password' => $this->request->getVar('password'),
            ]);

            // Insert into database
            if ($users->save($user)) {
                $status = 'success';
                $message = 'The password was changed successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the password!';
            }

            return redirect()->to('/users/profile')->with($status, $message);
        }
    }

    public function changeUserPassword()
    {
        if (!auth()->user()->can('users.edit')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $user_id = $this->request->getVar('user_id');

            // Get the User Provider (UserModel by default)
            $users = auth()->getProvider();

            if (isset($user_id)) {
                // Get user by ID
                $user = $users->findById($user_id);
            }

            if (isset($user)) {
                $user->fill([
                    'password' => $this->request->getVar('password'),
                ]);

                // Insert into database
                if ($users->save($user)) {
                    $status = 'success';
                    $message = 'The password was changed successfully!';
                } else {
                    $status = 'error';
                    $message = 'There was an error while updating the password!';
                }

                return redirect()->to('/users')->with($status, $message);
            }
        }
    }

    public function updateUserGroups()
    {
        if (!auth()->user()->can('users.edit')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $user_id = $this->request->getVar('user_id');
            $groups = $this->request->getVar('user-groups');

            // Get the User Provider (UserModel by default)
            $users = auth()->getProvider();

            if (isset($user_id)) {
                // Get user by ID
                $user = $users->findById($user_id);
            }

            if (isset($user)) {
                // Insert into database
                if ($user->syncGroups(...$groups)) {
                    $status = 'success';
                    $message = 'The user group(s) was updated successfully!';
                } else {
                    $status = 'error';
                    $message = 'There was an error while updating the user group(s)!';
                }

                return redirect()->to('/users')->with($status, $message);
            }
        }
    }

    public function profile()
    {
        $user = auth()->user();
        if (isset($user)) {
            $data['user'] = $user;
            $data['page_title'] = "Profile";
            $data['page_description'] = "Details about your personal information.";

            return view('backend/users/profile', $data);
        } else {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->to('/')->with($status, $message);
        }
    }

    public function restoreUser()
    {
        if (!auth()->user()->can('users.delete-admins')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'You are not allowed to do this!'
                ]);
        }

        if ($this->request->isAjax()) {
            $user_id = $this->request->getVar('user_id');

            $userModel = new IDUserModel();

            // Check if user exists and is soft deleted
            $user = $userModel->onlyDeleted()->find($user_id);

            if (!$user) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'User not found or not deleted'])->setStatusCode(404);
            }

            // Restore the user
            if ($userModel->restoreUser($user_id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'User restored successfully']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to restore user'])->setStatusCode(500);
            }
        }
    }
}
