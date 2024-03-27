document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('submit-btn').addEventListener('click', function(event) {
        // 予約人数と部屋数を取得
        var total = parseInt(document.getElementById('total').value);
        var numberOfRoom = parseInt(document.getElementById('number_of_room').value);

        // 予約人数が部屋数以上でない場合はフォーム送信をキャンセル
        if (total < numberOfRoom) {
            event.preventDefault(); // フォーム送信をキャンセル
            document.getElementById('error-message').innerText = '予約人数は部屋数以上でなければなりません。';
        }

    //部屋数が空き部屋数以上を入力された場合はフォーム送信をキャンセル
        
    });
});