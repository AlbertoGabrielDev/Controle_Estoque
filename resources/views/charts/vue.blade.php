@extends('layouts.principal')

@section('conteudo')
<grafico></grafico>
@endsection    

@push('js')
<script src="{{url('js/app.js')}}"></script>
@endpush