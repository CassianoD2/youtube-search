@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Adicionar/Alterar Chave de acesso API') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ url('youtube/saveapi') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <label for="text" class="col-form-label">Youtube API KEY</label>
                                        <input type="text" value="{{ $apiKey }}" name="api_key" class="form-control" placeholder="Cole aqui a sua chave da API">
                                    </div>
                                    <div class="col-12 text-center">
                                        <button class="btn btn-info col-6" type="submit">Salvar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
