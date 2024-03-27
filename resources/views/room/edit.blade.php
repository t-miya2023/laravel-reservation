@extends('layouts.admin')
@section('content')
<div class="container">
    <h2 class="mb-3">部屋情報登録</h2>
    <div class="row justify-content-center">
        <div class="col-10 d-flex flex-column">
            <form method="POST" action="{{ route('room.update',$room->id) }}" enctype='multipart/form-data'>
                @csrf
                @method('PATCH')
                <div class="mb-3 col-12">
                    <label class="form-label" for="name">部屋名</label>
                    <input class="form-control" type="text" name="name" value="{{ $room->name }}">
                    @error('room_info')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-3 col-12">
                    <label class="form-label" for="detail">部屋情報詳細</label>
                    <textarea class="form-control" name="detail"  cols="30" rows="5">{{ $room->detail }}</textarea>
                    @error('room_detail')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                        <label class="form-label" for="capacity">最大収容人数</label>
                        <input class="form-control" type="number" name="capacity" value="{{ $room->capacity }}" min="1">
                </div>

                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label" for="price">宿泊価格(税抜)</label>
                        <div class="d-flex align-items-center">
                            <input class="form-control" type="number" step="1" name="price"  min="0" value="{{ $room->price }}">
                            <span class="mx-1">円</span>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label" for="tax">税率</label>
                        <div class="d-flex align-items-center">
                            <input class="form-control" type="number" step="1" name="tax"  min="0" value="{{ $room->tax }}">
                            <span class="mx-1">％</span>
                        </div>
                    </div>
                    @error('price')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                    @error('tax')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label" for="dinner_fee">夕食代(税抜)</label>
                        <div class="d-flex align-items-center">
                            <input class="form-control" type="number" name="dinner_fee"  min="0" value="{{ $room->dinner_fee }}">
                            <span class="mx-1">円</span>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label" for="breakfast_fee">朝食代(税抜)</label>
                        <div class="d-flex align-items-center">
                            <input class="form-control" type="number" name="breakfast_fee"  min="0" value="{{ $room->breakfast_fee }}">
                            <span class="mx-1">円</span>
                        </div>
                    </div>
                    @error('dinner_fee')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                    @error('breakfast_fee')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label" for="bed_size">ベッドサイズ</label>
                        <input class="form-control" type="text" name="bed_size" value="{{ $room->price }}">
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label" for="smorking">喫煙の可否</label>
                        <select class="form-control" name="smorking">
                            <option value="禁煙" {{ $room->smorking === '禁煙' ? 'selected' : '' }}>禁煙</option>
                            <option value="喫煙可" {{ $room->smorking === '喫煙可' ? 'selected' : '' }}>喫煙可</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 col-12">
                    <label class="form-label" for="facility">設備</label>
                    <textarea class="form-control" name="facility"  cols="30" rows="3">{{ $room->facility }}</textarea>
                </div>

                <div class="mb-3 col-12">
                    <label class="form-label" for="amenities">アメニティ</label>
                    <textarea class="form-control" name="amenities" cols="30" rows="3">{{ $room->amenities }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="img" class="form-label">部屋の画像</label>
                    <input class="form-control" type="file" name="img" multiple>
                </div>
                
                <div class="mb-5">
                    <label for="status" class="form-label">利用状況</label>
                    <select name="status" class="form-control">
                        <option value="0" {{ $room->status === 0 ? 'selected' : '' }}>利用可能</option>
                        <option value="1" {{ $room->status === 1 ? 'selected' : '' }}>利用不可</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">登録</button>
                <a class="btn btn-warning mx-5" href="{{route('room.index')}}">戻る</a>
                
            </form>
            <div class="mt-5 text-end">
            @include('room.delete')
                <button class="btn btn-danger " type="button" data-bs-toggle="modal" data-bs-target="#deleteRoomModal{{ $room->id }}">削除</button>
            </div>
        </div>
    </div>
</div>
@endsection