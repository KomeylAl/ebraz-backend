<h2>{{ $notification->title }}</h2>
<p>{{ $notification->message }}</p>
@if(isset($notification->meta['url']))
    <a href="{{ $notification->meta['url'] }}">View details</a>
@endif