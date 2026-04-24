@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ticket Packages</h1>
    <div class="ticket-packages">
        @foreach ($ticketPackages as $package)
            <div class="ticket-package">
                <h2>{{ $package['name'] }}</h2>
                <p>{{ $package['description'] }}</p>
                <p><strong>Price:</strong> ${{ $package['price'] }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection