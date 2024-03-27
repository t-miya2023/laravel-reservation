<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;


class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        //キーワード検索機能
        $query = User::query()
                ->where('name','like',"%$search%")
                ->orwhere('email','like',"%$search%")
                ->orwhere('tel','like',"%$search%")
                ->orwhere('post_code','like',"%$search%")
                ->orwhere('address','like',"%$search%");
        //並び替え機能 （初期値）
        $orderDirection = $request->input('order_direction','desc');
        $query->orderBy('created_at',$orderDirection);

        //ページネーション
        $users = $query->paginate(20);
        
        return view('dashboard.user.index',compact('users'));
    }

    public function edit(User $user)
    {
        return view('dashboard.user.edit',compact('user'));
    }

    public function update(Request $request,User $user)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => ['required','email',Rule::unique('users')->ignore($user->id)],
            'tel' => 'required|numeric',
            'post_code' => 'required|size:7',
            'address' => 'required',
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->tel = $request->input('tel');
        $user->post_code = $request->input('post_code');
        $user->address = $request->input('address');

        $user->save();

        return redirect()->route('dashboard.user.index')->with('update','ユーザー情報は正しく変更されました。');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('dashboard.user.index')->with('update','ユーザー情報は正しく削除されました。');
    }
}
