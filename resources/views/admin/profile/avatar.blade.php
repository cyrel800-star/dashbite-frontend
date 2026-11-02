@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto" x-data="avatarUploader()">

    {{-- HEADER --}}
    <div class="mb-6">
        <a href="{{ route('admin.profile.index') }}" class="text-sm text-orange-600 dark:text-orange-400 hover:underline font-medium">
            ← Back to profile
        </a>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white mt-2">Change Profile Picture</h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Upload ang iyong admin picture (max 5MB)</p>
    </div>

    {{-- ERROR ALERT --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-black dark:bg-neutral-900 border-l-4 border-orange-500 rounded-xl text-sm flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-white font-medium">{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- CURRENT PICTURE --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-6 mb-4 text-center">
        <p class="text-xs uppercase tracking-wide text-neutral-400 dark:text-neutral-500 mb-4 font-bold">Current Picture</p>

        <div class="inline-block relative">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-[#141414] shadow-lg">
            @else
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-white dark:border-[#141414] shadow-lg">
                    {{ $user->initials }}
                </div>
            @endif

            @if ($user->isOnline())
                <span class="absolute bottom-2 right-2 w-6 h-6 bg-orange-500 border-4 border-white dark:border-[#141414] rounded-full"></span>
            @endif
        </div>

        <p class="text-sm font-medium text-neutral-900 dark:text-white mt-3">{{ $user->name }}</p>
        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $user->email }}</p>
    </div>

    {{-- UPLOAD FORM — DIRECT SAVE --}}
    <form method="POST"
          action="{{ route('admin.profile.avatar.update') }}"
          enctype="multipart/form-data"
          class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-6">
        @csrf

        <p class="text-sm font-medium text-neutral-900 dark:text-white mb-4">Upload new photo</p>

        <label class="block cursor-pointer">
            <input type="file"
                   name="avatar"
                   accept="image/jpeg,image/jpg,image/png,image/webp"
                   required
                   @change="onFileChange($event)"
                   class="hidden">

            <div class="border-2 border-dashed border-neutral-300 dark:border-[#262626] rounded-xl p-10 text-center hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-950/20 transition"
                 :class="uploading ? 'opacity-50 pointer-events-none' : ''">

                {{-- UPLOADING STATE --}}
                <template x-if="uploading">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-orange-500 animate-spin mb-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="text-sm font-bold text-orange-600 dark:text-orange-400">Uploading...</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Please wait</p>
                    </div>
                </template>

                {{-- PREVIEW STATE (habang nag-upload) --}}
                <template x-if="preview && !uploading">
                    <div class="flex flex-col items-center">
                        <img :src="preview" class="w-32 h-32 rounded-full object-cover shadow-md border-4 border-white dark:border-[#141414]">
                        <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold mt-3">Saving...</p>
                    </div>
                </template>

                {{-- DEFAULT STATE --}}
                <template x-if="!preview && !uploading">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center mx-auto mb-3 shadow-md">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">
                            <span class="text-orange-600 dark:text-orange-400 font-bold">Click to upload</span> photo
                        </p>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500">JPG, PNG, or WebP · Max 5MB</p>
                    </div>
                </template>
            </div>
        </label>

        {{-- CLIENT-SIDE ERROR --}}
        <p class="text-xs text-red-600 dark:text-red-400 mt-3" x-show="error" x-text="error"></p>

        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3 text-center">
            Ang photo ay agad na mai-save pagkatapos i-upload.
        </p>
    </form>

    {{-- REMOVE --}}
    @if ($user->avatar)
        <div class="mt-4 text-center">
            <form method="POST"
                  action="{{ route('admin.profile.avatar.remove') }}"
                  data-confirm="Remove your profile picture?"
                  data-confirm-ok="Remove">
                @csrf
                @method('DELETE')
                <button class="text-sm text-red-600 dark:text-red-400 hover:underline font-medium">
                    Remove current picture
                </button>
            </form>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function avatarUploader() {
    return {
        preview: '',
        uploading: false,
        error: '',

        onFileChange(e) {
            this.error = '';
            const file = e.target.files[0];
            if (!file) return;

            // Validate size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                this.error = 'File is too large. Maximum 5MB.';
                e.target.value = '';
                return;
            }

            // Validate type
            if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
                this.error = 'Invalid file type. Use JPG, PNG, or WebP.';
                e.target.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (ev) => {
                this.preview = ev.target.result;
            };
            reader.readAsDataURL(file);

            // ⭐ AUTO-SUBMIT — diretso save pagka-upload
            this.uploading = true;

            // Small delay para makita ang preview
            setTimeout(() => {
                e.target.form.submit();
            }, 500);
        }
    }
}
</script>
@endpush
