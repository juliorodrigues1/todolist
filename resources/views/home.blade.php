@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-8 col-sm-8">
                <form method="post" id="form-task">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome da tarefa</label>
                        <input type="text" class="form-control" name="name" id="name">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
                <h4 class="mt-5">Lista de tarefas</h4>
                <div id="tasks">

                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function () {

            getTasks();

            $('#form-task').submit(function (e) {
                $(this).find('div[class=invalid-feedback]').remove();
                $(this).find('input').removeClass("is-invalid");
                e.preventDefault();
                $.ajax({
                    url: '{{ route('task.store') }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (data) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'Tarefa salva com sucesso'
                        })
                        $('#form-task')[0].reset();
                        getTasks();
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

            function getTasks() {
                $.ajax({
                    url: '{{ route('task.listAll') }}',
                    type: 'GET',
                    success: function (data) {
                        let html = '';
                        for (task in data) {
                            html += `
                            <div class="form-check">
                                <div class="row d-flex align-items-center">
                                    <div class="col-4">
                                        <input class="form-check-input" type="checkbox" id="${data[task].id}" data-id="${data[task].id}" ${data[task].completed == 1 ? 'checked' : ''} >
                                            <label class="form-check-label ${data[task].completed == 1 ? 'text-decoration-line-through' : ''}" data-id="${data[task].id}" for="${data[task].id}">
                                                ${data[task].name}
                                            </label>
                                    </div>
                                    <div class="col-6">
<a class="btn btn-secondary" href="{{ route('task.edit', '')}}/${data[task].id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                              <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                              <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                            </svg>
                                        </a>
                                        <button class="btn btn-danger remove-task" data-id="${data[task].id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z"/></svg>
                                        </button>

                                    </div>
                                </div>
                            </div>`
                        }
                        $('#tasks').html(html);
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }

            $('body').on('click', '.form-check-input', function () {
                let id = $(this).data('id');
                let completed = $(this).is(':checked') ? 1 : 0
                $(this).is(':checked') ? $(`label[data-id=${id}]`).addClass('text-decoration-line-through') : $(`label[data-id=${id}]`).removeClass('text-decoration-line-through');
                $.ajax({
                    url: '{{ route('task.update', '') }}' + '/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        completed: completed
                    },
                    success: function (data) {
                        // console.log(data);
                    },
                    error: function (data) {
                        // console.log(data);
                    }
                });
            });

            $('body').on('click', '.remove-task', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: "isso não pode ser desfeito!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, deletar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('task.destroy', '') }}' + '/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (data) {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                })

                                Toast.fire({
                                    icon: 'success',
                                    title: 'Tarefa removida com sucesso'
                                })
                                getTasks();
                            },
                            error: function (data) {
                                console.log(data);
                            }
                        });
                    }
                })

            });
        });
    </script>
@endpush
