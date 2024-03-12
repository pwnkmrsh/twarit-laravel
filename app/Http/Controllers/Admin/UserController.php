<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;

class UserController extends MainAdminController
{
    public function __construct(Request $request)
    {
        if (
            $request->query('userlock')
            || $request->query('userunlock')
            || $request->query('useradmin')
            || $request->query('userunadmin')
            || $request->query('staff')
            || $request->query('unstaff')
            || $request->query('permadelete')
        ) {
            $this->middleware('DemoAdmin', ['only' => ['users']]);
        }

        parent::__construct();
    }

    public function users(Request $request)
    {
        if ($request->query('userlock')) {
            $user = User::findOrFail($request->query('userlock'));
            $user->usertype = 'banned';
            $user->save();
            Session::flash('success.message', trans('admin.Banned'));

            return redirect()->back();
        } elseif ($request->query('userunlock')) {
            $user = User::findOrFail($request->query('userunlock'));
            $user->usertype = null;
            $user->save();
            Session::flash('success.message', trans('admin.Unlocked'));

            return redirect()->back();
        } elseif ($request->query('useradmin')) {
            $user = User::findOrFail($request->query('useradmin'));
            $user->usertype = 'Admin';
            $user->save();
            Session::flash('success.message', trans('admin.ChangesSaved'));

            return redirect()->back();
        } elseif ($request->query('userunadmin')) {
            $user = User::findOrFail($request->query('userunadmin'));
            $user->usertype = null;
            $user->save();
            Session::flash('success.message', trans('admin.Nowuserisnotadmin'));

            return redirect()->back();
        } elseif ($request->query('staff')) {
            $user = User::findOrFail($request->query('staff'));
            $user->usertype = 'Staff';
            $user->save();
            Session::flash('success.message', trans('admin.ChangesSaved'));

            return redirect()->back();
        } elseif ($request->query('unstaff')) {
            $user = User::findOrFail($request->query('unstaff'));
            $user->usertype = null;
            $user->save();
            Session::flash('success.message', trans('admin.ChangesSaved'));

            return redirect()->back();
        } elseif ($request->query('verify')) {
            $user = User::findOrFail($request->query('verify'));
            $user->verified_at = now();
            $user->save();
            Session::flash('success.message', trans('admin.ChangesSaved'));

            return redirect()->back();
        } elseif ($request->query('unverify')) {
            $user = User::findOrFail($request->query('unverify'));
            $user->verified_at = null;
            $user->save();
            Session::flash('success.message', trans('admin.ChangesSaved'));

            return redirect()->back();
        } elseif ($request->query('permadelete')) {
            $user = User::findOrFail($request->query('permadelete'));
            $user->userDelete();
            Session::flash('success.message', trans('admin.Deleted'));

            return redirect()->back();
        }

        $typew = $request->query('only');

        return view('_admin.pages.users')->with(['type' => $typew]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTableData(Request $request)
    {
        $type = $request->query('only');

        $user = DB::table('users');
        $user->select('*');

        if ($type == 'admins') {
            $user->where('usertype', '=', 'Admin');
        } elseif ($type == 'staff') {
            $user->where('usertype', '=', 'Staff');
        } elseif ($type == 'banned') {
            $user->where('usertype', '=', 'banned');
        }

        return Datatables::of($user)
            ->editColumn('icon', function ($user) {
                return '<img src=" '.makepreview($user->icon, 's', 'members/avatar').'" width="55" height="55">';
            })
            ->editColumn('username', function ($user) {
                $out = '<a href="'.route('user.profile', ['user' => $user->username_slug]).'"  target="_blank" > '.$user->username.'  </a>';
                if ($user->verified_at) {
                    $verify = __('Official Verified Account');
                    $out .= '<a href="#" data-toggle="tooltip" data-original-title="'.$verify.'"><svg style="width:16px;height:16px;fill:green" viewBox="0 0 24 24"><g><path d="M22.5 12.5c0-1.58-.875-2.95-2.148-3.6.154-.435.238-.905.238-1.4 0-2.21-1.71-3.998-3.818-3.998-.47 0-.92.084-1.336.25C14.818 2.415 13.51 1.5 12 1.5s-2.816.917-3.437 2.25c-.415-.165-.866-.25-1.336-.25-2.11 0-3.818 1.79-3.818 4 0 .494.083.964.237 1.4-1.272.65-2.147 2.018-2.147 3.6 0 1.495.782 2.798 1.942 3.486-.02.17-.032.34-.032.514 0 2.21 1.708 4 3.818 4 .47 0 .92-.086 1.335-.25.62 1.334 1.926 2.25 3.437 2.25 1.512 0 2.818-.916 3.437-2.25.415.163.865.248 1.336.248 2.11 0 3.818-1.79 3.818-4 0-.174-.012-.344-.033-.513 1.158-.687 1.943-1.99 1.943-3.484zm-6.616-3.334l-4.334 6.5c-.145.217-.382.334-.625.334-.143 0-.288-.04-.416-.126l-.115-.094-2.415-2.415c-.293-.293-.293-.768 0-1.06s.768-.294 1.06 0l1.77 1.767 3.825-5.74c.23-.345.696-.436 1.04-.207.346.23.44.696.21 1.04z"></path></g></svg></a>';
                }
                $out .= '<div class=clear></div>';

                $social_profiles = json_decode($user->social_profiles, true);
                $social_links = collect(config('buzzy.social_links'))->filter(function ($item, $provider) use ($social_profiles) {
                    return ! empty($social_profiles[$provider]);
                });

                foreach ($social_links as $provider => $item) {
                    $out .= '<a href="'.$social_profiles[$provider].'" target="_blank" class="mr-5 mt-5"><img width="26px" src="'.$item['icon'].'" /></a>';
                }

                return $out;
            })
            ->editColumn('email', function ($user) {
                if (auth()->user()->isDemoAdmin()) {
                    return trans('admin.youPERMISSION');
                }

                $output = $user->email;

                if ($user->email_verified_at) {
                    $output .= '<i data-toggle="tooltip" data-original-title="'.__('Verified').'" class="fa fa-check-circle ml-5 text-green"></i>';
                } else {
                    $output .= '<i data-toggle="tooltip" data-original-title="'.__('Not verified').'" class="fa fa-times-circle ml-5 text-gray"></i>';
                }

                return $output;
            })
            ->addColumn('status', function ($user) {
                if ($user->usertype == 'Admin') {
                    return '<div class="label label-default">'.trans('admin.admin').'</div>';
                } elseif ($user->usertype == 'Staff') {
                    return '<div class="label label-warning">'.trans('admin.StaffEditor').'</div>';
                } elseif ($user->usertype == 'banned') {
                    return '<div class="label label-danger">'.trans('admin.Banned').'</div>';
                } else {
                    return '<div class="label label-info">'.trans('admin.Member').'</div>';
                }
            })
            ->addColumn('last_login_at', function ($user) {
                $ip = 'IP: ';
                $ip .= auth()->user()->isDemoAdmin() ? trans('admin.youPERMISSION') : ' ('.$user->last_login_ip.')';

                $out = '<span data-toggle="tooltip" data-original-title="'.$ip.'">';
                $out .= $user->last_login_at ?? '-';
                $out .= '</span>';

                return $out;
            })
            ->addColumn('action', function ($user) {
                $adminbutton = '';
                $staffbutton = '';
                $editbutton = '  <a href="'.route('user.settings', ['user' => $user->username_slug]).'"  target="_blank" class="btn btn-sm btn-success" role="button" data-toggle="tooltip" data-original-title="'.trans('admin.edit').'"><i class="fa fa-edit"></i></a>';
                $deletebutton = ' <a class="btn btn-sm btn-danger permanently" href="?permadelete='.$user->id.'" role="button" data-toggle="tooltip" data-original-title="'.trans('admin.deletepermanently').'"><i class="fa fa-trash"></i></a>';

                if ($user->usertype == 'banned') {
                    $lockbutton = ' <a class="btn btn-sm btn-default permanently" href="?userunlock='.$user->id.'" role="button" data-toggle="tooltip" data-original-title="'.trans('admin.UnlockUser').'"><i class="fa fa-unlock"></i></a>';
                    $adminbutton = $lockbutton.$deletebutton;
                } else {
                    $lockbutton = ' <a class="btn btn-sm btn-danger permanently" href="?userlock='.$user->id.'" role="button" data-toggle="tooltip" data-original-title="'.trans('admin.lockUser').'"><i class="fa fa-lock"></i></a>';

                    if ($user->usertype == 'Admin') {
                        $adminbutton = ' <a class="btn btn-sm btn-default " href="?userunadmin='.$user->id.'" role="button" data-toggle="tooltip" data-original-title="'.trans('admin.NotAdmin').'"><i class="fa fa-remove"></i></a>';
                    } else {
                        $adminbutton = $lockbutton.' <a class="btn btn-sm btn-info " href="?useradmin='.$user->id.'" role="button" data-toggle="tooltip" data-original-title="'.trans('admin.MakeAdmin').'"><i class="fa fa-user-secret"></i></a>';

                        if ($user->usertype == 'Staff') {
                            $staffbutton = ' <a class="btn btn-sm btn-default " href="?unstaff='.$user->id.'" role="button" data-toggle="tooltip" data-original-title="'.trans('admin.NotEditorStaff').'"><i class="fa fa-remove"></i></a>';
                        } else {
                            $staffbutton = ' <a class="btn btn-sm btn-warning" href="?staff='.$user->id.'" role="button" data-toggle="tooltip" data-original-title="'.trans('admin.MakeEditorStaff').'"><i class="fa fa-thumbs-up"></i></a>';
                        }

                        $staffbutton .= $deletebutton;
                    }
                }

                if ($user->verified_at !== null) {
                    $verifybutton = ' <a class="btn btn-sm btn-default " href="?unverify='.$user->id.'" role="button" data-toggle="tooltip" data-original-title="'.__('Unverify User').'"><i class="fa fa-certificate"></i></a>';
                } else {
                    $verifybutton = ' <a class="btn btn-sm btn-warning" href="?verify='.$user->id.'" role="button" data-toggle="tooltip" data-original-title="'.__('Verify User').'"><i class="fa fa-certificate"></i></a>';
                }

                return $editbutton.$adminbutton.$staffbutton.$verifybutton;
            })
            ->escapeColumns(['*'])
            ->make(true);
    }
}
