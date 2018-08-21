@extends('layouts.app')

@section('title', 'Info')

@section('content')
    <div>Disclaimer</div>
    <div>General information</div>
    <div>Going for lunch</div>
    <div>Going for diner</div>
    <div>Going for a drink</div>
    <div>Clubbing</div>
    <div>Karaoke</div>
    <div>Shopping</div>
    <div>Hotels</div>
    <div>Game centers</div>
    <div>Combini</div>
    <div>Cafes</div>
    <div>Internet cafes</div>
    <div>Toilets</div>
    @include('payments.donations')
    @include('ads.baner')
@endsection
