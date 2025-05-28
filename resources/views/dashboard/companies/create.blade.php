@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">{{ __('dashboard.company.create') }}</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard.common.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">{{ __('dashboard.company.management') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('dashboard.company.create') }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-tooltip">{{ __('dashboard.company.create_new') }}</h4>
                                    <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                            <li><a data-action="reload"><i class="icon-reload"></i></a></li>
                                            <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                            <li><a data-action="close"><i class="icon-cross2"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        <div class="card-text">
                                            <p>{{ __('dashboard.company.fill_required') }}</p>
                                        </div>

                                        <form class="form" method="POST" action="{{ route('companies.store') }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
                                                <div class="form-group">
            <label for="name_en">{{ __("dashboard.company.fields.name_en") }}</label>
            <input type="text" id="name_en" class="form-control @error('name_en') is-invalid @enderror"
                   name="name_en" value="{{ isset($company) ? $company->name_en : old('name_en') }}"
                   placeholder="{{ __("dashboard.company.fields.name_en") }}" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.company.fields.name_en") }}">
            @error('name_en')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                <div class="form-group">
            <label for="name_ar">{{ __("dashboard.company.fields.name_ar") }}</label>
            <input type="text" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                   name="name_ar" value="{{ isset($company) ? $company->name_ar : old('name_ar') }}"
                   placeholder="{{ __("dashboard.company.fields.name_ar") }}" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.company.fields.name_ar") }}">
            @error('name_ar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                <div class="form-group">
            <label for="is_active">{{ __("dashboard.company.fields.is_active") }}</label>
            <select id="is_active" name="is_active" class="form-control @error('is_active') is-invalid @enderror" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.company.fields.is_active") }}">
                <option value="0" {{ isset($company) && !$company->is_active ? 'selected' : '' }}>{{ __("dashboard.common.no") }}</option>
                <option value="1" {{ isset($company) && $company->is_active ? 'selected' : '' }}>{{ __("dashboard.common.yes") }}</option>
            </select>
            @error('is_active')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                                            </div>

                                            <div class="form-actions">
                                                <a href="{{ route('companies.index') }}" class="btn btn-warning mr-1">
                                                    <i class="icon-cross2"></i> {{ __('dashboard.common.cancel') }}
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="icon-check2"></i> {{ __('dashboard.common.save') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
