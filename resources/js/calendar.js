    import { Calendar } from "@fullcalendar/core";
    import dayGridPlugin from "@fullcalendar/daygrid";
    import interactionPlugin from "@fullcalendar/interaction";
    import axios from 'axios';
    
window.addEventListener('load', function(){
    var calendarEl = document.getElementById("calendar");
    var nowDate = new Date();
    var endOfMonth = new Date(nowDate.getFullYear(), nowDate.getMonth() + 1, 0);
    var endDate = new Date();
    endDate.setMonth(endDate.getMonth() + 3); // 今日の月に3ヶ月を加算
    var allowEndDate = new Date();
    allowEndDate.setMonth(allowEndDate.getMonth() + 3);
    allowEndDate.setDate(allowEndDate.getDate() + 1 );
    //予約用カレンダー
    let calendar = new Calendar(calendarEl, {
        plugins: [interactionPlugin,dayGridPlugin],
        initialView: "dayGridMonth",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right:"",
        },
        buttonText:{
            today:'今日',
        },
        locale:"ja",
        timeZone: 'Asia/Tokyo',
        businessHours: true, 
        selectable: true,
        selectLongPressDelay:0,
        validRange:{
            //カレンダーの範囲、今月の1日から3ヶ月後の月末まで
            start: nowDate.getFullYear() + '-' + ('0' + (nowDate.getMonth()+1)).slice(-2) + '-' + '01',
            end:endOfMonth.getFullYear() + '-' + ('0' + (endOfMonth.getMonth() + 4)).slice(-2) + '-' + ('0' + endOfMonth.getDate()).slice(-2)
        },
        showNonCurrentDates: false, // 来月の日付を非表示にする
        events: function(info, successCallback, failureCallback) {
            // バックエンドから予約情報を取得するエンドポイント
            var url = 'reservation';
        
            // バックエンドから予約情報を取得
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    var events = [];

                    // 今日の日付を取得
                    var today = new Date();
                    today.setHours(0, 0, 0, 0); // 時間をリセットして日付部分のみにする

                    //カレンダーに予約を取れる期間を設定
                    var currentDate = new Date(today);
                    while(currentDate <= endDate){
                        var dateString = currentDate.getFullYear() + '-' + ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' + ('0' + currentDate.getDate()).slice(-2);
                        var roomCount = data.roomCount;
                        var detailsCountMap = data.detailsCountMap;
                        var checkinCount = detailsCountMap[dateString];
                        var title = '空室あり';
                        var cssClass = 'available';
                        if(roomCount === checkinCount ){
                            title = '満室';
                            cssClass = 'full-reservation';
                        }
                        events.push({
                            title: title,
                            start: dateString,
                            end: dateString,
                            className: cssClass,
                        });
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                    // 取得したイベントをFullCalendarに渡す
                    successCallback(events);
                })
                .catch(error => {
                    console.error('予約情報の取得に失敗しました:', error);
                    failureCallback(error);
                });
        },
        
        select: function (info) {
            // 選択された日付をURLに変換
            const year = info.start.getFullYear();
            const month = ('0' + (info.start.getMonth() + 1)).slice(-2); // 月は 0-indexed なので +1 
            const day = ('0' + info.start.getDate()).slice(-2); // 日付を2桁にするために先頭に 0 を追加し、後ろから 2 桁目までの文字列を取得
            const selectedDate = `${year}-${month}-${day}`;
            //満室の場合フォームへアクセスできなくする
            // バックエンドから予約情報を取得するエンドポイント
            var url = 'reservation';
            // バックエンドから予約情報を取得
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    var roomCount = data.roomCount || 0;
                    var detailsCountMap = data.detailsCountMap || {};
                    var checkinCount = detailsCountMap[selectedDate] || 0;
                    if(roomCount === checkinCount) {
                        alert('選択された日付は満室です。');
                    }else{
                    // 予約ページのURLに選択された日付をクエリパラメータとして追加して遷移
                    window.location.href = `reservation/create/${selectedDate}`;
                    }
                });

        },
        //過去の日付をクリックできなくする
        selectConstraint:{
            start:nowDate.getFullYear() + '-' + ('0' + (nowDate.getMonth() + 1)).slice(-2) + '-' + ('0' + nowDate.getDate()).slice(-2),
            end:allowEndDate.getFullYear() + '-' + ('0' + (allowEndDate.getMonth() + 1)).slice(-2) + '-' + ('0' + allowEndDate.getDate()).slice(-2)
        },

    });
    calendar.render();
});