<?php

namespace Modules\Users\Controllers;

use App\Libraries\CommonLibrary;
use CodeIgniter\I18n\Time;
use JasonGrimes\Paginator;
use Modules\Users\Models\UserscrudModel;

class UserController extends \Modules\Backend\Controllers\BaseController
{
    /**
     * @var UserscrudModel
     */
    protected $userModel;

    /**
     *
     */
    public function __construct()
    {
        $this->userModel = new UserscrudModel();
    }

    /**
     * @return string
     */
    public function users()
    {
        $this->defData['timeClass'] = new Time();
        $totalItems = $this->commonModel->count('users', ['group_id!=' => 1, 'deleted_at' => null]);
        $itemsPerPage = 20;
        $currentPage = $this->request->getUri()->getSegment('3', 1);
        $urlPattern = '/backend/users/(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
        $paginator->setMaxPagesToShow(5);
        $this->defData['paginator'] = $paginator;
        $bpk = ($this->request->getUri()->getSegment(3, 1) - 1) * $itemsPerPage;
        $this->defData['userLists'] = $this->userModel->userList(
            $itemsPerPage,
            'users.id,email,firstname,surname,status,auth_groups.name,black_list_users.notes,reset_expires',
            [/* 'group_id!=' => 1,  */'deleted_at' => null],
            $bpk
        );
        return view('Modules\Users\Views\usersCrud\users', $this->defData);
    }

    /**
     * @return string
     */
    public function create_user()
    {
        if ($this->request->is('post')) {
            $valData = ([
                'firstname' => ['label' => 'Full Name', 'rules' => 'required'],
                'surname' => ['label' => 'Full Name', 'rules' => 'required'],
                'email' => ['label' => 'Email Address', 'rules' => 'required|valid_email'],
                'group' => ['label' => 'Permission', 'rules' => 'required'],
                'password' => ['label' => 'Password', 'rules' => 'required|min_length[8]']
            ]);

            if ($this->validate($valData) == false) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

            if ($this->commonModel->isHave('users', ['email' => $this->request->getPost('email')]) === 1) return redirect()->back()->withInput()->with('errors', ['E-posta adresi daha önce kayıt edilmiş lütfen üye listesini kontrol ediniz.']);
            $data = [
                'email' => $this->request->getPost('email'),
                'firstname' => $this->request->getPost('firstname'),
                'surname' => $this->request->getPost('surname'),
                'activate_hash' => $this->authLib->generateActivateHash(),
                'password_hash' => $this->authLib->setPassword($this->request->getPost('password')),
                'status' => 'deactive',
                'group_id' => $this->request->getPost('group'),
                'created_at' => new Time('now'),
                'who_created' => (int)session()->get('logged_in')
            ];
            $result = $this->commonModel->create('users', $data);
            $auth_users_permissions = [
                [
                    'page_id' => 1,
                    'create_r' => true,
                    'update_r' => true,
                    'read_r' => true,
                    'delete_r' => true,
                    'who_perm' => !empty($this->defData['logged_in_user']->id) ? $this->defData['logged_in_user']->id : NULL,
                    'created_at' => new Time('now'),
                    'user_id' => $result
                ],
                [
                    'page_id' => 9,
                    'create_r' => true,
                    'update_r' => true,
                    'read_r' => true,
                    'delete_r' => true,
                    'who_perm' => !empty($this->defData['logged_in_user']->id) ? $this->defData['logged_in_user']->id : NULL,
                    'created_at' => new Time('now'),
                    'user_id' => $result
                ],
                [
                    'page_id' => 10,
                    'create_r' => true,
                    'update_r' => true,
                    'read_r' => true,
                    'delete_r' => true,
                    'who_perm' => !empty($this->defData['logged_in_user']->id) ? $this->defData['logged_in_user']->id : NULL,
                    'created_at' => new Time('now'),
                    'user_id' => $result
                ]
            ];
            $this->commonModel->createMany('auth_users_permissions', $auth_users_permissions);
            if ((bool)$result == false) return redirect()->back()->withInput()->with('error', 'Kullanıcı oluşturulamadı.');
            $commonLibrary = new CommonLibrary();
            $mailResult = $commonLibrary->phpMailer(
                'noreply@' . $_SERVER['HTTP_HOST'],
                'noreply@' . $_SERVER['HTTP_HOST'],
                [['mail' => $this->request->getPost('email')]],
                'noreply@' . $_SERVER['HTTP_HOST'],
                'Information',
                'Membership Activation',
                'Your membership has been created by the company administrator. To activate your membership, please <a href="' . site_url('backend/activate-account/' . $data['activate_hash']) . '"><b>Click Here</b></a> to access the link shared with you <b>email</b> ve <b>password</b> You can log in with <br>E-mail adress : ' . $this->request->getPost('email') . '<br>Your password: ' . $this->request->getPost('password')
            );
            if ($mailResult === true) return redirect()->route('users', [1])->with('message', lang('Auth.activationSuccess'));
            else return redirect()->back()->withInput()->with('error', $mailResult);
        }
        $this->defData['groups'] = $this->commonModel->lists('auth_groups');
        $this->defData['authLib'] = $this->authLib;
        return view('Modules\Users\Views\usersCrud\createUser', $this->defData);
    }

