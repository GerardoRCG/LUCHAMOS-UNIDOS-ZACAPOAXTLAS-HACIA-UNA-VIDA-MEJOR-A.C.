@extends('layouts.app')

@section('titulo')
    Nuestro Trabajo
@endsection

@section('contenido')

    <x-listar-post :posts="$posts"/>

@endsection