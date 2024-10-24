let isFormDirty = false;

// フォームの入力が変更されたときにフラグを立てる
document.querySelector('form').addEventListener('input', function() {
    isFormDirty = true;
});

// ページがリロードまたは閉じられる前に警告を表示
window.addEventListener('beforeunload', function (e) {
    if (isFormDirty) {
        const confirmationMessage = '変更が保存されていません。ページを離れますか？';
        e.returnValue = confirmationMessage; // 標準に従う
        return confirmationMessage; // 一部のブラウザではこれが必要
    }
});

// 保存・更新ボタンのクリックイベント
document.querySelector('button[data-save="true"]').addEventListener('click', function(e) {
    isFormDirty = false;
});

// キャンセルボタンのクリックイベント
document.querySelector('button[data-cancel="true"]').addEventListener('click', function(e) {
    if (isFormDirty) {
        const confirmation = confirm('変更が保存されていません。キャンセルしますか？');
        if (!confirmation) {
            e.preventDefault();
        }
    }
});
