<div x-data="{ revealed: false }" class="flex items-center">
    <span x-show="!revealed" class="font-mono text-gray-700">**** **** **** ****</span>
    <span x-show="revealed" class="font-mono text-gray-700">{{ $getState() }}</span>
    <button 
        x-on:click="revealed = !revealed" 
        type="button" 
        class="ml-2 text-blue-600 hover:underline"
    >
        <span x-show="!revealed">Reveal</span>
        <span x-show="revealed">Hide</span>
    </button>
</div>
