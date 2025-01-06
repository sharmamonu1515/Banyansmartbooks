
<img src="{{ url('images/logo.png') }}" alt="{{ env('APP_NAME') }}">
@if ($type === 'login')
Here is your login OTP : <strong>{{ $otp }}</strong><br><br>
Please make sure you never share this code with anyone.<br><br>
Note : This code will expire in 10 minutes<br><br>
You have received this email because you are registered at <a href="https://banyansmartbooks.in/" target="_blank">Banyan Smart Book</a> website.<br><br>
@else
Welcome to Banyan Smart Books, your Sign Up Key : <strong>{{ $data['signup_key'] }}</strong> for <strong>{{ $data['standard'] }}</strong> Standard <strong>{{ $data['language'] }}</strong><br><br>
Here is your registration OTP : <strong>{{ $otp }}</strong><br><br>
Please make sure you never share this code with anyone.<br><br>
Note : This code will expire in 10 minutes
You have received this email because you are Signing up at <a href="https://banyansmartbooks.in/" target="_blank">Banyan Smart Book</a> website.<br><br>
@endif
