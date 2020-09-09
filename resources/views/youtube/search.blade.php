@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Lista de Vídeos') }} - Termo pesquisado: {{ $_GET['text'] }}</div>
                    <div class="card-body">
                        <form action="{{ url('youtube/search') }}" method="GET">
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-group">
                                        <input type="text" name="text" class="form-control" placeholder="Pesquisar vídeo">
                                        <div class="input-group-append">
                                            <button class="btn btn-info" type="submit">GO</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if ($error)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="text-center alert-danger">
                                        {{ $errorMessage }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row mt-3 mb-3">
                                <div class="col-12">
                                    Estatisticas (Palavras que mais aparecem):
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Palavra</th>
                                                <th>Contagem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($statistics as $word => $quantity)
                                            <tr>
                                                <td>{{ $word }}</td>
                                                <td>{{ $quantity }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                            @foreach( $videos as $id => $video)
                                <div class="col-12 mb-2">
                                    <div class="card">
                                        <div class="row no-gutters">
                                            <div class="col-3 m-1">
                                                <img src="{{ $video['items'][0]['snippet']['thumbnails']['high']['url'] }}" style="max-width: 150px" class="card-img" alt="">
                                            </div>
                                            <div class="col-8">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <a href="https://www.youtube.com/watch?v={{ $id }}" target="_blank">
                                                            {{ $video['items'][0]['snippet']['title'] }}
                                                        </a>
                                                    </h5>
                                                    <p class="card-text">
                                                        {{ substr($video['items'][0]['snippet']['description'], 0, 120) }} ...
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
