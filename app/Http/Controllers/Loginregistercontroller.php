<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Model\Buku;
use App\Models\User;



class LoginRegisterController extends Controller
{
    public function login(Request $request)
    {
        return view('auth.login');
    }
    public function register()
    {
        return view('auth.register');
    }

    public function adminHome(Request $request) {
        $search = $request->input('search');

        $data = User::where('level', 'admin')->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })->paginate(5);
        return view('admin.home', compact('data'));
        }

    public function userHome() {
        return view('user.home');
    }

    public function postRegister(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'jenisKelamin' => 'required',
            'password' => 'required|min:8|max:20|confirmed'
            ]);
        $user = new User;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->jenis_kelamin = $request->jenisKelamin;
        $user->password = Hash::make($request->password);

        $user->save();
        if($user){
        return redirect('/auth/login')->with('success', 'Akun berhasil dibuat, silahkan melakukan proses login!');
        } else {
        return back()->with('failed', 'Maaf, terjadi kesalahan, coba kembali beberapa saat!');
        }
    }


    public function postLogin(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|max:20'
            ]);

            if(Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                if ($user->level == 'user') {
                    return redirect('/user/home');
                } elseif ($user->level == 'admin') {
                    return redirect('/admin/home');
                }
        }
                return back()->with('failed', 'Maaf, terjadi kesalahan, coba kembali beberapa saat!');
}
    public function logout() {
    Auth::logout();
        return redirect('/');
}
}