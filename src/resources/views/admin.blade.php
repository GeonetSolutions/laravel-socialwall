@foreach ($socialwall as $post)
	Social Type: {{ $post['social_type'] }}
	<br />
	Timestamp: {{ $post['frontend_timestamp'] }}
	<br />
	Link: <a href="{{ $post['link'] }}" target="_BLANK">{{ $post['link'] }}</a>
	<br />
	@if ( $post['social_type'] !== 'instagram' && !empty( $post['message'] )  )
		Message: {{ $post['message'] }}
		<br />
	@endif

	@if ( !empty( $post['media'] )  )
			Media: <br /> <img src="{{ $post['media'] }}" />
		<br />
	@endif

	Posted By: {{ $post['name'] }} <br />
	<img style="height:50px;" src="{{ $post['profile_pic'] }}" />

	<br /> <br />
@endforeach