<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <i class="fa-solid fa-user-tie text-emerald-600"></i> Absensi Tamu
    </h2>

    <form wire:submit.prevent="save" class="space-y-5">
        <!-- Nama Lengkap -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" wire:model="name"
                class="w-full p-3 rounded-xl border-gray-200 bg-gray-50 focus:ring-emerald-500" placeholder="Nama Tamu">
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Nama Instansi -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Instansi</label>
            <input type="text" wire:model="institution"
                class="w-full p-3 rounded-xl border-gray-200 bg-gray-50 focus:ring-emerald-500"
                placeholder="Contoh: PT. Maju Bersama">
            @error('institution') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
            <select wire:model="department_id"
                class="w-full p-3 rounded-xl border-gray-200 bg-gray-50 focus:ring-emerald-500">
                <option value="">Pilih Departemen</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            @error('department_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <!-- Tipe Makan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Makan</label>
                <!-- DITAMBAHKAN .live AGAR INTERAKTIF -->
                <select wire:model.live="meal_type"
                    class="w-full p-3 rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500">
                    <option value="">-- Pilih --</option>
                    <option value="Kantin">Kantin</option>
                    <option value="Kotakan">Kotakan</option>
                </select>
                @error('meal_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <!-- Waktu Makan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Waktu</label>
                <select wire:model.live="meal_time"
                    class="w-full p-3 rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500">
                    <option value="">-- Pilih --</option>
                    <option value="Pagi">Pagi</option>
                    <option value="Siang">Siang</option>
                    <option value="Malam">Malam</option>
                </select>
                @error('meal_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Kepuasan: Hanya muncul jika user memilih 'Kantin' -->
        @if($meal_type === 'Kantin')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Seberapa puas anda dengan makanan?</label>
            <div class="flex gap-4">
                <label class="flex-1">
                    <input type="radio" wire:model.live="satisfaction" value="Puas" class="hidden peer">
                    <div
                        class="p-3 text-center rounded-xl border-2 border-gray-100 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 text-gray-500 peer-checked:text-emerald-600 cursor-pointer transition-all">
                        <i class="fa-regular fa-face-smile text-xl"></i><br>Puas
                    </div>
                </label>
                <label class="flex-1">
                    <input type="radio" wire:model.live="satisfaction" value="Tidak Puas" class="hidden peer">
                    <div
                        class="p-3 text-center rounded-xl border-2 border-gray-100 peer-checked:border-red-500 peer-checked:bg-red-50 text-gray-500 peer-checked:text-red-600 cursor-pointer transition-all">
                        <i class="fa-regular fa-face-frown text-xl"></i><br>Tidak Puas
                    </div>
                </label>
            </div>
            @error('satisfaction') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
        </div>
        @endif

        <!-- Kritik/Saran: Hanya muncul jika memilih 'Kantin' DAN 'Tidak Puas' -->
        @if($meal_type === 'Kantin' && $satisfaction === 'Tidak Puas')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kritik & Saran (Wajib)</label>
            <textarea wire:model="feedback" rows="3" placeholder="Masukkan alasan atau kritik/saran..."
                class="w-full p-3 rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500"></textarea>
            @error('feedback') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
        </div>
        @endif

        <button type="submit"
            class="w-full bg-emerald-600 text-white font-bold py-4 rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all">
            KIRIM ABSENSI TAMU
        </button>
    </form>

    <!-- Modal Sukses -->
    @if($showSuccessModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center animate-in fade-in zoom-in duration-300">
            <div
                class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-check text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Berhasil Terkirim!</h3>
            <p class="text-gray-500 mb-8">Data absensi tamu sudah kami catat.</p>
            <button wire:click="closeModal"
                class="w-full bg-gray-900 text-white font-bold py-4 rounded-2xl hover:bg-black transition-colors">
                KEMBALI KE MENU
            </button>
        </div>
    </div>
    @endif
</div>