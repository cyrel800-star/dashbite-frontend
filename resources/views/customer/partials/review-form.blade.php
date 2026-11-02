{{-- resources/views/customer/partials/review-form.blade.php --}}
@if ($order->status === 'delivered' && !$order->hasBeenReviewed())
    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-950/20 dark:to-orange-950/20 rounded-2xl border-2 border-orange-200 dark:border-orange-800 p-6 mt-6">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center flex-shrink-0 shadow-lg">
                <span class="text-2xl">⭐</span>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100">
                    Rate your experience
                </h3>
                <p class="text-sm text-gray-600 dark:text-neutral-400">
                    Tulungan mo ang ibang customers sa review mo para sa
                    <strong>{{ $order->restaurant->name }}</strong>.
                </p>
            </div>
        </div>

        <button type="button"
                onclick="document.getElementById('review-modal').classList.remove('hidden')"
                class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg transition">
            ⭐ Write a Review
        </button>
    </div>

    {{-- Review Modal --}}
    <div id="review-modal"
         x-data="reviewForm({{ $order->id }})"
         class="hidden fixed inset-0 z-[150] items-center justify-center p-4 bg-black/60">
        
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6"
             @click.outside="document.getElementById('review-modal').classList.add('hidden')">

            <div class="flex justify-between items-start mb-5">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100">Rate your experience</h2>
                    <p class="text-sm text-gray-500 dark:text-neutral-400">
                        {{ $order->restaurant->name }}
                    </p>
                </div>
                <button type="button"
                        @click="document.getElementById('review-modal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submit()" class="space-y-4">

                {{-- Star Rating --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-neutral-100 mb-2">
                        Overall Rating *
                    </label>
                    <div class="flex gap-2">
                        <template x-for="i in 5" :key="i">
                            <button type="button"
                                    @click="rating = i"
                                    @mouseenter="hoverRating = i"
                                    @mouseleave="hoverRating = 0"
                                    class="transition-transform hover:scale-110">
                                <svg class="w-10 h-10"
                                     :class="i <= (hoverRating || rating) 
                                        ? 'text-yellow-400 fill-yellow-400' 
                                        : 'text-gray-300 dark:text-neutral-600'"
                                     viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        </template>
                    </div>
                    <p x-show="rating > 0"
                       x-text="['', 'Terrible 😞', 'Poor 😕', 'Average 😐', 'Good 😊', 'Excellent 🤩'][rating]"
                       class="text-sm font-medium text-gray-600 dark:text-neutral-400 mt-2"></p>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-neutral-100 mb-2">
                        Title <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input type="text"
                           x-model="title"
                           maxlength="100"
                           placeholder="e.g. Best pizza in CDO!"
                           class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                {{-- Body --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-neutral-100 mb-2">
                        Your Review *
                    </label>
                    <textarea x-model="body"
                              required
                              minlength="10"
                              maxlength="2000"
                              rows="5"
                              placeholder="Ano ang experience mo? Naging maayos ba ang pagkain at service?"
                              class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>
                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                        <span x-text="body.length"></span>/2000 characters (min 10)
                    </p>
                </div>

                {{-- Photos --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-neutral-100 mb-2">
                        Add Photos <span class="text-gray-400 font-normal">(optional, max 5)</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(preview, i) in photoPreviews" :key="i">
                            <div class="relative w-20 h-20">
                                <img :src="preview" class="w-full h-full object-cover rounded-xl">
                                <button type="button"
                                        @click="removePhoto(i)"
                                        class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 transition">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <button type="button"
                                x-show="photoPreviews.length < 5"
                                @click="$refs.photoInput.click()"
                                class="w-20 h-20 border-2 border-dashed border-gray-300 dark:border-dark-600 rounded-xl flex flex-col items-center justify-center text-gray-400 dark:text-neutral-500 hover:border-orange-500 hover:text-orange-500 dark:hover:border-orange-500 transition">
                            <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-xs">Add</span>
                        </button>

                        <input type="file"
                               x-ref="photoInput"
                               @change="onPhotoChange($event)"
                               accept="image/jpeg,image/jpg,image/png,image/webp"
                               multiple
                               class="hidden">
                    </div>
                </div>

                {{-- Info --}}
                <div class="bg-blue-50 dark:bg-blue-950/30 rounded-xl p-3 flex gap-2">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-blue-800 dark:text-blue-300 leading-relaxed">
                        Ang review mo ay makikita ng lahat. I-base ito sa tunay na experience mo.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 pt-2">
                    <button type="button"
                            @click="document.getElementById('review-modal').classList.add('hidden')"
                            class="flex-1 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 py-3 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-dark-850 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            :disabled="submitting || rating === 0 || body.length < 10"
                            :class="(submitting || rating === 0 || body.length < 10) 
                                ? 'opacity-40 cursor-not-allowed' 
                                : 'hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98'"
                            class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl font-bold shadow-md transition transform">
                        <span x-show="!submitting">Submit Review</span>
                        <span x-show="submitting">Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function reviewForm(orderId) {
        return {
            rating: 0,
            hoverRating: 0,
            title: '',
            body: '',
            photos: [],
            photoPreviews: [],
            submitting: false,

            onPhotoChange(e) {
                const files = Array.from(e.target.files);
                files.forEach(file => {
                    if (this.photos.length >= 5) return;

                    if (file.size > 5 * 1024 * 1024) {
                        alert('File too large (max 5MB): ' + file.name);
                        return;
                    }

                    this.photos.push(file);

                    const reader = new FileReader();
                    reader.onload = (ev) => this.photoPreviews.push(ev.target.result);
                    reader.readAsDataURL(file);
                });
                e.target.value = '';
            },

            removePhoto(i) {
                this.photos.splice(i, 1);
                this.photoPreviews.splice(i, 1);
            },

            async submit() {
                if (this.rating === 0) {
                    alert('Please select a rating.');
                    return;
                }
                if (this.body.length < 10) {
                    alert('Please write at least 10 characters.');
                    return;
                }

                this.submitting = true;

                const formData = new FormData();
                formData.append('rating', this.rating);
                formData.append('title', this.title || '');
                formData.append('body', this.body);
                this.photos.forEach(photo => formData.append('images[]', photo));

                try {
                    const res = await fetch(`/orders/${orderId}/review`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData,
                    });

                    if (res.ok) {
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || 'Could not submit review.');
                        this.submitting = false;
                    }
                } catch (err) {
                    console.error(err);
                    alert('Network error. Please try again.');
                    this.submitting = false;
                }
            }
        }
    }
    </script>
    @endpush
@endif