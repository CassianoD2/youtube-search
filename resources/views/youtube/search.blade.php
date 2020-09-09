@extends('layouts.app')

@section('content')
    @if (session('saveKey'))
        <div class="alert alert-success">
            {{ session('saveKey') }}
        </div>
    @endif

    @if (session('errorSaveKey'))
        <div class="alert alert-success">
            {{ session('errorSaveKey') }}
        </div>
    @endif

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Lista de Vídeos') }} <br>
                        @if (isset($_GET['text']))
                            Termo pesquisado: {{ $_GET['text'] }}
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="{{ url('youtube/search') }}" method="GET">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <label for="text" class="col-form-label">Pesquisar vídeo</label>
                                        <input type="text" name="text" class="form-control" placeholder="Pesquisar vídeo">
                                    </div>
                                    <div class="form-group row">
                                        <label for="timeView">Tempo de visualização</label>
                                        <select name="timeView" id="timeView" class="form-control">
                                            <option value="30">30</option>
                                            <option value="60">60</option>
                                            <option value="90">90</option>
                                            <option value="120">120</option>
                                            <option value="150">150</option>
                                            <option value="180">180</option>
                                            <option value="210">210</option>
                                            <option value="240">240</option>
                                        </select>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button class="btn btn-info col-6" type="submit">GO</button>
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
