@extends('layouts.admin')
@section('content')

<div class="container">
    @if(session('update'))
        <div class="alert alert-success">
            {{ session('update') }}
        </div>
    @endif
    
    <h2 class="mb-3">ユーザー情報一覧</h2>
    <a class="btn btn-warning mb-3" href="{{route('dashboard.index')}}">ダッシュボードに戻る</a>
    <div class="row">
        <h3>ユーザー検索</h3>
            <form class="mb-5" id="search-form" action="{{ route('dashboard.user.index') }}" method="GET">
                <div class="form-group">
                    <label for="search">検索キーワード：</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}">
                </div>
                <div class="form-group mb-3">
                    <label for="order_by">並び替え：</label>
                    <select name="order_direction" id="order_direction" class="form-control">
                        <option value="desc" {{ request('order_direction') === 'desc' ? 'selected' : '' }}>新着順</option>
                        <option value="asc" {{ request('order_direction') === 'asc' ? 'selected' : '' }}>古い順</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">絞り込む</button>
                <button type="button" class="btn btn-danger mb-2" id="reset-btn">リセット</button>
            </form>
            <div class="table-responsive">
                <table class="user-table border border-info  border-3 p-3 mb-3 bg-white text-center table">
                    <tr class="border-bottom ">
                        <th>ユーザーID</th>
                        <th>氏名</th>
                        <th>メールアドレス</th>
                        <th>電話番号</th>
                        <th>郵便番号</th>
                        <th>住所</th>
                        <th>編集</th>
                        <th>削除</th>
                    </tr>
                    @foreach($users as $user)
                    <tr class="border-bottom">
                        <td>{{ $user->id }}</td>
                        <td >{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->tel }}</td>
                        <td>{{ $user->post_code }}</td>
                        <td>{{ $user->address }}</td>
                        <td><a href="{{ route('dashboard.user.edit',$user->id) }}"><i class="bi bi-pencil"></i></a></td>
                        <td>
                            @include('dashboard.user.delete')  
                            <span type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                <i class="bi bi-trash"></i>
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            {{ $users->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }} <!-- ページネーションリンクを表示 -->
    </div>
</div>

@endsection
@section('script')
<script src="{{asset('js/reset-btn.js')}}"></script>
@endsection