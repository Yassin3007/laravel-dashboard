@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">{{ __('dashboard.category.edit') }}</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard.common.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">{{ __('dashboard.category.management') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('dashboard.category.edit') }}
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
                                    <h4 class="card-title" id="basic-layout-tooltip">{{ __('dashboard.category.edit') }} {{ __('dashboard.category.title') }} #{{ $category->id }}</h4>
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
                                            <p>{{ __('dashboard.category.update_info') }}</p>
                                        </div>

                                        <form class="form" method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-body">
                                                <div class="form-group">
            <label for="name_en">{{ __("dashboard.category.fields.name_en") }}</label>
            <input type="text" id="name_en" class="form-control @error('name_en') is-invalid @enderror"
                   name="name_en" value="{{ isset($category) ? $category->name_en : old('name_en') }}"
                   placeholder="{{ __("dashboard.category.fields.name_en") }}" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.category.fields.name_en") }}">
            @error('name_en')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                <div class="form-group">
            <label for="name_ar">{{ __("dashboard.category.fields.name_ar") }}</label>
            <input type="text" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                   name="name_ar" value="{{ isset($category) ? $category->name_ar : old('name_ar') }}"
                   placeholder="{{ __("dashboard.category.fields.name_ar") }}" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.category.fields.name_ar") }}">
            @error('name_ar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                <div class="form-group">
            <label for="company_id">{{ __("dashboard.category.fields.company_id") }}</label>
            <select id="company_id" name="company_id" class="form-control @error('company_id') is-invalid @enderror" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.category.fields.company_id") }}">
                <option value="">{{ __("dashboard.common.select") }} {{ __("dashboard.category.fields.company_id") }}</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ isset($category) && $category->company_id == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
            @error('company_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                <div class="form-group">
            <label for="is_active">{{ __("dashboard.category.fields.is_active") }}</label>
            <select id="is_active" name="is_active" class="form-control @error('is_active') is-invalid @enderror" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.category.fields.is_active") }}">
                <option value="0" {{ isset($category) && !$category->is_active ? 'selected' : '' }}>{{ __("dashboard.common.no") }}</option>
                <option value="1" {{ isset($category) && $category->is_active ? 'selected' : '' }}>{{ __("dashboard.common.yes") }}</option>
            </select>
            @error('is_active')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                                            </div>

                                            <div class="form-actions">
                                                <a href="{{ route('categories.index') }}" class="btn btn-warning mr-1">
                                                    <i class="icon-cross2"></i> {{ __('dashboard.common.cancel') }}
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="icon-check2"></i> {{ __('dashboard.common.update') }}
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
