<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Entities\User;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!auth()->user()->can('users.manage-admins')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title']       = 'Users';
        $data['page_description'] = 'Individuals accessing content and services via ITN Digital Portal.';
        $data['can_edit']         = auth()->user()->can('users.edit');
        $data['can_delete']       = auth()->user()->can('users.delete');
        $data['can_restore']      = auth()->user()->can('users.restore');
        $data['can_ban']          = auth()->user()->can('users.ban');
        $data['can_unban']        = auth()->user()->can('users.unban');

        return view('backend/users/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('users.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

        $groups = setting('AuthGroups.groups');
        asort($groups);

        $data['page_title']       = "Create a User";
        $data['page_description'] = "Individuals accessing content and services via ITN Digital Portal.";
        $data['groups']           = $groups;

        return view('backend/users/add_user', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('users.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

        $usersModel = auth()->getProvider();
        $validation =  \Config\Services::validation();
        $availableGroups = array_keys(setting('AuthGroups.groups'));
        $selectedGroups = $this->request->getPost('user-groups');
        $selectedGroups = is_array($selectedGroups) ? array_values(array_unique($selectedGroups)) : [];

        // Prepare data to insert            
        $data = $this->request->getPost();
        $data['username'] = null;
        $rules = $validation->getRuleGroup('registration');

        if ($this->validate($rules)) {
            if (empty($selectedGroups)) {
                return redirect()->back()->withInput()->with('error', ['User Role' => 'At least one user role must be selected.']);
            }

            $invalidGroups = array_diff($selectedGroups, $availableGroups);

            if (!empty($invalidGroups)) {
                return redirect()->back()->withInput()->with('error', ['User Role' => 'One or more selected user roles are invalid.']);
            }

            $user = new User();
            $user->fill($data);
            if ($usersModel->save($user)) {
                // To get the complete user object with ID, we need to get from the database
                $user = $usersModel->findById($usersModel->getInsertID());

                $user->syncGroups(...$selectedGroups);

                log_activity('user.created', 'user', $user->id, "Created user '{$user->email}'", [
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
                    'email'      => $user->email,
                    'groups'     => $selectedGroups,
                ]);

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

        return redirect()->back()->withInput()->with($status, $message);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('users.edit')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

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

    public function updateUser()
    {
        if (!auth()->user()->can('users.edit')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

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
                log_activity('user.updated', 'user', (int) $user_id, "Updated user '{$user->email}'", [
                    'first_name' => $this->request->getVar('first_name'),
                    'last_name'  => $this->request->getVar('last_name'),
                    'email'      => $this->request->getVar('email'),
                ]);

                $status = 'success';
                $message = 'The user was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the user!';
            }

            return redirect()->to('/users')->with($status, $message);
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
                log_activity('user.profile_updated', 'user', $user->id, "Updated own profile");

                $status = 'success';
                $message = 'The profile was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the profile!';
            }

            return redirect()->to('/users/profile')->with($status, $message);
        }
    }

    public function deleteUser()
    {
        if (!auth()->user()->can('users.delete')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

        $user_id = $this->request->getVar('user_id');

        // Get the User Provider (UserModel by default)
        $users = auth()->getProvider();

        if (isset($user_id)) {
            // Get user by ID
            $user = $users->findById($user_id);
        }

        if ($users->delete($user->id)) {
            log_activity('user.deleted', 'user', $user->id, "Deleted user '{$user->email}'");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'The user was deleted successfully'
            ])->setStatusCode(200);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting the user'
            ])->setStatusCode(500);
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
        }

        // Get the User Provider (UserModel by default)
        $users = auth()->getProvider();

        // Get current user
        $user = auth()->user();
        $user->fill([
            'password' => $this->request->getVar('password'),
        ]);

        // Insert into database
        if ($users->save($user)) {
            log_activity('user.password_changed', 'user', $user->id, "Changed own password");

            $status = 'success';
            $message = 'The password was changed successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while updating the password!';
        }

        return redirect()->to('/users/profile')->with($status, $message);
    }

    public function changeUserPassword()
    {
        if (!auth()->user()->can('users.edit')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

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
                log_activity('user.password_changed_by_admin', 'user', (int) $user_id, "Admin changed password for user '{$user->email}'");

                $status = 'success';
                $message = 'The password was changed successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the password!';
            }

            return redirect()->to('/users')->with($status, $message);
        }
    }

    public function updateUserGroups()
    {
        if (!auth()->user()->can('users.edit')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

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
                log_activity('user.groups_updated', 'user', (int) $user_id, "Updated groups for user '{$user->email}'", [
                    'groups' => $groups,
                ]);

                $status = 'success';
                $message = 'The user group(s) was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the user group(s)!';
            }

            return redirect()->to('/users')->with($status, $message);
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
        }

        $status = 'error';
        $message = 'You are not allowed to view this page!';
        return redirect()->to('/')->with($status, $message);
    }

    public function banUser()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method. AJAX request required.'
            ])->setStatusCode(400);
        }

        if (!auth()->user()->can('users.ban')) {
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'You are not allowed to do this!']);
        }

        $user_id = $this->request->getVar('user_id');
        $reason  = $this->request->getVar('reason') ?? 'No reason provided.';

        $users = auth()->getProvider();
        $user  = $users->findById($user_id);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found.'])->setStatusCode(404);
        }

        if ($user->isBanned()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User is already banned.'])->setStatusCode(400);
        }

        $user->ban($reason);

        log_activity('user.banned', 'user', $user->id, "Banned user '{$user->email}'", [
            'reason' => $reason,
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'User has been banned successfully.']);
    }

    public function unBanUser()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method. AJAX request required.'
            ])->setStatusCode(400);
        }

        if (!auth()->user()->can('users.unban')) {
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'You are not allowed to do this!']);
        }

        $user_id = $this->request->getVar('user_id');

        $users = auth()->getProvider();
        $user  = $users->findById($user_id);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found.'])->setStatusCode(404);
        }

        if (!$user->isBanned()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User is not currently banned.'])->setStatusCode(400);
        }

        $user->unBan();

        log_activity('user.unbanned', 'user', $user->id, "Unbanned user '{$user->email}'");

        return $this->response->setJSON(['status' => 'success', 'message' => 'User has been unbanned successfully.']);
    }

    public function restoreUser()
    {
        // Ensure AJAX request
        if (!$this->request->isAjax()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method. AJAX request required.'
            ])->setStatusCode(400);
        }

        if (!auth()->user()->can('users.delete')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'You are not allowed to do this!'
                ]);
        }

        $user_id = $this->request->getVar('user_id');

        // Check if user exists and is soft deleted
        $user = $this->userModel->onlyDeleted()->find($user_id);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found or not deleted'])->setStatusCode(404);
        }

        // Restore the user
        if ($this->userModel->restoreUser($user_id)) {
            log_activity('user.restored', 'user', (int) $user_id, "Restored deleted user (id: {$user_id})");

            return $this->response->setJSON(['status' => 'success', 'message' => 'User restored successfully']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to restore user'])->setStatusCode(500);
        }
    }
}
