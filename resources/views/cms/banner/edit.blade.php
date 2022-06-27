@extends('cms.layouts.page')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ui-form form-class="form-horizontal" title="Atualizar Registro" token="{{ csrf_token() }}"
            url="{{ route('banner.update', $banner->id) }}" cancel-url="{{ route('banner.index') }}" method="PUT">
            @if($errors->any())
            <div class="col-sm-12">
                <alert class="alert-danger" icon="fa-ban" title="Ops!"
                    text="Não foi possível atualizar o registro, verifique os campos em destaque!">
                </alert>
            </div>
            @endif

            <div class="row form-group{{ $errors->has('popup') ? ' has-error' : '' }}">
                <label class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Pop-up</label>
                <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
                    <input type="checkbox" aria-label="Nome" name="popup" id="popup" value="1"
                        {{$banner->popup == '1' ? 'checked' : ''}}> Ativo
                    @if ($errors->has('popup'))
                    <span class="help-block">
                        <strong>{{ $errors->first('popup') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            
            <div class="row form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                <label class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Status</label>
                <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
                    <input type="checkbox" aria-label="Nome" name="active" id="active" value="1"
                        {{$banner->active == '1' ? 'checked' : ''}}> Ativo
                    @if ($errors->has('active'))
                    <span class="help-block">
                        <strong>{{ $errors->first('active') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="row form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                <label class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Período de exibição</label>
                <div class="d-md-flex justify-content-between col-xl-10 col-lg-10 col-sm-10 col-12">
                    <input placeholder="Data inicial" class="mouse-alter datepicker mb-md-0 mb-2 form-control startDate" value="{{ $banner->start_date }}" name="start_date" id="startDate">
                    <input placeholder="Data final" class="mouse-alter datepicker ml-md-2 form-control endDate" value="{{ $banner->end_date }}" name="end_date" id="endDate">
                </div>
            </div>
            <div class="row form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                <label class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Título</label>
                <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
                    <input class="form-control" aria-label="Nome" type="text" name="title" id="title" maxlength="30"
                        value="{{ $banner->title }}">
                    @if ($errors->has('title'))
                    <span class="help-block">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                    @endif
                    <span class="help-block">
                        Máximo de 30 caracteres
                    </span>
                </div>
            </div>
            <div class="row form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                <label for="description"
                    class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Descrição</label>
                <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
                    <textarea name="description" value="{{ $banner->description }}" id="full_textarea_banner" >{{ $banner->description }}</textarea>
                    @if ($errors->has('description'))
                    <span class="help-block">
                        <strong>{{ $errors->first('description') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="row form-group{{ $errors->has('turn') ? ' has-error' : '' }}">
                <label for="turn" class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Ordem*</label>
                <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
                    <input type="number" name="turn" aria-label="Nome" class="form-control" id="turn"
                        value="{{ $banner->turn }}" maxlength="200" required>
                    @if ($errors->has('turn'))
                    <span class="help-block">
                        <strong>{{ $errors->first('turn') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="row form-group{{ $errors->has('button') ? ' has-error' : '' }}">
                <label class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Texto do botão 1</label>
                <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
                    <input class="form-control" aria-label="Nome" type="text" name="button" id="button" maxlength="20"
                        value="{{ $banner->button }}">
                    @if ($errors->has('button'))
                    <span class="help-block">
                        <strong>{{ $errors->first('button') }}</strong>
                    </span>
                    @endif
                    <span class="help-block">
                        Máximo de 20 caracteres
                    </span>
                </div>
            </div>
            <div class="row form-group{{ $errors->has('link') ? ' has-error' : '' }}">
                <label class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Link do botão 1</label>
                <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
                    <input class="form-control" aria-label="Nome" type="url" name="link" id="link"
                        value="{{ $banner->link }}">
                    @if ($errors->has('link'))
                    <span class="help-block">
                        <strong>{{ $errors->first('link') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="row form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                <label class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Imagem de fundo</label>
                <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
                    <div class="row">
                        @if(strlen($banner->image) > 0)
                        <div class="col-md-2 col-xs-4">
                            <img src="{{ asset($banner->image) }}" style="width:100%">
                        </div>
                        @endif
                        <div class="col-xs-11">
                            <input type="file" aria-label="Nome" name="image" value="">
                            <input type="hidden" aria-label="Nome" value="{{ $banner->image }}">
                            <span class="help-block">
                                Para manter a imagem atual, não preencha esse campo
                            </span>
                            @if ($errors->has('image'))
                            <span class="help-block">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <span class="help-block">
                        Tamanho e formato recomendado: 1920x600px JPG
                    </span>
                </div>
            </div>
            <div class="row form-group{{ $errors->has('mobile_image') ? ' has-error' : '' }}">
                <label class="col-xl-2 col-lg-2 col-sm-2 col-12 text-lg-right text-sm-left">Imagem de fundo
                    mobile</label>
                <div class="col-xl-10 col-lg-10 col-sm-10 col-12">
                    <div class="row">
                        @if(strlen($banner->mobile_image) > 0)
                        <div class="col-md-2 col-xs-4">
                            <img src="{{ asset($banner->mobile_image) }}" style="width:100%">
                        </div>
                        @endif
                        <div class="col-xs-11">
                            <input type="file" aria-label="Nome" name="mobile_image" value="">
                            <input type="hidden" aria-label="Nome" value="{{ $banner->mobile_image }}">
                            <span class="help-block">
                                Para manter a imagem atual, não preencha esse campo
                            </span>
                            @if ($errors->has('mobile_image'))
                            <span class="help-block">
                                <strong>{{ $errors->first('mobile_image') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <span class="help-block">
                        Tamanho e formato recomendado: 370x345px JPG
                    </span>
                </div>
            </div>
    </div>
    </ui-form>
</div>
</div>
@endsection