@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Статистика сыгранных игр с boardgamegeek.com</div>
                <div class="card-body">
                    <div id="app" class="container">
                        <app></app>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
