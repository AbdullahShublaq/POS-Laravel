@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.products')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.products.index') }}"> @lang('site.products')</a></li>
                <li class="active">@lang('site.edit')</li>
            </ol>

        </section>

        <section class="content">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('site.edit')</h3>
                </div>

                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.products.update', $product->id) }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}

                        <div class="form-group">
                            <label>@lang('site.categories') <small style="color: red">*</small></label>
                            <select name="category_id" class="form-control" style="padding: 2px 12px;">
                                <option value="">@lang('site.all_categories')</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @foreach(config('translatable.locales') as $locale)
                            <div class="form-group">
                                {{--site.ar.name--}}
                                <label>@lang('site.' . $locale . '.name') <small style="color: red">*</small></label>
                                {{--ar[name] ... en[name]--}}
                                <input type="text" name="{{ $locale }}[name]" class="form-control ckeditor" value="{{ $product->name }}">
                            </div>
                            <div class="form-group">
                                <label>@lang('site.' . $locale . '.description') <small style="color: red">*</small></label>
                                <textarea name="{{ $locale }}[description]" class="form-control ckeditor">{{ $product->description }}</textarea>
                            </div>
                        @endforeach

                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <img src="{{ $product->image_path }}" style="width: 100px" class="img-thumbnail image-preview" alt="">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.purchase_price') <small style="color: red">*</small></label>
                            <input type="number" step="0.01" min="0.01" name="purchase_price" class="form-control" value="{{ $product->purchase_price }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.sale_price') <small style="color: red">*</small></label>
                            <input type="number" step="0.01" min="0.01" name="sale_price" class="form-control" value="{{ $product->sale_price }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.stock') <small style="color: red">*</small></label>
                            <input type="number" name="stock" min="1" class="form-control" value="{{ $product->stock }}">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.edit')</button>
                        </div>

                    </form>
                </div>
            </div>

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection