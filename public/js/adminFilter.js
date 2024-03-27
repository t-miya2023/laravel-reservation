
/*=======================ロード時の絞り込み========================================*/
    document.addEventListener('DOMContentLoaded', function() {

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
                uncheckedCheckbox.closest('.room').style.display = '';
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