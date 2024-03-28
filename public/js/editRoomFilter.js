function filterRoom(){
    var roomFound = false; //適切な部屋を表示できているか判定するため
    var targetCapacity = total - 1; //total - 1がなければ１づつ増やし再検索
    var availableRoomCapa = 0; //表示している部屋の最大収容数
    var reservedRoomsCount = 0; // 予約された部屋の数をカウントする変数
    var maxCapacity = Math.max(...Array.from(document.querySelectorAll('.room'), room => parseInt(room.dataset.capacity)));

        //必要最低限の部屋を表示する（２人に対して１０人部屋を表示させない）
        while(!roomFound && targetCapacity <= maxCapacity){
            document.querySelectorAll('.room').forEach(function(room) {
            var roomCapacity = parseInt(room.dataset.capacity);
            if (roomCapacity <= targetCapacity ) {
                room.style.display = ''; // 部屋を表示
                reservedRoomsCount++;
                availableRoomCapa = roomCapacity;
                //部屋数が足りない場合さらに大きい部屋を検索する
                if(reservedRoomsCount >= maxSelectedRooms){
                    roomFound = true;
                    }
                }else{
                    room.style.display = 'none'; // 部屋を非表示
                }
            });
            //条件を満たさなければ＋１して再検索
            if(!roomFound){
                targetCapacity++;
                reservedRoomsCount = 0;
            }
        }
        //大部屋が必要なパターンが出たら（２部屋５人：１：４のような分かれ方をする場合）
        //１回り大きい部屋を表示させる。
        var count = targetCapacity + 1;
        roomFound = false;
        if(availableRoomCapa < total - (maxSelectedRooms - 1) ){
            while(!roomFound && count <= maxCapacity){
                document.querySelectorAll('.room').forEach(function(room) {
                var roomCapacity = parseInt(room.dataset.capacity);
                if (roomCapacity == count ) {
                    room.style.display = ''; // 部屋を表示
                    roomFound = true;
                    }
                });
                if(!roomFound){
                    count++;
                }
            }
        }
        //2部屋以上から１部屋に変更された時
        if( maxSelectedRooms <= 1){
            document.querySelectorAll('.now-room').forEach(function(room) { 
                var roomCapacity = parseInt(room.dataset.capacity);
                if( roomCapacity < total){
                    room.style.display = 'none'; // 部屋を非表示
                }else{
                    room.style.display = ''; // 部屋を表示
                }
            });
        }
}
/*=======================ロード時の絞り込み========================================*/
    document.addEventListener('DOMContentLoaded', function() {
        filterRoom();


    /*---------セレクトボックスの中身を作成--------------------------------------------*/
        var formSelects = document.querySelectorAll('.form-select');
        formSelects.forEach(function(formSelect) {
            var room = formSelect.closest('.room');
            var roomCapacity = parseInt(room.dataset.capacity);
            if(roomCapacity > total){
                for (var i = 1; i <= total; i++) {
                    var option = document.createElement('option');
                    option.text = i;
                    option.value = i;
                    formSelect.add(option);
                }
            }else{
                for (var i = 1; i <= roomCapacity; i++) {
                    var option = document.createElement('option');
                    option.text = i;
                    option.value = i;
                    formSelect.add(option);
                }
            }

        });

    });

/*===================クリック時の動作========================================================================*/

    document.querySelectorAll('.select-btn').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var selectedRooms = document.querySelectorAll('.select-btn:checked');//選択した部屋情報を取得
            var selectedCount = selectedRooms.length; //選択した部屋の数
            var submitButton = document.querySelector('.submit-btn');//送信ボタンの要素を取得

            //選択した部屋の定員数の合計を計算
            var totalCapacity = 0; //選択した部屋の定員数の合計
            selectedRooms.forEach(function(room) {
                totalCapacity += parseInt(room.dataset.capacity);
                //選択した部屋の人数入力を有効にする
                var formSelect = room.closest('.room').querySelector('.form-select');
                formSelect.removeAttribute("disabled");
            });

    /*----------最大選択可能な部屋数以上選択できないようにする--------------------*/
            if (selectedCount >= maxSelectedRooms) {
                document.querySelectorAll('.select-btn:not(:checked)').forEach(function(uncheckedCheckbox) {
                    uncheckedCheckbox.closest('.room').style.display = 'none'; // 非表示にする
                });
            } else {
                filterRoom();
            }

    /*-----------選択した部屋の定員の合計が予約人数以下だった場合-----------------*/
            if (totalCapacity < total && selectedCount == maxSelectedRooms) {
                document.getElementById('error-message').innerText = '選択した部屋の定員数が足りません。';
                submitButton.disabled = true;
            } else if(totalCapacity >= total && selectedCount == maxSelectedRooms) {
                document.getElementById('error-message').innerText = ''; // エラーメッセージをクリア
                submitButton.disabled = false;
            }else{
                document.getElementById('error-message').innerText = ''; // エラーメッセージをクリア
                submitButton.disabled = true;
            }

            
        });
    });

/*===================submit-btnを押した時の動作========================================================================*/
    if( total >= 2 ){
        document.querySelector('.submit-btn').addEventListener('click',function(event){
            var selectedRooms = document.querySelectorAll('.select-btn:checked');//選択した部屋情報を取得
            var totalSelect = 0;
            selectedRooms.forEach(function(room) {
                var formSelect = room.closest('.room').querySelector('.form-select');
                var selectValue = formSelect.value;
                totalSelect += parseInt(selectValue);
            });
            if(totalSelect != total){
                event.preventDefault();
                document.getElementById('error-message2').innerText = '選択した宿泊人数と合計人数が一致しません。';
            }
        })
    }
