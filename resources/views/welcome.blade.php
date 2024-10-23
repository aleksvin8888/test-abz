<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
    <div class="row align-items-center">
        <div class=" btn-group" role="group">
            <button type="button" class="btn btn-primary" id="btnToken">Token</button>
            <button type="button" class="btn btn-primary" id="btnUsers">Users</button>
            <button type="button" class="btn btn-primary" id="btnCreate">Create</button>
            <button type="button" class="btn btn-primary" id="btnPositions">Positions</button>
        </div>
    </div>
    <div id="content">
        <h3>Content</h3>
        <div id="contentData">
            <!-- Тут буде динамічний вивід інформації -->
        </div>
        <div id="pagination" class="mt-3">
            <!-- Пагінація для списку користувачів -->
        </div>
    </div>
</div>

<script>

    document.getElementById('btnToken').addEventListener('click', function () {
        fetch('/api/token')
            .then(response => response.json())
            .then(data => {
                document.getElementById('contentData').innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                document.getElementById('pagination').innerHTML = '';
            });
    });

    document.getElementById('btnUsers').addEventListener('click', function () {
        fetchUsers('/api/users');
    });

    function fetchUsers(url) {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const usersContainer = document.getElementById('contentData');
                let usersList = '<ul class="list-group">';

                data.users.forEach(user => {
                    usersList += `
                            <li class="list-group-item d-flex align-items-center">
                                <img src="${user.photo}" alt="User Photo" class="img-thumbnail me-3" style="width: 80px; height: 80px;">
                                <div>
                                    <h5 class="mb-1">${user.name}</h5>
                                    <p class="mb-1">
                                        <strong>Email:</strong> ${user.email}<br>
                                        <strong>Phone:</strong> ${user.phone}<br>
                                        <strong>Position:</strong> ${user.position}<br>
                                        <strong>Registered:</strong> ${new Date(user.registration_timestamp * 1000).toLocaleString()}
                                    </p>
                                </div>
                                <button class="btn btn-info btn-sm ms-auto" onclick="showUser(${user.id})">Show</button>
                            </li>
                        `;
                });

                usersList += '</ul>';
                usersContainer.innerHTML = usersList;

                const paginationContainer = document.getElementById('pagination');
                paginationContainer.innerHTML = `
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                <li class="page-item ${data.links.prev_link ? '' : 'disabled'}">
                                    <a class="page-link" href="#" tabindex="-1" onclick="fetchUsers('${data.links.prev_link}')">Previous</a>
                                </li>
                                ${Array.from({length: data.total_pages}, (_, i) => `
                                    <li class="page-item ${data.page === i + 1 ? 'active' : ''}">
                                        <a class="page-link" href="#" onclick="fetchUsers('/api/users?page=${i + 1}&count=${data.count}')">${i + 1}</a>
                                    </li>
                                `).join('')}
                                <li class="page-item ${data.links.next_link ? '' : 'disabled'}">
                                    <a class="page-link" href="#" onclick="fetchUsers('${data.links.next_link}')">Next</a>
                                </li>
                            </ul>
                        </nav>
                    `;
            });
    }

    function showUser(userId) {
        fetch(`/api/users/${userId}`)
            .then(response => response.json())
            .then(data => {
                const user = data.user;
                const userCard = `
                <div class="card mx-auto" style="width: 18rem;">
                    <img src="${user.photo}" class="card-img-top" alt="User Photo">
                    <div class="card-body">
                        <h5 class="card-title">${user.name}</h5>
                        <p class="card-text">
                            <strong>Email:</strong> ${user.email}<br>
                            <strong>Phone:</strong> ${user.phone}<br>
                            <strong>Position:</strong> ${user.position}<br>
                            <strong>Registered:</strong> ${new Date(user.registration_timestamp * 1000).toLocaleString()}
                        </p>
                        <button class="btn btn-primary" onclick="fetchUsers('/api/users')">Back to Users</button>
                    </div>
                </div>
            `;

                document.getElementById('contentData').innerHTML = userCard;
                document.getElementById('pagination').innerHTML = '';
            });
    }

    document.getElementById('btnPositions').addEventListener('click', function () {
        fetch('/api/positions')
            .then(response => response.json())
            .then(data => {
                let positionsList = '<ul>';
                data.positions.forEach(position => {
                    positionsList += `<li>${position.id}: ${position.name}</li>`;
                });
                positionsList += '</ul>';
                document.getElementById('contentData').innerHTML = positionsList;
                document.getElementById('pagination').innerHTML = '';
            });
    });

    document.getElementById('btnCreate').addEventListener('click', function () {
        fetch('/api/positions')
            .then(response => response.json())
            .then(data => {
                let positionOptions = '';
                data.positions.forEach(position => {
                    positionOptions += `<option value="${position.id}">${position.name}</option>`;
                });
                const createUserForm = `
                <div id="contentData" class="p-3 border">
                    <form id="createUserForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="position_id" class="form-label">Position</label>
                            <select class="form-select" id="position_id" name="position_id" required>
                                <option value="">Select a position</option>
                                ${positionOptions}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                            <canvas id="canvas" style="display:none;"></canvas>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="getTokenAndCreateUser()">Save</button>
                    </form>
                </div>
            `;

                document.getElementById('contentData').innerHTML = createUserForm;
                document.getElementById('pagination').innerHTML = '';
                document.getElementById('photo').addEventListener('change', handleImage, false);
            })
            .catch(error => {
                console.error('Error fetching positions:', error);
                alert('Failed to load positions.');
            });
    });

    function handleImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(event) {
            const img = new Image();
            img.src = event.target.result;

            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const targetSize = 70;
                canvas.width = targetSize;
                canvas.height = targetSize;
                const minSize = Math.min(img.width, img.height);
                const sx = (img.width - minSize) / 2;
                const sy = (img.height - minSize) / 2;
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, sx, sy, minSize, minSize, 0, 0, targetSize, targetSize);
                canvas.toBlob(function(blob) {
                    const newFile = new File([blob], 'photo.jpg', { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(newFile);
                    document.getElementById('photo').files = dataTransfer.files;
                }, 'image/jpeg', 1.0);
            };
        };

        reader.readAsDataURL(file);
    }

    function getTokenAndCreateUser() {
        fetch('/api/token')
            .then(response => response.json())
            .then(data => {
                const token = data.token;
                createUser(token);
            })
            .catch(error => {
                console.error('Error fetching token:', error);
                alert('Failed to get token.');
            });
    }

    function createUser(token) {
        const form = document.getElementById('createUserForm');
        const formData = new FormData(form);

        fetch('/api/users', {
            method: 'POST',
            body: formData,
            headers: {
                'Token': token
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User created successfully!');
                    document.getElementById('contentData').innerHTML = `<p>User ID: ${data.user_id}</p>`;
                } else {
                    if (data.fails) {
                        let errorMessages = '<div class="alert alert-danger"><ul>';
                        for (const [field, messages] of Object.entries(data.fails)) {
                            messages.forEach(message => {
                                errorMessages += `<li>${message}</li>`;
                            });
                        }
                        errorMessages += '</ul></div>';
                        document.getElementById('contentData').insertAdjacentHTML('afterbegin', errorMessages);
                    } else {
                        alert('Error creating user.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to create user.');
            });
    }

    function generatePagination(data) {
        let paginationHtml = '<nav><ul class="pagination">';
        for (let i = 1; i <= data.total_pages; i++) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="fetchPage(${i})">${i}</a></li>`;
        }
        paginationHtml += '</ul></nav>';
        return paginationHtml;
    }

    function fetchPage(pageNumber) {
        fetch(`/api/users?page=${pageNumber}`)
            .then(response => response.json())
            .then(data => {
                let usersList = '<ul>';
                data.users.forEach(user => {
                    usersList += `<li>${user.name} <button onclick="showUser(${user.id})">Show</button></li>`;
                });
                usersList += '</ul>';
                document.getElementById('contentData').innerHTML = usersList;
                document.getElementById('pagination').innerHTML = generatePagination(data);
            });
    }
</script>
</body>
</html>
