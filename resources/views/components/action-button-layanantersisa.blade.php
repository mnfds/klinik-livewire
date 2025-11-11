<div>
    <a wire:navigate href="{{ route('tindaklanjut.detail', ['pasien_id' => $row->id]) }}" class="btn btn-primary">
        <i class="fa-solid fa-gifts"></i>Detail
    </a>
    <a href="https://wa.me/{{ $row->no_telp }}" target="_blank" class="btn btn-success">
        <i class="fa-brands fa-whatsapp"></i>WhatsApp
    </a>
</div>