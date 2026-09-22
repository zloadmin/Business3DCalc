@extends('layouts.app')

@section('title', __('Create Game Session'))

@section('content')
    @include('game-session.game-session-info')
    @include('game-session.buy-building-form')
    @include('game-session.buildings-table')
    @include('game-session.train-items-table')
@endsection