    /**
     * @param $id
     * @return string
     */
    public function update_user(int $id)
    {
        if ($this->request->is('post')) {
            $valData = ([
                'firstname' => ['label' => 'Full Name', 'rules' => 'required'],
                'surname' => ['label' => 'Full Name', 'rules' => 'required'],
                'email' => ['label' => 'Email Address', 'rules' => 'required|valid_email'],
                'group' => ['label' => 'Permission', 'rules' => 'required']
            ]);

            if ($this->request->getPost('password')) $valData['password'] = ['label' => 'Password', 'rules' => 'required|min_length[8]'];

            if ($this->validate($valData) == false) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

            $data = [
                'email' => $this->request->getPost('email'),
                'firstname' => $this->request->getPost('firstname'),
                'surname' => $this->request->getPost('surname'),
                'status' => 'deactive',
                'force_pass_reset' => false,
                'group_id' => $this->request->getPost('group'),
                'created_at' => new Time('now'),
                'who_created' => session()->get('logged_in')
            ];
            if ($this->request->getPost('password')) $data['password_hash'] = $this->authLib->setPassword($this->request->getPost('password'));

            $result = (string)$this->commonModel->edit('users', $data, ['id' => $id]);
            if ((bool)$result == false) return redirect()->back()->withInput()->with('error', 'User could not be created.');
            else return redirect()->route('users', [1])->with('message', 'Membership Updated.');
        }
        $this->defData['groups'] = $this->commonModel->lists('auth_groups');
        $this->defData['authLib'] = $this->authLib;
        $this->defData['userInfo'] = $this->commonModel->selectOne('users', ['id' => $id]);
        return view('Modules\Users\Views\usersCrud\updateUser', $this->defData);
    }

    /**
     * @param string $id
     */
    public function user_del(string $id)
    {
        if ($this->commonModel->edit('users', ['deleted_at' => date('Y-m-d H:i:s'), 'status' => 'deleted'], ['id' => $id]) === true) return redirect()->route('users', [1])->with('message', 'Membership Deleted.');
        else
        return redirect()->route('users', [1])->with('error', 'Membership could not be deleted.');
    }

    /**
     * @return string
     */
    public function profile()
    {
        if ($this->request->is('post')) {
            $valData = ([
                'firstname' => ['label' => 'Full Name', 'rules' => 'required'],
                'surname' => ['label' => 'Full Name', 'rules' => 'required'],
                'email' => ['label' => 'Email Address', 'rules' => 'required|valid_email'],
            ]);

            if ($this->request->getPost('password')) $valData['password'] = ['label' => 'Şifre', 'rules' => 'required|min_length[8]'];

            if ($this->validate($valData) == false) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

            $user = $this->commonModel->selectOne('users', ['id' => session()->get('logged_in')], 'email');

            $data = [
                'email' => $this->request->getPost('email'),
                'firstname' => $this->request->getPost('firstname'),
                'surname' => $this->request->getPost('surname')
            ];

            if ($this->request->getPost('password')) $data['password_hash'] = $this->authLib->setPassword($this->request->getPost('password'));

            if ($user->email != $data['email']) {
                if ($this->commonModel->isHave('users', ['id!=' => $user->id, 'email' => $this->request->getPost('email')]) === 1) return redirect()->back()->withInput()->with('error', 'This email address has already been used by another user. Please check your information.');

                $data['activate_hash'] = $this->authLib->generateActivateHash();
                $data['status'] = 'deactive';

                $result = $this->commonModel->edit('users', $data, ['id' => $user->id]);

                if ((bool)$result == true) {
                    $commonLibrary = new CommonLibrary();
                    $mailResult = $commonLibrary->phpMailer(
                        'noreply@' . $_SERVER['HTTP_HOST'],
                        'noreply@' . $_SERVER['HTTP_HOST'],
                        ['mail' => $this->request->getPost('email')],
                        'noreply@' . $_SERVER['HTTP_HOST'],
                        'Information',
                        'Email Activation',
                        'You have updated your email. Please <a href="' . site_url('backend/activate-email/' . $data['activate_hash']) . '"><b>Click here</b></a> to activate your new email address.'
                    );
                    if ($mailResult === true) return redirect()->route('users', [1])->with('message', 'Please activate your new email address.');
                    else return redirect()->back()->withInput()->with('error', $mailResult);
                }
            } else $result = $this->commonModel->edit('users', $data, ['id' => session()->get('logged_in')]);

            if ((bool)$result == false) return redirect()->back()->withInput()->with('error', 'Profile could not be updated.');
            else return redirect()->back()->withInput()->with('message', 'Profile Updated.');
        }
        $this->defData['user'] = $this->commonModel->selectOne('users', ['id' => session()->get('logged_in')], 'email,firstname,surname');
        return view('Modules\Users\Views\usersCrud\profile', $this->defData);
    }

