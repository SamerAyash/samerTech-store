@component('mail::message')
<h1>Reset Account</h1> ,
Welcome {{$data['admin']->name}}

@component('mail::button', ['url' => route('admin.resetPasswordToken',$data['token'])])
Click here to reset your password
@endcomponent
or copy this link
<a href="{{route('admin.resetPasswordToken',$data['token'])}}">{{route('admin.resetPasswordToken',$data['token'])}}</a>

Thanks<br>
@endcomponent
