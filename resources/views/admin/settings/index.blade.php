<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Profil Perusahaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Identitas Perusahaan --}}
                        <div class="space-y-4">
                            <h3 class="text-sm font-black uppercase text-gray-400 border-b pb-2">Identitas Perusahaan</h3>
                            
                            <div>
                                <x-input-label for="company_name" :value="__('Nama Perusahaan')" />
                                <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="$settings['company_name']" required />
                            </div>

                            <div>
                                <x-input-label for="company_email" :value="__('Email Kontak')" />
                                <x-text-input id="company_email" name="company_email" type="email" class="mt-1 block w-full" :value="$settings['company_email']" required />
                            </div>

                            <div>
                                <x-input-label for="company_phone" :value="__('Nomor Telepon Kantor')" />
                                <x-text-input id="company_phone" name="company_phone" type="text" class="mt-1 block w-full" :value="$settings['company_phone']" placeholder="Contoh: 62211234567" required />
                            </div>

                            <div>
                                <x-input-label for="company_whatsapp" :value="__('Nomor WhatsApp')" />
                                <x-text-input id="company_whatsapp" name="company_whatsapp" type="text" class="mt-1 block w-full" :value="$settings['company_whatsapp']" placeholder="Contoh: 628123456789" required />
                            </div>

                            <div>
                                <x-input-label for="company_address" :value="__('Alamat Kantor')" />
                                <textarea name="company_address" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required>{{ $settings['company_address'] }}</textarea>
                            </div>
                        </div>

                        {{-- Informasi Pembayaran --}}
                        <div class="space-y-4">
                            <h3 class="text-sm font-black uppercase text-gray-400 border-b pb-2">Informasi Pembayaran (Invoice)</h3>
                            
                            <div>
                                <x-input-label for="bank_name" :value="__('Nama Bank')" />
                                <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" :value="$settings['bank_name']" required />
                            </div>

                            <div>
                                <x-input-label for="bank_account_number" :value="__('Nomor Rekening')" />
                                <x-text-input id="bank_account_number" name="bank_account_number" type="text" class="mt-1 block w-full" :value="$settings['bank_account_number']" required />
                            </div>

                            <div>
                                <x-input-label for="bank_account_name" :value="__('Atas Nama (A/N)')" />
                                <x-text-input id="bank_account_name" name="bank_account_name" type="text" class="mt-1 block w-full" :value="$settings['bank_account_name']" required />
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg mt-6 border-l-4 border-blue-500">
                                <p class="text-[10px] text-gray-500 italic uppercase">Keterangan:</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Data ini akan otomatis muncul pada setiap <strong>Invoice (PDF)</strong> dan <strong>Surat Konfirmasi</strong> yang diunduh oleh Customer.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t dark:border-gray-700 flex justify-end">
                        <x-primary-button class="px-8 py-3">Simpan Seluruh Pengaturan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
