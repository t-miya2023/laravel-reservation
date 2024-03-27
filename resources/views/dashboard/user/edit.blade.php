@extends('layouts.admin')

@section('content')

<div class="container">

    <h2 class="mb-3">アカウント情報編集</h2>
    <div class="row justify-content-center">
        <div class="col-6">
            <form method="POST" action="{{ route('dashboard.user.update',$user->id) }}">
                @csrf
                @method('PATCH')
                
                <label for="name" class="mb-2">氏名</label>
                <input type="text" id="name" name="name" value="{{$user->name}}" class="form-control mb-3">
                    @error('name')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror

                <label for="email" class="mb-2">メールアドレス</label>
                <input type="text" id="email" name="email" value="{{$user->email}}" class="form-control mb-3">
                    @error('email')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                <label for="tel" class="mb-2">電話番号</label>
                <input type="text" id="tel" name="tel" value="{{$user->tel}}" class="form-control mb-3">
                    @error('tel')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                <label for="post_code" class="mb-2">郵便番号</label>
                <input type="text" id="post_code" name="post_code" value="{{$user->post_code}}" class="form-control mb-3">
                    @error('post_code')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                <label for="address" class="mb-2">住所</label>
                <input type="text" id="address" name="address" value="{{$user->address}}" class="form-control mb-3">
                    @error('address')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror

                <button type="submit" class="btn btn-primary">更新</button>
                <a href="{{route('dashboard.user.index')}}" class="btn btn-warning mx-5">戻る</a>
            </form>
        </div>

    </div>
</div>

@endsection