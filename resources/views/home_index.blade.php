@extends('layouts.app')

@section('content')
    <div class="mt-5 font-sans font-bold text-3xl sm:text-4xl md:text-6xl lg:text-7xl text-center w-4/5 md:w-auto mx-auto">{{ $title }}</div>
    <div class="text-white pb-20 pt-10 md:pt-20 pl-4 pr-4 md:pl-40 md:pr-40 text-center text-l sm:text-xl md:text-2xl w-4/5 md:w-auto mx-auto">
        <p>{{ $paragraph1 }}</p>
        <br/>
        <p>{{ $paragraph2 }}</p>
        <br/>
        <p>{{ $paragraph3 }}</p>
    </div>
@endsection

