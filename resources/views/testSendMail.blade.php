<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width='device-width', initial-scale=1.0">
    <title>Api send mail</title>
</head>
<body>
    <form id="emailForm" action="{{route('sendmail')}}" method="POST">
        @csrf
        <input type="text" name="to" id="to" placeholder="ส่งถึงไผ" required>
        <input type="text" name="subject" id="subject" placeholder="หัวข้อ" required>
        <input type="text" name="message" id="message" placeholder="ข้อความ" required>
        <input type="submit" value="send mail">
    </form>
    <div id="responseMessage"></div>

    <script>
        // document.getElementById('emailForm').addEventListener('submit', function(e) {
        //     e.preventDefault();
        //     const to = document.getElementById('to').value;
        //     const subject = document.getElementById('subject').value;
        //     const message = document.getElementById('message').value;
        //     const data = {to: to, subject: subject, message: message};
        //     fetch('https://fdnet.dhammakaya.network/application/api/send_mail_approve.php', {
        //         method: 'POST',
        //         headers: {'Content-Type': 'application/json'},
        //         body: JSON.stringify(data)
        //     })
        //     .then(response => response.json())
        //     .then(result => {
        //         document.getElementById('responseMessage').textContent = result.message;
        //         if(result.status === 'success') {
        //             document.getElementById('responseMessage').style.color = 'green';
        //         } else {
        //             document.getElementById('responseMessage').style.color = 'red';
        //         }
        //     })
        //     .catch(error => {
        //         document.getElementById('responseMessage'.textContent = 'An error occured: ' + error.message);
        //         document.getElementById('responseMessage').style.color = 'red';
        //     });
        // });
    </script>
    <script>
        document.getElementById('emailForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form from submitting the normal way

            // Get form data
            const to = document.getElementById('to').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;

            // Prepare the data for sending
            const data = {
                to: to,
                subject: subject,
                message: message,
                _token: '{{ csrf_token() }}' // Include CSRF token
            };

            // Send the request using Fetch API
            fetch("{{ route('sendmail') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response
                let messageDiv = document.getElementById('responseMessage');
                if (data.status === 'success') {
                    messageDiv.innerHTML = `<p style="color:green">${data.message}</p>`;
                } else {
                    messageDiv.innerHTML = `<p style="color:red">${data.message}</p>`;
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>