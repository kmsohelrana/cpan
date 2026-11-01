<x-app-layout title="{{ $title }}">


    <x-header title='{{ $title }}'>
        <li class="breadcrumb-item"><a class="link-fx" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a class="link-fx" href="{{ route('profile.index') }}">Profile</a>
        </li>
        <li class="breadcrumb-item">Edit</li>
    </x-header>

    <div class="row mt-5">
        <div class="col-md-6 mx-auto">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Edit Profile</h3>
                    <div class="block-options">
                        <a href="{{ route('profile.index') }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-fw fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="block-content pb-4">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter name">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                    <input disabled type="text" value="{{ old('email', $user->email) }}" class="form-control" id="email" name="email" placeholder="Enter Email">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="image">Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept=".jpg,.jpeg,.png,.JPG,.JPEG,.PNG">
                                    @if ($user->image)
                                    <div class="hover-data">
                                        <small>
                                            Current Profile Picture:
                                            <a href="{{ $user->image }}" download> Download</a>
                                        </small>
                                        <div class="preview">
                                            <img src="{{ $user->image }}" alt="Preview" width="100" class="img-thumbnail mt-2">
                                        </div>
                                    </div>
                                    @endif
                                    @error('image')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa fa-fw fa-save me-1"></i>
                                    Update Profile
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
