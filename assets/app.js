document.getElementById('upload-file-form').addEventListener('submit', function (e){
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/get-licenses', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('licenses').textContent = JSON.stringify(data.error, null, 2);
            } else {
                document.getElementById('licenses').textContent = JSON.stringify(data['licenses'], null, 2);
            }
        })
        .catch(error => {
            console.error('Ошибка: ', error);
        })
})