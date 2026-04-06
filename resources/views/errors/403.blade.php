{{-- resources/views/errors/403.blade.php --}}
@extends('errors::minimal')

@section('title', $exception->getMessage() ?: 'Forbidden - Harris Ticketing System')
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Forbidden Error!'))
