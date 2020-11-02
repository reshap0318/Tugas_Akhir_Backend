<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Validator, Storage};
use App\Helpers\helper;
use App\Http\Resources\User\{profileCollection};

class userController extends Controller
{
    private $siaCode = 1;
    private $dosenCode = 2;
    private $mahasiswaCode = 3;

    /*
        #login
        -validasi v
        -login ke server local v
        --berhasil > login v
        --gagal > login ke server sia x
        -login ke server sia x
        --berhasil > simpan data id, email?, pass v
        --gagal > gagal x

        note : masing bingun untuk kondisi simpan data, karna sekarang saat user tidak ketemu saat login, data user langsung tersimpan, takutnya nanti, saat yang login data mahasiswa login ke dosen, maka data mahasiswa akan tersimpan ke data dosen, karna datanya dosen belum ada data yang mahasiswa, untuk mengatasinya, mungkin tambahkan role login saat login ke websia, atau perbaiki where login.
    */
 
    public function loginSia(Request $request)
    {
        try {
            return $this->loginUser($request, $this->siaCode);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function loginDosen(Request $request)
    {
        try {
            return $this->loginUser($request, $this->dosenCode);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function loginMahasiswa(Request $request)
    {
        try {
            return $this->loginUser($request, $this->mahasiswaCode);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function loginUser(Request $request, $code_id)
    {
        $validator = Validator::make($request->all(), [
            'login'      => 'required',
            'password'      => 'required',
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }
        
        $login = $request->login;
        $password = $request->password ? $request->password : null;

        $user = user::where(function ($query) use ($code_id) {
            $query->where('role', $code_id);
        })->Where(function($query) use($login) {
            $query->where('id',$login)->orWhere('email',$login);
        })->first();

        if($user){
            $userPass = $user ? $user->password : null;
            if(Hash::check($password, $userPass)){
                $token = $this->changeTokenApi($user);
                if($request->filled('device_id')){
                    helper::updateDeviceId($user->fcm_token,$request->device_id);
                }
                return $this->MessageSuccess(['token' => $token, 'uid' => $user->fcm_token]);
            }else{
                $validator->errors()->add('password','Wrong Password');
                return $this->MessageError($validator->errors(), 422);
            }
        }
        else{
            $id = rand(1000000000,9999999999);
            $name = "Reinaldo Shandev P";
            $user = $this->registerUser($id, $name, $login, $code_id, $password);
            $token = $this->changeTokenApi($user);
            $uid = helper::firebaseCreateUser(['email'=>$login.'@student.unand.ac.id', 'password'=>$request->password]);
            $user->update(['fcm_token'=>$uid]);
            if($request->filled('device_id')){
                helper::updateDeviceId($uid,$request->device_id);
            }
            return $this->MessageSuccess(['token' => $token, 'uid'=>$uid]);
        }
    }

    public function registerUser($id, $name, $email, $code, $pass)
    {
        $user = new User();
        $user->id = $id;
        $user->name = $name;
        $user->email = $email;
        $user->role = $code;
        $user->password = Hash::make($pass);
        $user->save();
        return $user;
    }

    public function changeTokenApi(User $user)
    {
        $apiToken = Str::random(40);
        $user->update([
            'last_login' => Carbon::now(),
            'api_token' => $apiToken,
        ]);
        return $apiToken;
    }

    public function changeAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar'  => 'required|image|mimes:jpg,png,jpeg,gif'
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }

        try {
            if ($request->hasFile('avatar') && $request->avatar->isValid()) {
                $user = app('auth')->user();
                $old_avatar = $user->avatar;
                $fileext = $request->avatar->extension();
                $filename = $user->FileNameAvatar().'.'.$fileext;
                $user->avatar = $request->file('avatar')->storeAs('avatars', $filename);
                $user->update();
                if($old_avatar){
                    Storage::disk('local')->delete($old_avatar);
                }
                return $this->MessageSuccess($user);
            }
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function profile()
    {
        try {
            $user = app('auth')->user();
            $user = new profileCollection($user);
            return $this->MessageSuccess($user);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function changeProfile(Request $request)
    {
        $user = app('auth')->user();
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }

        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->update();
            return $this->MessageSuccess($user);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'          => 'required|string',
            'new_password'          => 'required|string|min:8',
            'confirm_password'      => 'required|string|min:8|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }

        try {
            $user = app('auth')->user();    
            $password = $request->old_password;
            if($user){
                $userPass = $user ? $user->password : null;
                if(Hash::check($password, $userPass)){
                    $user->password = Hash::make($request->new_password);
                    $user->api_token = null; 
                    $user->update();
                    return $this->MessageSuccess($user);
                }else{
                    $validator->errors()->add('old_password','Password not same with old Password');
                    return $this->MessageError($validator->errors(), 422);
                }
            }
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function logout()
    {
        try {
            $user = app('auth')->user();
            $user->api_token = null;        
            $user->update();
            return $this->MessageSuccess($user);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function isLogin()
    {
        try {
            $user = app('auth')->user();
            if($user){
                return $this->MessageSuccess(['isLogin'=>true]);
            }
            return $this->MessageError(['isLogin'=>false],401);
        } catch (\Exception $th) {
            return $this->MessageError(['isLogin'=>false],401);
        }
    }
}
