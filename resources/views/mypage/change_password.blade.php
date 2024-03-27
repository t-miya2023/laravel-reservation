@extends('layouts.app')

@section('content')



<div class="container">

    <h2 class="mb-3 heading-2">パスワード変更</h2>
        @if(session('caution'))
            <div class="alert alert-danger">
                {{ session('caution') }}
            </div>
        @endif
    <div class="row justify-content-center">
        <div class="col-6">
            <form method="POST" action="{{ route('mypage.update_password', $user->id) }}">
                @csrf
                @method('PATCH')
                <label for="currentpassword" class="mb-2">現在のパスワード</label>
                <input type="password" name="currentpassword" class="form-control mb-3">
                <label for="password" class="mb-2">新しいパスワード</label>
                <input type="password" name="password" class="form-control mb-3">
                <label for="confirmpassword" class="mb-2">新しいパスワード(確認用)</label>
                <input type="password" name="confirmpassword" class="form-control mb-3">
                    @error('password')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                <button type="submit" class="btn btn-primary">更新</button>
                <a href="{{route('mypage.index')}}" class="btn btn-warning mx-5">戻る</a>
            </form>
        </div>

    </div>
</div>

@endsection