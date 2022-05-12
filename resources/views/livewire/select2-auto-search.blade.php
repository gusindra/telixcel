<div>

    <div wire:ignore>
        <select class="form-control" id="select2">
            <option value="">Choose Song</option>
            @foreach($songs as $data)
            <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>

</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#select2').select2();
        $('#select2').on('change', function (e) {
            var item = $('#select2').select2("val");
            @this.set('viralSongs', item);
        });
    });

</script>

@endpush
