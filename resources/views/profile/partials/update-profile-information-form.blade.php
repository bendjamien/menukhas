<!-- <section>
    <form method="post" action="{{ route('profile.update') }}" class="mt-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <x-input-label for="name" :value="__('Nama')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                              :value="old('name', $user->name)" required 
                              @disabled(Auth::user()->role == 'kasir')
                              autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="username" :value="__('Username')" />
                <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" 
                              :value="old('username', $user->username)" required 
                              @disabled(Auth::user()->role == 'kasir')
                              autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('username')" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="no_hp" :value="__('No. HP')" />
                <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" 
                              :value="old('no_hp', $user->no_hp)" 
                              @disabled(Auth::user()->role == 'kasir') 
                              autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                              :value="old('email', $user->email)" required 
                              @disabled(Auth::user()->role == 'kasir') 
                              autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
        </div>

        @if(Auth::user()->role == 'admin')
            <div class="flex items-center gap-4 mt-6">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
            </div>
        @endif
    </form>
</section> -->