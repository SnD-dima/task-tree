@extends('layouts.app')


@section('content')
    <div class="container">

        <div >
            <h1>Tree</h1>
            <lorem-tree :data="{{ json_encode($tree) }}"/>
        </div>

    </div>
@endsection
