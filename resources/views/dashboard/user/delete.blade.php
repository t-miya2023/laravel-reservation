<!-- モーダルのHTML -->
<div id="deleteUserModal{{ $user->id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">本当に削除してもよろしいですか？</h5><br>
            </div>
            <div class="modal-body">
                <h6 class="text-danger">*ユーザーを削除する際はユーザーの予約がないことを<br>確認してから削除してください。</h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"  data-bs-dismiss="modal">戻る</button>
                <form id="deleteForm" method="POST" action="{{ route('dashboard.user.destroy',$user->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">選択したユーザーを削除する</button>
                </form>
            </div>
        </div>
    </div>
</div>