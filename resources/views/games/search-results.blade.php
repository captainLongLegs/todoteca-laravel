@extends('layouts.app')
@foreach ($games as $game)
    <h3>{{ $game['name'] }}</h3>
    <img src="{{ str_replace('t_thumb', 't_cover_big', $game['cover']['url'] ?? '') }}" />
@endforeach