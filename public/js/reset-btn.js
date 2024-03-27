
//絞り込みリセットボタン

document.querySelector('#reset-btn').addEventListener('click', () => {
    // フォームをリセット
    if(document.querySelector('#search') !== null){
        document.querySelector('#search').value = '';
    }
    // フォームをリセット
    if(document.querySelector('#search_date') !== null){
        document.querySelector('#search_date').value = '';
    }
    // フォームを送信（リセットボタンのデフォルトの動作を防ぐ）
    document.querySelector('#search-form').submit();
});
