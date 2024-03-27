import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";

window.addEventListener('load', function(){
//管理者用カレンダー　まだ途中
var CalendarEl = document.getElementById("admin");
//予約用カレンダー
let adminCalendar = new Calendar(CalendarEl, {
    plugins: [interactionPlugin,dayGridPlugin,timeGridPlugin,listPlugin],
    initialView: "dayGridMonth",
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek",
    },
    buttonText:{
        today:'今日',
        month:'月',
        week:'週',
    },
    locale:"ja",
    navLinks: false,
    businessHours: true,
    showNonCurrentDates: false, 
    selectLongPressDelay:0,
    slotMinTime: '12:00:00',
    allDaySlot: false,
    events: function(info,successCallback, failureCallback){
        var url = 'admin';

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    var events = [];
                    data.forEach(function(reservation) {
                        var start = reservation.checkin_date + 'T' + reservation.checkin_time; // 予約開始日時
                        var end = reservation.checkout_date; // 予約終了日時
                        var user = reservation.user; // 予約したユーザー情報
                        var title = user.name + '様';
                        var total = reservation.total;
                        var cssClass = "";
                        if( total >= 5){
                            cssClass = "five-people"
                        }else if(total >= 10){
                            cssClass = "ten-people"
                        }
                        events.push({
                            id:reservation.id,
                            title: title,
                            start: start,
                            end: end,
                            className: cssClass,
                        });
                        
                    });

                    // 取得したイベントをFullCalendarに渡す
                    successCallback(events);
                })
                .catch(error => {
                    console.error('予約情報の取得に失敗しました:', error);
                    failureCallback(error);
                });
    },
    eventClick: (e)=>{// イベントのクリックイベント
        var selectedReservation = e.event.id;
        console.log(e.event.id);
        window.location.href = `dashboard/reservation/${selectedReservation}`;
	}
});
adminCalendar.render();

});