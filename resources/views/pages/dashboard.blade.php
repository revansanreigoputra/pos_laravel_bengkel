@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <h1>Halo selamat datang {{ auth()->user()->name }}</h1>
@endsection