    /**
     * @return false|string|string[]
     */
    public function ajax_blackList_post()
    {
        if ($this->request->isAJAX()) {
            $valData = (['note' => ['label' => 'Note', 'rules' => 'required'], 'uid' => ['label' => 'User ID', 'rules' => 'required']]);
            if ($this->validate($valData) == false) return $this->validator->getErrors();
            $result = [];
            if ($this->commonModel->isHave('black_list_users', ['blacked_id' => $this->request->getPost('uid')]) === 0) $bid = $this->commonModel->create('black_list_users', ['blacked_id' => $this->request->getPost('uid'), 'who_blacklisted' => session()->get('logged_in'), 'notes' => $this->request->getPost('note'), 'created_at' => new Time('now')]);
            else $result = ['result' => true, 'error' => ['type' => 'warning', 'message' => 'The membership has already been added to the blacklist.']];

            if (!empty($bid) && $this->commonModel->edit('users', ['status' => 'banned', 'statusMessage' => $this->request->getPost('note')], ['id' => $this->request->getPost('uid')])) $result = ['result' => true, 'error' => ['type' => 'success', 'message' => 'The membership has been added to the blacklist']];
            else $result = ['result' => true, 'error' => ['type' => 'danger', 'message' => 'Membership could not be added to the blacklist.']];

            return $this->respond($result, 200);
        } else return $this->failForbidden();
    }

    public function ajax_remove_from_blackList_post()
    {
        if ($this->request->isAJAX()) {
            $valData = (['uid' => ['label' => 'Kullanıcı id', 'rules' => 'required']]);

            if ($this->validate($valData) == false) return $this->validator->getErrors();

            $result = [];

            $pwd = $this->authLib->randomPassword();
            $data = [
                'password_hash' => $this->authLib->setPassword($pwd),
                'status' => 'deactive',
                'activate_hash' => $this->authLib->generateActivateHash(),
                'statusMessage' => null
            ];
            if ($this->commonModel->update('users', $data, ['id' => $this->request->getPost('uid')]) && $this->commonModel->deleteOne('black_list_users', ['blacked_id' => $this->request->getPost('uid')])) {
                $user = $this->commonModel->selectOne('users', ['id' => $this->request->getPost('uid')], 'email');

                $commonLibrary = new CommonLibrary();
                $mailResult = $commonLibrary->phpMailer(
                    'noreply@' . $_SERVER['HTTP_HOST'],
                    'noreply@' . $_SERVER['HTTP_HOST'],
                    ['mail' => $user->email],
                    'noreply@' . $_SERVER['HTTP_HOST'],
                    'Information',
                    'Membership Reactivation',
                    'To reactivate your membership, the administrator has intervened. To activate your membership, please <a href="' . site_url('backend/activate-account/' . $data['activate_hash']) . '"><b>Click Here</b></a> to access the link shared with you <b>email</b> ve <b>Password</b> to log in <br>E-mail adresi : ' . $user->email . '<br>Password : ' . $pwd
                );
                if ($mailResult === true) $result = ['result' => true, 'error' => ['type' => 'success', 'message' => $user->email . ' TheUser with the email address has been removed from the blacklist.']];
                else $result = ['result' => false, 'error' => ['type' => 'danger', 'message' => $mailResult]];
            } else $result = ['result' => false, 'error' => ['type' => 'danger', 'message' => 'Membership could not be removed from the blacklist.']];

            return $this->response->setJSON($result);
        } else return $this->failForbidden();
    }

    public function ajax_force_reset_password()
    {
        if ($this->request->isAJAX()) {
            $valData = (['uid' => ['label' => 'User id', 'rules' => 'required']]);

            if ($this->validate($valData) == false) return $this->validator->getErrors();

            $result = [];

            if ($this->commonModel->edit('users', ['status' => 'deactive', 'reset_hash' => $this->authLib->generateActivateHash(), 'reset_expires' => date('Y-m-d H:i:s', time() + $this->config->resetTime)], ['id' => $this->request->getPost('uid')])) {
                $user = $this->commonModel->selectOne('users', ['id' => $this->request->getPost('uid')]);
                $commonLibrary = new CommonLibrary();
                $mailResult = $commonLibrary->phpMailer(
                    'noreply@' . $_SERVER['HTTP_HOST'],
                    'noreply@' . $_SERVER['HTTP_HOST'],
                    ['mail' => $user->email],
                    'noreply@' . $_SERVER['HTTP_HOST'],
                    'Information',
                    'Password Reset Request',
                    'Your membership password has been reset by an authorized person. Your password reset request ' . date('d-m-Y H:i:s', strtotime($user->reset_expires)) . ' is valid until the date. Please set your new password by <a href="' . site_url('backend/reset-password/' . $user->reset_hash) . '"><b>Click here </b></a> to access the link shared with you.'
                );
                if ($mailResult === true) $result = ['result' => true, 'error' => ['type' => 'success', 'message' => $user->email . ' The password reset request has been sent to the email address.']];
                else $result = ['result' => false, 'error' => ['type' => 'danger', 'message' => $mailResult]];
            } else $result = ['result' => false, 'error' => ['type' => 'danger', 'message' => 'Password reset request could not be sent.']];

            return $this->response->setJSON($result);
        } else return $this->failForbidden();
    }
}
