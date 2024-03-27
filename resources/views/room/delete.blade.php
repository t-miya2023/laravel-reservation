<!-- モーダルのHTML -->
<div id="deleteRoomModal{{ $room->id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">本当に削除してもよろしいですか？</h5>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"  data-bs-dismiss="modal">キャンセル</button>
                <form id="deleteForm" method="POST" action="{{ route('room.destroy',$room->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">削除</button>
                </form>
            </div>
        </div>
    </div>
</div>