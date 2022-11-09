@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-8 col-sm-8">
                <form method="post" id="form-task">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome da tarefa</label>
                        <input type="text" class="form-control" name="name" value="{{ $task->name }}" id="name">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function () {
            $('#form-task').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('task.update', $task->id) }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (data) {
                        window.location.href = '{{ route('home') }}';
                    },
                    error: function (err) {
                        $.each(err.responseJSON.errors, function (i, error) {
                            let el = $(document).find('[name="' + i + '"]');
                            el.addClass('is-invalid');
                            el.after($('<div class="invalid-feedback">' + error[0] + '</div>'));
                        });
                    }
                });
            });
        })
    </script>

@endpush
