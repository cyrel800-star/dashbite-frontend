@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto" x-data="avatarEditor()">

    {{-- HEADER --}}
    <div class="mb-6">
        <a href="{{ route('profile.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to profile</a>
        <h1 class="text-2xl font-semibold text-gray-900 mt-2">Change Profile Picture</h1>
        <p class="text-sm text-gray-500 mt-1">Upload at i-crop ang iyong picture (max 5MB)</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- CURRENT PICTURE --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-4 text-center">
        <p class="text-xs uppercase tracking-wide text-gray-400 mb-4">Current Picture</p>

        <div class="inline-block relative">
            <template x-if="!avatarUrl">
                <div class="w-32 h-32 rounded-full {{ $user->avatar_color }} flex items-center justify-center text-white text-4xl font-bold border-4 border-white shadow-lg">
                    {{ $user->initials }}
                </div>
            </template>
            <template x-if="avatarUrl">
                <img :src="avatarUrl" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
            </template>

            @if ($user->isOnline())
                <span class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 border-4 border-white rounded-full"></span>
            @else
                <span class="absolute bottom-2 right-2 w-6 h-6 bg-gray-400 border-4 border-white rounded-full"></span>
            @endif
        </div>

        <p class="text-sm font-medium text-gray-900 mt-3">{{ $user->name }}</p>
        <p class="text-xs text-gray-500">{{ $user->email }}</p>
    </div>

    {{-- UPLOAD / CROP --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">

        {{-- STEP 1: CHOOSE FILE --}}
        <template x-if="!imageSrc">
            <div>
                <p class="text-sm font-medium text-gray-900 mb-4">Upload new photo</p>

                <label class="block cursor-pointer">
                    <input type="file"
                           @change="onFileChange($event)"
                           accept="image/jpeg,image/jpg,image/png,image/webp"
                           class="hidden">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-10 text-center hover:border-orange-500 hover:bg-orange-50 transition">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm text-gray-600 mb-1">
                            <span class="text-orange-600 font-medium">Click to upload</span>
                        </p>
                        <p class="text-xs text-gray-400">JPG, PNG, or WebP · Max 5MB</p>
                    </div>
                </label>
            </div>
        </template>

        {{-- STEP 2: CROP --}}
        <template x-if="imageSrc">
            <div>
                <p class="text-sm font-medium text-gray-900 mb-4">Crop your photo</p>

                <div class="bg-gray-100 rounded-lg p-4 mb-4">
                    <img x-ref="cropImage" :src="imageSrc" class="max-w-full">
                </div>

                <p class="text-xs text-gray-500 text-center mb-4">
                    I-drag at i-resize ang crop box para i-adjust ang frame
                </p>

                {{-- ACTIONS --}}
                <div class="flex gap-2">
                    <button type="button"
                            @click="reset()"
                            class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="button"
                            @click="upload()"
                            :disabled="uploading"
                            :class="uploading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-700'"
                            class="flex-1 bg-orange-600 text-white py-2 rounded-lg text-sm font-medium transition">
                        <span x-show="!uploading">Save Photo</span>
                        <span x-show="uploading">Uploading...</span>
                    </button>
                </div>

                <p class="text-xs text-red-600 text-center mt-2" x-show="error" x-text="error"></p>
            </div>
        </template>
    </div>

    {{-- REMOVE --}}
    @if ($user->avatar)
        <div class="mt-4 text-center">
            <form method="POST"
                  action="{{ route('profile.avatar.remove') }}"
                  data-confirm="Remove your profile picture?"
                  data-confirm-ok="Remove">
                @csrf
                @method('DELETE')
                <button class="text-sm text-red-600 hover:underline">
                    Remove current picture
                </button>
            </form>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.css">
<script src="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.js"></script>
<script src="//unpkg.com/alpinejs" defer></script>
<script>
function avatarEditor() {
    return {
        imageSrc: '',
        cropper: null,
        uploading: false,
        error: '',
        avatarUrl: '{{ $user->avatar_url }}',

        init() {
            // Check kung may temp image mula sa profile page
            const tempImage = sessionStorage.getItem('temp_avatar_image');

            if (tempImage) {
                this.imageSrc = tempImage;
                sessionStorage.removeItem('temp_avatar_image');

                this.$nextTick(() => this.initCropper());
            }
        },

        onFileChange(e) {
            this.error = '';
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                this.error = 'File is too large. Maximum 5MB.';
                e.target.value = '';
                return;
            }

            if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
                this.error = 'Invalid file type. Use JPG, PNG, or WebP.';
                e.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (ev) => {
                this.imageSrc = ev.target.result;
                this.$nextTick(() => this.initCropper());
            };
            reader.readAsDataURL(file);

            e.target.value = '';
        },

        initCropper() {
            if (this.cropper) this.cropper.destroy();

            this.cropper = new Cropper(this.$refs.cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                background: false,
                guides: false,
                center: false,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        },

        reset() {
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
            this.imageSrc = '';
            this.error = '';
        },

        async upload() {
            if (!this.cropper) return;

            this.uploading = true;
            this.error = '';

            try {
                const canvas = this.cropper.getCroppedCanvas({
                    width: 500,
                    height: 500,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.9));

                const formData = new FormData();
                formData.append('avatar', blob, 'avatar.jpg');

                const res = await fetch('{{ route('profile.avatar.update') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });

                const data = await res.json();

                if (!res.ok) {
                    this.error = data.message || 'Upload failed.';
                    this.uploading = false;
                    return;
                }

                window.location.href = '{{ route('profile.index') }}';
            } catch (err) {
                console.error(err);
                this.error = 'Upload failed. Please try again.';
                this.uploading = false;
            }
        }
    }
}
</script>
@endpush
