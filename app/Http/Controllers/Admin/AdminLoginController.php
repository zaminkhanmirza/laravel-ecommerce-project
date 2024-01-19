<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    Public function index() {
        return view('admin.login');
    }

    Public function authenticate(Request $request) {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                $admin = Auth::guard('admin')->user();
                if ($admin->role == 1) {
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not authorized to access Admin Panel...!!')->withInput($request->only('email'));
                } else {
                return redirect()->route('admin.dashboard');
                }
            } else {
                return redirect()->route('admin.login')->with('error', 'Provided Email/Password is not correct, Please fill correct details')->withInput($request->only('email'));
            }
        } else {
            return redirect()->route('admin.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }
}
