@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Videogame Collection</h1>
    <p>This page will display the videogames you have added to your collection.</p>
    {{-- Placeholder for collection list --}}
    @if (isset($videogames) && count($videogames) > 0)
         {{-- We will loop through user's videogames here later --}}
    @else
         <p>You haven't added any videogames to your collection yet.</p>
    @endif
</div>
@endsection