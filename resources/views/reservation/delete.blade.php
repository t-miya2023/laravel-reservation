<!-- モーダルのHTML -->
<div id="deleteReservationModal{{ $reservation->id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">本当にキャンセルしてもよろしいですか？</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"  data-bs-dismiss="modal">戻る</button>
                <form id="deleteForm" method="POST" action="{{ route('reservation.destroy',$reservation->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">予約をキャンセルする</button>
                </form>
            </div>
        </div>
    </div>
</div>