function showPopup(popupId) {
    document.getElementById(popupId).style.display = 'block';
}

function closePopup(popupId) {
    document.getElementById(popupId).style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function () {
    var formEvent = document.querySelector('#popup10 form');
    var formMedia = document.querySelector('#popup11 form');

    formEvent.addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm(this, '#eventTable tbody');
    });

    formMedia.addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm(this, '#mediaTable tbody');
    });

    function submitForm(form, tableBodySelector) {
        var formData = new FormData(form);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${response.titre}</td>
                        <td>${response.description}</td>
                        <td><img src="uploads/${response.image}" width="100"></td>
                    `;
                    document.querySelector(tableBodySelector).appendChild(newRow);
                    closePopup(form.parentElement.parentElement.id);
                    form.reset();
                } else {
                    alert('Failed to add the post');
                }
            } else {
                alert('An error occurred');
            }
        };
        xhr.send(formData);
    }
});
