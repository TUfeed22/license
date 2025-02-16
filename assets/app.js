document.getElementById('upload-file-form').addEventListener('submit', function (e){
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/get-licenses', {
        method: 'POST',
        body: formData
    })
        .then(responce => responce.json())
        .then(data => {

            if (data.error) {
                alert(data.error)
            } else {

                document.getElementById('licenses').textContent = JSON.stringify(data['licenses'], null, 2);
            }
        })
        .catch(error => {
            console.error('Ошибка: ', error);
        })
})