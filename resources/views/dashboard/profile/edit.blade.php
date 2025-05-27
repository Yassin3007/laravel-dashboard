@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">Edit Profile</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Edit Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Profile Edit Card -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Profile Information</h4>
                            </div>
                            <div class="card-body collapse in">
                                <div class="card-block">
                                    @if(session('success'))
                                        <div class="alert alert-success alert-dismissible fade in" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="form">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <!-- Profile Image Section -->
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-body">
                                                    <div class="form-group text-center">
                                                        <label>Profile Picture</label>
                                                        <div class="profile-image-container mb-2">
                                                            <img id="profile-preview" src="{{ $user->image_url }}" alt="Profile Picture"
                                                                 class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ddd;">
                                                        </div>
                                                        <div class="profile-image-actions">
                                                            <input type="file" id="image-upload" name="image" accept="image/*" style="display: none;">
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('image-upload').click()">
                                                                <i class="icon-camera"></i> Change Photo
                                                            </button>
                                                            @if($user->image)
                                                                <button type="button" class="btn btn-danger btn-sm ml-1" id="delete-image-btn">
                                                                    <i class="icon-trash"></i> Remove
                                                                </button>
                                                            @endif
                                                        </div>
                                                        @error('image')
                                                        <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Profile Form Section -->
                                            <div class="col-md-8 col-sm-12">
                                                <div class="form-body">
                                                    <h4 class="form-section"><i class="icon-user"></i> Personal Information</h4>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="name">Full Name <span class="text-danger">*</span></label>
                                                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                                                       placeholder="Enter your full name" name="name" value="{{ old('name', $user->name) }}" required>
                                                                @error('name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                                                <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                                                       placeholder="Enter your email" name="email" value="{{ old('email', $user->email) }}" required>
                                                                @error('email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="phone">Phone Number</label>
                                                                <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror"
                                                                       placeholder="Enter your phone number" name="phone" value="{{ old('phone', $user->phone) }}">
                                                                @error('phone')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
{{--                                                        <div class="col-md-6">--}}
{{--                                                            <div class="form-group">--}}
{{--                                                                <label for="address">Address</label>--}}
{{--                                                                <input type="text" id="address" class="form-control @error('address') is-invalid @enderror"--}}
{{--                                                                       placeholder="Enter your address" name="address" value="{{ old('address', $user->address) }}">--}}
{{--                                                                @error('address')--}}
{{--                                                                <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                                                                @enderror--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
                                                    </div>

{{--                                                    <div class="form-group">--}}
{{--                                                        <label for="bio">Bio</label>--}}
{{--                                                        <textarea id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror"--}}
{{--                                                                  name="bio" placeholder="Tell us about yourself">{{ old('bio', $user->bio) }}</textarea>--}}
{{--                                                        @error('bio')--}}
{{--                                                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                                                        @enderror--}}
{{--                                                    </div>--}}

                                                    <h4 class="form-section"><i class="icon-lock"></i> Change Password</h4>
                                                    <p class="text-muted">Leave blank if you don't want to change your password</p>

                                                    <div class="form-group">
                                                        <label for="current_password">Current Password</label>
                                                        <input type="password" id="current_password" class="form-control @error('current_password') is-invalid @enderror"
                                                               placeholder="Enter your current password" name="current_password">
                                                        @error('current_password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="password">New Password</label>
                                                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                                                       placeholder="Enter new password" name="password">
                                                                @error('password')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="password_confirmation">Confirm New Password</label>
                                                                <input type="password" id="password_confirmation" class="form-control"
                                                                       placeholder="Confirm new password" name="password_confirmation">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-actions">
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="icon-check2"></i> Update Profile
                                                        </button>
                                                        <a href="{{ route('dashboard') }}" class="btn btn-warning mr-1">
                                                            <i class="icon-arrow-left"></i> Cancel
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            // Image preview functionality
            $('#image-upload').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profile-preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Delete image functionality
            $('#delete-image-btn').on('click', function() {
                if (confirm('Are you sure you want to remove your profile picture?')) {
                    $.ajax({
                        url: '{{ route("profile.delete-image") }}',
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#profile-preview').attr('src', '{{ asset("dashboard/app-assets/images/portrait/small/avatar-s-1.png") }}');
                                $('#delete-image-btn').hide();
                                alert(response.message);
                            }
                        },
                        error: function() {
                            alert('Error deleting image. Please try again.');
                        }
                    });
                }
            });

            // Password field validation
            $('#password, #password_confirmation').on('keyup', function() {
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                if (password !== '' && confirmPassword !== '' && password !== confirmPassword) {
                    $('#password_confirmation').addClass('is-invalid');
                } else {
                    $('#password_confirmation').removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection
