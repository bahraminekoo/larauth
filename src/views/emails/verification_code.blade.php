<p>Thanks for signing up!</p>
<p>Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.</p>

------------------------
Email: {{ $email }}
Password: {{ $password }}
------------------------

Please click this link to activate your account:

{{ url('/auth/verify-email', ['email' => $email, 'hash' => $hash]) }}